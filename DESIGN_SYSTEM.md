# Eclipz Media & Marketing — Design System (Phase 1 lock-in)

This is a locked requirement, tracked alongside the database schema and
routing — not a "polish later" item.

## Brand
- **Company:** Eclipz Media & Marketing Limited
- **Tagline:** Crafting Timeless Visual Stories
- **Personality:** Premium, Cinematic, Modern, Minimal, Creative, Professional

## Color tokens (`tailwind.config.js`)
| Token             | Hex       | Role                                                |
|--------------------|-----------|------------------------------------------------------|
| `white`            | `#FFFFFF` | Primary foundation                                   |
| `jet`               | `#0A0A0A` | Primary foundation                                   |
| `electric-blue`     | `#007BFF` | Restrained accent — links, active states, highlights |
| `warm-gold`         | `#D4A017` | Restrained accent — brand moments, selected elements |

**Rule:** black + white carry the page. Blue and gold are accents, not fills —
if a component uses either as more than ~10% of its surface, that's a sign
it's drifting from the brand direction in §3 of the brief.

## Typography
- **Display (`font-display`):** Bebas Neue — hero statements, section titles,
  big cinematic moments. Used sparingly, not on every heading.
- **Body/UI (`font-body`):** Archivo — an honest, freely-licensed substitute
  for Akzidenz-Grotesk (which isn't available for web embedding). This is a
  stand-in, not a claim that it's the original typeface. If a licensed
  Akzidenz-Grotesk web font is acquired later, swap it in
  `tailwind.config.js` and `resources/css/app.css` — nothing else changes.

## Scope
- **Public site:** fully themed with these tokens — this is where the brand lives.
- **Admin panel (`/admin`):** intentionally excluded from this theme (see
  `tailwind.config.js` content paths). Filament keeps its own default styling.
  Operational clarity, not brand expression, is the goal there.

## Avoid (per brief §3)
Excessive rounded cards, gradients, glassmorphism, decorative blobs, emoji
icons, stock imagery presented as Eclipz work, motion for its own sake.
