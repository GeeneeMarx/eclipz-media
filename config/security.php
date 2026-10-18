<?php

return [

    // Hosts the site may be served from, comma separated, for example
    // "eclipzmedia.com,www.eclipzmedia.com". Requests with any other Host header are
    // rejected outside local development and tests. The host in APP_URL, and its
    // subdomains, are always trusted as well.
    'trusted_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('TRUSTED_HOSTS', ''))))),

    // Sends the Strict-Transport-Security header on HTTPS responses. Turn this on
    // only when the site is permanently served over HTTPS.
    'hsts' => (bool) env('SECURITY_HSTS', false),

];