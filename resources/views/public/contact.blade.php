<x-layouts.public title="Contact Us | Eclipz Media & Marketing Limited">
    <section class="max-w-5xl mx-auto px-6 lg:px-10 pt-40 pb-28">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Send an Enquiry</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">Tell us a little about what you need and the Eclipz team will review your enquiry.</p>

        <form
            method="POST"
            action="{{ route('contact.store') }}"
            x-data="{ submitting: false }"
            @submit="submitting = true"
            @pageshow.window="submitting = false"
            class="mt-12 grid md:grid-cols-2 gap-6 font-body"
        >
            @csrf
            <input type="hidden" name="form_token" value="{{ $formToken }}">

            {{-- Honeypot: hidden from people, visible to bots. Must stay empty. --}}
            <div style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
                <label for="hp_check">Leave this field empty</label>
                <input type="text" id="hp_check" name="hp_check" tabindex="-1" autocomplete="off" data-lpignore="true" data-1p-ignore="true">
            </div>

            @if($errors->has('form'))
                <div role="alert" class="md:col-span-2 border border-red-600/40 bg-red-50 text-red-700 px-6 py-4">
                    {{ $errors->first('form') }}
                </div>
            @endif

            <div>
                <label for="name" class="text-xs uppercase tracking-wider text-jet/50">Full name <span class="text-red-600" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                    class="w-full border-b border-jet/20 py-2 focus:border-electric-blue outline-none">
                @error('name')<p id="name-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="company" class="text-xs uppercase tracking-wider text-jet/50">Company</label>
                <input type="text" id="company" name="company" value="{{ old('company') }}" maxlength="120" autocomplete="organization"
                    @error('company') aria-invalid="true" aria-describedby="company-error" @enderror
                    class="w-full border-b border-jet/20 py-2 focus:border-electric-blue outline-none">
                @error('company')<p id="company-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="text-xs uppercase tracking-wider text-jet/50">Email <span class="text-red-600" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    class="w-full border-b border-jet/20 py-2 focus:border-electric-blue outline-none">
                @error('email')<p id="email-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="text-xs uppercase tracking-wider text-jet/50">Phone / WhatsApp</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" maxlength="25" autocomplete="tel"
                    @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                    class="w-full border-b border-jet/20 py-2 focus:border-electric-blue outline-none">
                @error('phone')<p id="phone-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="service_requested" class="text-xs uppercase tracking-wider text-jet/50">Service of interest <span class="text-red-600" aria-hidden="true">*</span></label>
                <select id="service_requested" name="service_requested" required
                    @error('service_requested') aria-invalid="true" aria-describedby="service-error" @enderror
                    class="w-full border-b border-jet/20 py-2 bg-transparent focus:border-electric-blue outline-none">
                    <option value="">Choose a service</option>
                    @foreach($serviceOptions as $option)
                        <option value="{{ $option }}" @selected(old('service_requested') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error('service_requested')<p id="service-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="budget" class="text-xs uppercase tracking-wider text-jet/50">Estimated budget (optional)</label>
                <select id="budget" name="budget"
                    @error('budget') aria-invalid="true" aria-describedby="budget-error" @enderror
                    class="w-full border-b border-jet/20 py-2 bg-transparent focus:border-electric-blue outline-none">
                    <option value="">Prefer not to say</option>
                    @foreach($budgetOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('budget') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('budget')<p id="budget-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="message" class="text-xs uppercase tracking-wider text-jet/50">About your project <span class="text-red-600" aria-hidden="true">*</span></label>
                <textarea id="message" name="message" rows="5" required minlength="10" maxlength="3000"
                    aria-describedby="message-help{{ $errors->has('message') ? ' message-error' : '' }}"
                    @error('message') aria-invalid="true" @enderror
                    class="w-full border-b border-jet/20 py-2 focus:border-electric-blue outline-none">{{ old('message') }}</textarea>
                <p id="message-help" class="text-xs text-jet/40 mt-1">Please do not attach files. You can send any relevant files once we have replied.</p>
                @error('message')<p id="message-error" class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button
                type="submit"
                :disabled="submitting"
                :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                class="md:col-span-2 justify-self-start bg-jet text-white px-8 py-4 hover:bg-electric-blue transition-colors"
            >
                <span x-show="!submitting">Send enquiry</span>
                <span x-show="submitting" x-cloak>Sending...</span>
            </button>
        </form>

        @php
            $hasChannels = $contact->whatsappUrl() || collect(\App\Models\ContactSetting::SOCIAL_FIELDS)->contains(fn ($field) => $contact->socialUrl($field));
        @endphp
        @if(filled($contact->email) || filled($contact->phone) || $hasChannels)
            <div class="mt-16 pt-8 border-t border-jet/10 font-body text-sm text-jet/60 flex flex-col gap-6">
                <x-contact-details :contact="$contact" tone="light" />
                <x-contact-channels :contact="$contact" tone="light" />
            </div>
        @endif
    </section>
</x-layouts.public>