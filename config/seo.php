<?php

return [

    // The address search engines should treat as "the site" in the sitemap, robots.txt
    // and canonical links. In production this is always https://eclipzmedia.com.
    'production_url' => 'https://eclipzmedia.com',

    // Optional override, set with SITE_URL in .env:
    //   Local development:  SITE_URL=http://127.0.0.1:8000
    //   Production:         leave it out, or SITE_URL=https://eclipzmedia.com
    // When it is empty, local development and tests use APP_URL, and every other
    // environment uses the production address above. A local address (127.0.0.1,
    // localhost, *.test) is never published while the site is served from a real
    // domain, whatever this says.
    'canonical_url' => env('SITE_URL'),

];