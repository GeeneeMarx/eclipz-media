<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use App\Models\ServiceCategory;
use App\Services\InquiryNotifier;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    // Neutral name on purpose: a field called "website" or "url" can be
    // autofilled by browsers and would silently discard a real enquiry.
    private const HONEYPOT_FIELD = 'hp_check';

    public function index(): View
    {
        return view('public.contact', [
            'contact' => ContactSetting::current(),
            'serviceOptions' => $this->serviceOptions(),
            'budgetOptions' => config('inquiry.budget_options'),
            'formToken' => (string) Str::uuid(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->isRateLimited($request)) {
            return back()->withInput()->withErrors([
                'form' => 'Too many attempts. Please wait a few minutes and try again.',
            ]);
        }

        // Honeypot: real visitors never see or fill this field.
        if (filled($request->input(self::HONEYPOT_FIELD))) {
            return redirect()->route('contact');
        }

        // The form has no file field. Reject anything that arrives with files.
        if ($request->allFiles() !== []) {
            return back()->withInput()->withErrors([
                'form' => 'Attachments cannot be sent through this form. Once we reply to your enquiry you can send any files directly.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+()\-\s]{7,25}$/'],
            'service_requested' => ['required', 'string', Rule::in($this->serviceOptions())],
            'budget' => ['nullable', 'string', Rule::in(array_keys(config('inquiry.budget_options')))],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'form_token' => ['required', 'uuid'],
        ], [
            'phone.regex' => 'Please enter a valid phone or WhatsApp number.',
            'service_requested.in' => 'Please choose one of the listed services.',
            'budget.in' => 'Please choose one of the listed budget options.',
            'message.min' => 'Please tell us a little more about your project (at least 10 characters).',
        ]);

        $token = $validated['form_token'];

        // Same form submitted twice (double click, back button): reuse the first record.
        $existing = Inquiry::where('submission_token', $token)->first();

        if ($existing) {
            return $this->redirectToReceived($existing->reference);
        }

        try {
            $inquiry = Inquiry::createFromSubmission(Arr::except($validated, ['form_token']), $token);
        } catch (UniqueConstraintViolationException $e) {
            // Two identical submissions arrived at the same moment.
            $existing = Inquiry::where('submission_token', $token)->first();

            if ($existing) {
                return $this->redirectToReceived($existing->reference);
            }

            return $this->failedToSave($e);
        } catch (Throwable $e) {
            return $this->failedToSave($e);
        }

        Log::info('Inquiry received', ['reference' => $inquiry->reference]);

        // The enquiry is saved. Emails go out after the response is sent, each
        // outcome is recorded on the enquiry, and no mail problem can undo it.
        app()->terminating(fn () => app(InquiryNotifier::class)->notifyNewInquiry($inquiry));

        return $this->redirectToReceived($inquiry->reference);
    }

    /**
     * Confirmation page. The ID comes from the visitor's own session, never
     * from the URL, so nobody can look up another visitor's enquiry.
     */
    public function received(): View|RedirectResponse
    {
        $reference = session('inquiry_reference');

        if (! is_string($reference) || $reference === '') {
            return redirect()->route('contact');
        }

        return view('public.contact-received', [
            'reference' => $reference,
            'whatsappUrl' => ContactSetting::current()->whatsappUrl(Inquiry::followUpMessage($reference)),
        ]);
    }

    private function serviceOptions(): array
    {
        $names = ServiceCategory::query()
            ->visible()
            ->whereHas('services', fn ($query) => $query->visible())
            ->orderBy('sort_order')
            ->pluck('name')
            ->all();

        $names[] = config('inquiry.other_service_label');

        return $names;
    }

    private function isRateLimited(Request $request): bool
    {
        $ip = (string) $request->ip();
        $minuteKey = "inquiry-form:minute:{$ip}";
        $hourKey = "inquiry-form:hour:{$ip}";

        if (
            RateLimiter::tooManyAttempts($minuteKey, (int) config('inquiry.rate_limits.per_minute'))
            || RateLimiter::tooManyAttempts($hourKey, (int) config('inquiry.rate_limits.per_hour'))
        ) {
            return true;
        }

        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($hourKey, 3600);

        return false;
    }

    private function redirectToReceived(string $reference): RedirectResponse
    {
        session(['inquiry_reference' => $reference]);

        return redirect()->route('contact.received');
    }

    private function failedToSave(Throwable $e): RedirectResponse
    {
        // Log the error type only. The full message can contain the visitor's details.
        Log::error('Inquiry could not be saved', [
            'exception' => get_class($e),
            'code' => $e->getCode(),
        ]);

        return back()->withInput()->withErrors([
            'form' => 'Sorry, we could not save your enquiry just now. Please try again in a moment.',
        ]);
    }
}