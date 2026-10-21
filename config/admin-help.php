<?php

/**
 * Help text for the admin panel. Change the wording here and nothing else needs editing.
 *
 * pages   : a short line under the page heading plus an info icon with the full explanation
 *           (hover on desktop, tap on a phone). Keyed by the page's route name without the
 *           "filament.admin." prefix. A key ending in ".*" covers every page of that kind.
 * fields  : the same two levels for each editable field, keyed by the resource or page class
 *           name and then the field name. "short" is the line under the field and "full" is
 *           the info-icon explanation.
 * actions : the tooltip on buttons, keyed by the button's internal name.
 * columns : the tooltip on table columns, keyed by the column name.
 *
 * Keep each "full" text starting with at least 13 plain letters before the first comma or
 * symbol (the tests rely on it), and avoid apostrophes inside the single-quoted strings.
 */

$projectForm = [
    'short' => 'Fill in what you know and leave out anything you cannot prove.',
    'full' => 'Only publish real results that Eclipz can stand behind: metrics appear on the public page exactly as typed. Keep the project a draft until the images and text are ready, then switch Published on. Switch Featured on to show it on the home page, and choose Case study for projects with a full story (challenge, strategy, execution and results).',
];

$blogPages = [
    'short' => 'Write and publish articles for the blog.',
    'full' => 'Save as a draft while you write, use Preview to see exactly how visitors will see it, then switch Published on. Posts appear on the Blog page, in the home page Insights section and in the sitemap once they are live. Each post has its own web address, so avoid changing it after the link has been shared.',
];

return [

    'pages' => [
        'pages.dashboard' => [
            'short' => 'A live overview of enquiries and projects, with shortcuts to common jobs.',
            'full' => 'Every number is counted from your real data each time this page opens. New means an enquiry nobody has reviewed yet. Email problems counts enquiries where an automatic email could not be sent, and the Fix email problems shortcut opens them so you can resend. Quick actions only list places you are allowed to open.',
        ],
        'pages.contact-settings-page' => [
            'short' => 'The phone, email and social links shown across the website.',
            'full' => 'What you enter here appears in the footer and on the Contact Us page, and each social link is shown as an icon. A box left empty is simply not shown. The inquiry notification email is private: it only receives the alert for each new enquiry and is never shown to visitors.',
        ],
        'pages.company-settings-page' => [
            'short' => 'The company name, tagline, mission, vision and About page text.',
            'full' => 'These words feed the home page and the About page, so you can change them here instead of in code. Keep the tagline short, write the mission and vision in plain language, and save to publish the change straight away.',
        ],
        'pages.seo-settings-page' => [
            'short' => 'How Eclipz appears in Google results and when links are shared.',
            'full' => 'These are fallbacks: any page or blog post that has its own title, summary or cover image uses that instead. Fill in the default title, a default description of 120 to 160 characters and a default share image of 1200 by 630 pixels. Changes can take days to show in Google.',
        ],
        'pages.activity-log-page' => [
            'short' => 'A read-only record of who changed what, and when.',
            'full' => 'Each change made in this admin panel is recorded here with the person, the time and the item. Entries cannot be edited or deleted, so use this page to check what happened.',
        ],
        'auth.profile' => [
            'short' => 'Your own name, email and password.',
            'full' => 'Change your sign-in details here. A new password needs at least 12 characters with upper and lower case letters and a number. Use a password you do not use anywhere else.',
        ],

        'resources.inquiries.index' => [
            'short' => 'Every enquiry sent from the website contact form.',
            'full' => 'Each enquiry has an Inquiry ID such as EZ-2026-ABC234, which the visitor sees on screen and in their email. Search finds an enquiry by its ID, and the filters show one stage at a time. The email column shows whether the confirmation and alert emails were delivered, and Resend retries a failed one. Open an enquiry to change its status.',
        ],
        'resources.inquiries.edit' => [
            'short' => 'Review the enquiry and move it through its stages.',
            'full' => 'Changing the status to Under review, Responded or Closed automatically emails the visitor a short update, once per stage and only when moving forward. Moving back to New never sends an email, and internal edits never email anyone. The Inquiry ID at the top has a Copy button.',
        ],

        'resources.projects.index' => [
            'short' => 'Your portfolio: every project and case study.',
            'full' => 'Published projects appear on the Work page and in the sitemap. Featured projects also appear in Selected Work on the home page. Drafts are visible only here. The copy-link action gives you the public address of a project to share.',
        ],
        'resources.projects.create' => $projectForm,
        'resources.projects.edit' => $projectForm,

        'resources.client-partners.*' => [
            'short' => 'The clients and partners shown on the website.',
            'full' => 'Clients are businesses you have worked for, and partners are organizations you collaborate with. Only rows switched to Visible appear publicly, so keep Visible off until a real logo is uploaded. Drag the rows in the list to change the order.',
        ],
        'resources.testimonials.*' => [
            'short' => 'Words from clients, shown on the home page once published.',
            'full' => 'Only published testimonials appear, and the home page shows up to three. Use real quotes with permission from the client, and never invent or reword what a client said.',
        ],
        'resources.service-categories.*' => [
            'short' => 'Groups for your services, shown on the Services page.',
            'full' => 'A category appears on the website only when it is visible and contains at least one visible service. Use the order number to arrange categories, because lower numbers come first.',
        ],
        'resources.services.*' => [
            'short' => 'Individual services listed under a category.',
            'full' => 'Each service belongs to a category and appears under it on the Services page. Switch Visible off to hide a service without deleting it. The home page Capabilities section shows the first three services of each category.',
        ],
        'resources.process-steps.*' => [
            'short' => 'The steps shown in How We Work.',
            'full' => 'These steps appear as a numbered overview on the home page (titles only) and as the full list with descriptions on the Services page. Hidden steps do not appear anywhere, and lower order numbers come first.',
        ],
        'resources.media-library-items.*' => [
            'short' => 'Upload images and files you want to reuse.',
            'full' => 'Allowed files are JPG, PNG, WebP, PDF, MP4 and WebM, up to 5 MB each. SVG and script files are rejected for security. Give each file a clear label so you can find it later.',
        ],

        'resources.blog-posts.index' => $blogPages,
        'resources.blog-posts.create' => $blogPages,
        'resources.blog-posts.edit' => $blogPages,
    ],

    'fields' => [

        'PostResource' => [
            'title' => [
                'short' => 'The headline readers see.',
                'full' => 'Write a clear headline of about 60 characters or fewer. It appears on the blog page, at the top of the article, in the browser tab and in Google results. The web address is suggested from it.',
            ],
            'slug' => [
                'short' => 'The web address of this post.',
                'full' => 'Use lowercase letters, numbers and hyphens only, for example how-we-shot-a-campaign. It becomes eclipzmedia.com/blog/your-address. Set it before publishing, because changing it later breaks any link that was already shared.',
            ],
            'excerpt' => [
                'short' => 'A one or two sentence summary.',
                'full' => 'Shown on the blog page cards, the home page and in Google and social previews. Aim for 120 to 160 characters (the maximum is 300). If you leave it empty, the start of the article is used instead.',
            ],
            'body' => [
                'short' => 'The article text.',
                'full' => 'Use Heading 2 and Heading 3 for sections, lists for steps and the link button to point readers elsewhere. You can paste from Word or a web page, and unsafe formatting is removed automatically. Save as a draft first and use Preview to check how it looks.',
            ],
            'user_id' => [
                'short' => 'Who the article is credited to.',
                'full' => 'Shown as the author on the post. It defaults to you, and only admin accounts appear in this list.',
            ],
            'category' => [
                'short' => 'An optional topic label.',
                'full' => 'Used for the filter buttons on the blog page, which only appear once two or more categories have live posts. Pick an existing category so the spelling stays consistent.',
            ],
            'is_published' => [
                'short' => 'Off means a draft. On means visitors can read it.',
                'full' => 'A post stays a draft while this is off, and only you can see it through Preview. Turning it on publishes it at the publish date (now, if no date is set). To take a post down later, turn it off and it disappears from the blog, the home page and the sitemap at once.',
            ],
            'published_at' => [
                'short' => 'When the post goes live.',
                'full' => 'Leave empty to publish as soon as Published is on. Set a future date and time (Lagos time) to schedule it, and it stays hidden until then. Posts are listed newest first by this date.',
            ],
            'cover_image' => [
                'short' => 'The picture at the top and in link previews.',
                'full' => 'Upload a landscape picture about 1600 px wide as JPG, PNG or WebP, up to 2 MB. Keep anything important away from the edges because it is cropped to different shapes. This is also the picture shown when the link is shared.',
            ],
            'cover_alt' => [
                'short' => 'Describes the picture for screen readers.',
                'full' => 'Write one short sentence saying what is in the picture, for visitors who cannot see it and for search engines. If you leave it empty, the post title is used.',
            ],
        ],

        'ClientPartnerResource' => [
            'name' => [
                'short' => 'The organization name as it should appear.',
                'full' => 'Use the exact business name the client or partner uses publicly.',
            ],
            'type' => [
                'short' => 'Client or partner.',
                'full' => 'Choose Client for a business you have worked for, and Partner for an organization you collaborate with.',
            ],
            'industry_id' => [
                'short' => 'Optional. Their sector.',
                'full' => 'Pick the sector this organization works in. It is optional and only helps you organize the list.',
            ],
            'website' => [
                'short' => 'Optional link to their website.',
                'full' => 'Enter the full address starting with https://. Leave it empty if they have no website.',
            ],
            'logo' => [
                'short' => 'Their real logo as PNG, JPG or WebP.',
                'full' => 'Upload the official logo, not a screenshot or a recreation. SVG files are not accepted. The website fits it into a consistent area without cropping or stretching it.',
            ],
            'display_scale' => [
                'short' => 'Leave at 1.00 unless this logo looks too small or too large.',
                'full' => 'Only adjust this if the logo looks visually smaller or larger than the others on the live site. It never changes the uploaded file.',
            ],
            'is_visible' => [
                'short' => 'Off keeps it hidden from the website.',
                'full' => 'Keep this off until a real logo is uploaded, so a placeholder is never shown as a client brand.',
            ],
            'sort_order' => [
                'short' => 'Lower numbers appear first.',
                'full' => 'You can also drag the rows in the list to reorder them, which updates this number for you.',
            ],
        ],

        'ContactSettingsPage' => [
            'phone' => [
                'short' => 'Shown as a tap-to-call link.',
                'full' => 'Enter the number visitors should call, with the country code, for example +234 801 234 5678.',
            ],
            'whatsapp' => [
                'short' => 'International format with no leading zero.',
                'full' => 'Use the country code first, for example +2348012345678. A number that starts with 0 is treated as not set, so the WhatsApp icon stays hidden instead of showing a broken link.',
            ],
            'email' => [
                'short' => 'The public email address shown on the website.',
                'full' => 'This is shown in the footer and on the Contact Us page, and visitors can click it to write to you.',
            ],
            'inquiry_notification_email' => [
                'short' => 'Private: where new enquiry alerts are sent.',
                'full' => 'This address is never shown to visitors. Every new enquiry sends an alert here, so use an inbox that someone checks every day.',
            ],
            'address' => [
                'short' => 'Optional office or studio address.',
                'full' => 'Shown in the footer as plain text. Leave it empty to hide it.',
            ],
            'instagram' => [
                'short' => 'Full profile link starting with https://.',
                'full' => 'Paste the full address of the profile page, for example https://instagram.com/yourname. Leave it empty to hide the icon. A handle such as @yourname on its own is not accepted.',
            ],
            'facebook' => [
                'short' => 'Full page link starting with https://.',
                'full' => 'Paste the full address of the page, for example https://facebook.com/yourpage. Leave it empty to hide the icon.',
            ],
            'linkedin' => [
                'short' => 'Full company link starting with https://.',
                'full' => 'Paste the full address of the company page, for example https://linkedin.com/company/yourcompany. Leave it empty to hide the icon.',
            ],
            'tiktok' => [
                'short' => 'Full profile link starting with https://.',
                'full' => 'Paste the full address of the profile, for example https://tiktok.com/@yourname. Leave it empty to hide the icon.',
            ],
        ],

        'SeoSettingsPage' => [
            'default_meta_title' => [
                'short' => 'The fallback page title for Google and browser tabs.',
                'full' => 'Used only by pages that do not set their own title, so most pages ignore it. Keep it under 60 characters and put the brand first, for example Eclipz Media & Marketing | Creative Media in Lagos.',
            ],
            'default_meta_description' => [
                'short' => 'The fallback summary shown under the title in Google.',
                'full' => 'Used by pages that do not write their own summary. Write one or two plain sentences of 120 to 160 characters saying what Eclipz does and where. Do not stuff it with keywords.',
            ],
            'default_og_image' => [
                'short' => 'The fallback picture shown when a link is shared.',
                'full' => 'Use a picture that is 1200 by 630 pixels as JPG, PNG or WebP, ideally under 1 MB. Keep the important part in the middle because apps crop it, and avoid tiny text.',
            ],
        ],
    ],

    'actions' => [
        'edit' => 'Open this item to change it.',
        'delete' => 'Permanently delete this item. This cannot be undone.',
        'save' => 'Save your changes.',
        'cancel' => 'Go back without saving.',
        'createAnother' => 'Save this item and start another.',
        'preview' => 'Open the post as visitors will see it, even while it is a draft.',
    ],

    'columns' => [
        'is_featured' => 'A tick means this is shown on the home page.',
        'is_published' => 'A tick means visitors can see it.',
        'is_visible' => 'A tick means it is shown on the website.',
        'sort_order' => 'Lower numbers are shown first.',
    ],
];