<?php

namespace App\Enums;

enum ProjectType: string
{
    case Visual = 'visual';
    case CaseStudy = 'case_study';
    case Digital = 'digital';

    /**
     * Stored value never changes (see migration/taxonomy lock) - only this
     * display wording changed, to read more editorially on the public site.
     */
    public function label(): string
    {
        return match ($this) {
            self::Visual => 'Campaign / Visual Production',
            self::CaseStudy => 'Case Study',
            self::Digital => 'Digital Experience',
        };
    }
}