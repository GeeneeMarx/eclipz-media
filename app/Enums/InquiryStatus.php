<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case UnderReview = 'under_review';
    case Responded = 'responded';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::UnderReview => 'Under Review',
            self::Responded => 'Responded',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::UnderReview => 'warning',
            self::Responded => 'info',
            self::Closed => 'success',
        };
    }

    /**
     * Position in the lifecycle. A visitor is emailed only when an enquiry
     * moves forward to a stage they have not yet been told about.
     */
    public function rank(): int
    {
        return match ($this) {
            self::New => 0,
            self::UnderReview => 1,
            self::Responded => 2,
            self::Closed => 3,
        };
    }

    /** New is the starting point. The submission confirmation already covers it. */
    public function notifiesVisitor(): bool
    {
        return $this !== self::New;
    }
}