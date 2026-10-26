<?php

namespace Tests\Feature;

use App\Support\Seo;
use Tests\TestCase;

class SeoFallbackTest extends TestCase
{
    public function test_the_production_address_survives_a_missing_configuration_value(): void
    {
        config(['seo.production_url' => null, 'seo.canonical_url' => null]);
        app()->detectEnvironment(fn () => 'production');

        $this->assertSame('https://eclipzmedia.com', Seo::baseUrl());
        $this->assertSame('https://eclipzmedia.com/work', Seo::route('work.index'));
    }
}