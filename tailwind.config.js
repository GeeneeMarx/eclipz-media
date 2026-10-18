import defaultTheme from 'tailwindcss/defaultTheme';

/**
 * Eclipz Media & Marketing — design tokens.
 *
 * Color usage rule (do not violate when building views/components):
 * - jet + white are the primary visual foundation (backgrounds, text, structure)
 * - electric blue + warm gold are RESTRAINED accents only — interaction states,
 *   selected elements, highlights, brand moments. Never used as large fill
 *   colors or treated as equal partners to black/white.
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Filament admin views are intentionally excluded from brand theming —
        // the admin panel keeps its own default Filament look and feel.
    ],
    theme: {
        extend: {
            colors: {
                white: '#FFFFFF',
                'electric-blue': '#007BFF',
                jet: '#0A0A0A',
                'warm-gold': '#D4A017',
            },
            fontFamily: {
                // Display/title font — used sparingly for hero statements, section
                // titles, and big cinematic moments. Not every heading.
                display: ['"Bebas Neue"', ...defaultTheme.fontFamily.sans],
                // Body/UI font — Akzidenz-Grotesk is not freely licensable for web
                // embedding, so Archivo (a grotesque sans with a comparable
                // structure/proportions) is used as the honest substitute. This is
                // a stand-in, not a claim that it is Akzidenz-Grotesk.
                body: ['Archivo', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
