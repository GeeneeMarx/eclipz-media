<?php

/**
 * More admin help text, in the same format as config/admin-help.php: the short line under a
 * field, the longer info-icon text, and tooltips for buttons and table columns. The two files
 * are merged, so wording can live in either one. Avoid apostrophes in the strings.
 */

return [

    'fields' => [

        'ProjectResource' => [
            'name' => [
                'short' => 'The project or campaign name.',
                'full' => 'Use the client or campaign name exactly as you want it shown, for example Afari Travels Thailand Campaign. It is the headline on the project page and on its card on the Work page.',
            ],
            'slug' => [
                'short' => 'The web address of this project.',
                'full' => 'Use lowercase letters, numbers and hyphens only, for example afari-travels-thailand. It becomes eclipzmedia.com/work/your-address. Set it before sharing the link, because changing it later breaks links that were already shared.',
            ],
            'industry_id' => [
                'short' => 'The sector this project belongs to.',
                'full' => 'Choose the closest industry. It groups the project with similar work and powers the industry filter on the Work page, so use the same choice for similar projects.',
            ],
            'client_partner_id' => [
                'short' => 'Which client or partner this was for.',
                'full' => 'Choose the organization this work was done for. Add them under Clients and Partners first if they are not in the list.',
            ],
            'project_type' => [
                'short' => 'Visual, case study or digital.',
                'full' => 'Visual is for showing finished work. Case study is for a full story with challenge, strategy, execution and results. Digital is for websites and apps, and should only be used for work that is confirmed. The type also powers the type filter on the Work page.',
            ],
            'location' => [
                'short' => 'Optional. Where the work took place.',
                'full' => 'Enter the place or places, for example Osapa London, Lagos. Leave it empty if it does not apply.',
            ],
            'year' => [
                'short' => 'Optional. The year of the work.',
                'full' => 'Enter a four digit year such as 2026. Leave it empty if you are not sure, rather than guessing.',
            ],
            'short_description' => [
                'short' => 'A one line summary for project cards.',
                'full' => 'Shown on the project cards on the Work page and the home page. Keep it to one or two sentences saying what was done and for whom.',
            ],
            'description' => [
                'short' => 'The full description on the project page.',
                'full' => 'Describe what Eclipz did, who it was for and what it achieved, in plain language. Only include facts that are true and can be confirmed. Do not invent clients, results or quotes.',
            ],
            'cover_image' => [
                'short' => 'The main picture for this project.',
                'full' => 'Upload a landscape picture about 1600 pixels wide as JPG, PNG or WebP. It is the picture on the project card and at the top of the project page. Use real work from the project, not stock photos.',
            ],
            'video_url' => [
                'short' => 'Optional. A link to a video of the work.',
                'full' => 'Paste the full web address of the video, for example a YouTube or Vimeo link. After saving, open the project page and check that it plays. Leave it empty if there is no video.',
            ],
            'project_url' => [
                'short' => 'Optional. A link to the live result.',
                'full' => 'Add a link to the live website, page or campaign, starting with https://. Leave it empty if there is nothing public to link to.',
            ],
            'images' => [
                'short' => 'The gallery pictures.',
                'full' => 'Add each picture with the add button and drag to reorder. Every picture needs a short description in the alt text box for people using screen readers. Use JPG, PNG or WebP up to 5 MB each.',
            ],
            'path' => [
                'short' => 'The picture file.',
                'full' => 'Upload a JPG, PNG or WebP up to 5 MB. Landscape pictures work best in the gallery.',
            ],
            'alt_text' => [
                'short' => 'Describe the picture in one sentence.',
                'full' => 'Write a short sentence saying what is in the picture, for people who cannot see it and for search engines. For example Guests relaxing in a Purple Lavender Court living room.',
            ],
            'services' => [
                'short' => 'The services Eclipz provided.',
                'full' => 'Select every service that applies, for example Videography and Social media management. They are listed on the project page and used by the service filter on the Work page. Manage the list under Services.',
            ],
            'challenge' => [
                'short' => 'Case studies: the problem to solve.',
                'full' => 'Describe the situation before Eclipz started, in one or two short paragraphs. Used for projects marked as a case study. Only include facts the client has confirmed.',
            ],
            'strategy' => [
                'short' => 'Case studies: what was planned.',
                'full' => 'Explain the approach and the thinking behind it, such as the audience, the message and the channels chosen. Used for case studies.',
            ],
            'execution' => [
                'short' => 'Case studies: how it was delivered.',
                'full' => 'Describe what was actually produced and delivered, such as the shoots, content and campaigns. Used for case studies.',
            ],
            'metrics' => [
                'short' => 'Real results only.',
                'full' => 'Add a label and a value for each result, for example Reach and 119K+. Only enter figures that came from the client or from your own reports, and never estimates. Leave this empty if you do not have verified numbers.',
            ],
            'label' => [
                'short' => 'What was measured.',
                'full' => 'Use a short name such as Reach, Engagements or Qualified leads.',
            ],
            'value' => [
                'short' => 'The result.',
                'full' => 'Type the figure exactly as reported, for example 119K+ or 2,310. Do not round up or estimate.',
            ],
            'is_published' => [
                'short' => 'Off keeps it hidden from the website.',
                'full' => 'A project is visible to visitors only when this is on. Turn it on once the pictures and text are ready. Turn it off to take the project down without deleting it.',
            ],
            'is_featured' => [
                'short' => 'Shows it on the home page.',
                'full' => 'Featured and published projects appear under Selected Work on the home page, up to six at a time, in the order set below. Turn it off to remove it from the home page while keeping it on the Work page.',
            ],
            'sort_order' => [
                'short' => 'Lower numbers come first.',
                'full' => 'Projects are shown in this order on the Work page and in Selected Work. Use small numbers such as 1, 2 and 3. Two projects with the same number can appear in either order.',
            ],
        ],

        'TestimonialResource' => [
            'client_name' => [
                'short' => 'The person who gave the quote.',
                'full' => 'Use their real name exactly as they want it shown. Only add a testimonial with the permission of the client.',
            ],
            'company' => [
                'short' => 'Optional. Their company.',
                'full' => 'Add the business they speak for. It is shown after their name.',
            ],
            'position' => [
                'short' => 'Optional. Their job title.',
                'full' => 'For example Managing Director. Leave it empty if they prefer not to share it.',
            ],
            'photo' => [
                'short' => 'Optional. A photo of the person.',
                'full' => 'Upload a square or portrait JPG, PNG or WebP, with their permission. Leave it empty if you do not have one.',
            ],
            'quote' => [
                'short' => 'Their words.',
                'full' => 'Paste the quote exactly as they said it, without changing the meaning. Never write testimonials on behalf of clients or use invented quotes.',
            ],
            'is_published' => [
                'short' => 'Off keeps it hidden.',
                'full' => 'Only published testimonials appear on the home page. Turn it on once you have confirmed the wording and the permission.',
            ],
            'sort_order' => [
                'short' => 'Lower numbers come first.',
                'full' => 'The home page shows up to three testimonials in this order, so give your best ones the lowest numbers.',
            ],
        ],

        'ServiceCategoryResource' => [
            'name' => [
                'short' => 'The group heading on the Services page.',
                'full' => 'Use a clear group name such as Media Production. It is the heading on the Services page and a label in the home page Capabilities section.',
            ],
            'slug' => [
                'short' => 'Used internally to identify the group.',
                'full' => 'Use lowercase letters, numbers and hyphens only. Visitors do not see it, so you rarely need to change it.',
            ],
            'is_visible' => [
                'short' => 'Off hides the whole group.',
                'full' => 'A group appears on the website only when this is on and it contains at least one visible service. Turn it off to hide the group without deleting its services.',
            ],
        ],

        'ServiceResource' => [
            'name' => [
                'short' => 'The service as visitors read it.',
                'full' => 'Use short and plain wording such as Commercial videography. Each service is listed under its group on the Services page.',
            ],
            'slug' => [
                'short' => 'Used internally to identify the service.',
                'full' => 'Use lowercase letters, numbers and hyphens only. Visitors do not see it, so you rarely need to change it.',
            ],
            'service_category_id' => [
                'short' => 'The group this service belongs to.',
                'full' => 'Choose the group it should appear under. Create the group under Service Categories first if it is missing.',
            ],
            'is_visible' => [
                'short' => 'Off hides this service.',
                'full' => 'Turn it off to hide a service from the Services page without deleting it.',
            ],
        ],

        'ProcessStepResource' => [
            'title' => [
                'short' => 'The step name.',
                'full' => 'Use one or two words such as Discovery or Strategy. It is shown in the numbered overview on the home page and in the full list on the Services page.',
            ],
            'description' => [
                'short' => 'What happens in this step.',
                'full' => 'Write one or two plain sentences explaining what Eclipz does and what the client can expect. It appears with the step on the Services page.',
            ],
            'is_visible' => [
                'short' => 'Off hides this step.',
                'full' => 'A hidden step is not shown anywhere on the website, and the remaining steps are renumbered automatically.',
            ],
        ],

        'MediaLibraryItemResource' => [
            'path' => [
                'short' => 'The file to store.',
                'full' => 'Upload a file of type JPG, PNG, WebP, PDF, MP4 or WebM up to 5 MB. SVG and script files are rejected for security.',
            ],
            'label' => [
                'short' => 'A name to help you find it later.',
                'full' => 'Describe the file in a few words, such as Purple Lavender Court exterior photo. It is for your own reference.',
            ],
        ],

        'CompanySettingsPage' => [
            'company_name' => [
                'short' => 'The company name.',
                'full' => 'Used as the browser title of the home page and in the company text across the site. Keep the full registered name, Eclipz Media and Marketing Limited.',
            ],
            'tagline' => [
                'short' => 'The main headline on the home page.',
                'full' => 'Shown as the large headline at the top of the home page. Keep it short and strong, for example Crafting Timeless Visual Stories.',
            ],
            'short_description' => [
                'short' => 'A brief description of the company.',
                'full' => 'Write one or two sentences about what Eclipz does. Keep it factual and avoid claims you cannot prove.',
            ],
            'story' => [
                'short' => 'The company story on the About page.',
                'full' => 'Tell how Eclipz started and what it stands for, in a few short paragraphs. Stick to facts that are true and do not invent history, dates or awards.',
            ],
            'what_we_do' => [
                'short' => 'What Eclipz does, for the About page.',
                'full' => 'Explain the services and approach in plain language. Keep it consistent with the Services page.',
            ],
            'differentiator' => [
                'short' => 'What makes Eclipz different.',
                'full' => 'Say in one or two honest sentences what clients get from Eclipz that they would not get elsewhere. Avoid vague claims such as best or leading unless you can prove them.',
            ],
            'client_type_text' => [
                'short' => 'Who Eclipz works with.',
                'full' => 'Describe the kinds of businesses and organizations you serve, in plain words.',
            ],
            'mission' => [
                'short' => 'Why Eclipz exists.',
                'full' => 'Shown in the Why Eclipz section on the home page and on the About page. Use the approved mission wording and keep it to one or two sentences.',
            ],
            'vision' => [
                'short' => 'Where Eclipz is heading.',
                'full' => 'Shown beside the mission on the home page and on the About page. Use the approved wording.',
            ],
            'founder_name' => [
                'short' => 'The founder name.',
                'full' => 'Shown in the founder section of the About page. Do not add biographical details that have not been confirmed.',
            ],
            'founder_title' => [
                'short' => 'The founder job title.',
                'full' => 'For example Founder and Creative Director.',
            ],
            'founder_bio' => [
                'short' => 'A short biography.',
                'full' => 'Write only what the founder has approved. Do not invent history, awards or qualifications.',
            ],
            'founder_specializations' => [
                'short' => 'Areas of expertise.',
                'full' => 'List the main areas of expertise in one line, for example Creative direction and Brand strategy. Only include true specialities.',
            ],
            'founder_quote' => [
                'short' => 'Optional. A short quote from the founder.',
                'full' => 'Use a real quote that the founder has approved. Leave it empty if there is none.',
            ],
            'logo' => [
                'short' => 'Stored for reference.',
                'full' => 'At the moment the website uses fixed brand files for the logo, so uploading here does not change what visitors see. Ask your developer before relying on this field.',
            ],
            'favicon' => [
                'short' => 'Stored for reference.',
                'full' => 'At the moment the browser tab icon comes from a fixed brand file, so uploading here does not change it. Ask your developer before relying on this field.',
            ],
        ],

        'InquiryResource' => [
            'name' => [
                'short' => 'The name the visitor entered.',
                'full' => 'This is what the visitor typed in the contact form. Use it to address them when you reply.',
            ],
            'company' => [
                'short' => 'Their company, if given.',
                'full' => 'An optional field from the contact form.',
            ],
            'email' => [
                'short' => 'Where to reply.',
                'full' => 'The visitor email address. Status updates are sent here automatically, and replies you write yourself should go here too.',
            ],
            'phone' => [
                'short' => 'Their number, if given.',
                'full' => 'An optional number from the form. Include the country code when you message them on WhatsApp.',
            ],
            'service_requested' => [
                'short' => 'The service they chose.',
                'full' => 'The option the visitor picked on the contact form. It may differ from what they describe in the message, so read both.',
            ],
            'message' => [
                'short' => 'What they wrote.',
                'full' => 'The description of the project in their own words. Read it fully before you reply.',
            ],
            'status' => [
                'short' => 'Where this enquiry is now.',
                'full' => 'New means nobody has reviewed it yet. Under review, Responded and Closed mark your progress. Moving forward to one of those stages emails the visitor a short update once, while moving back or returning to New never sends an email. If an email fails, use Resend emails.',
            ],
        ],
    ],

    'actions' => [
        'resendEmails' => 'Try again to send only the emails that failed. Emails that were already delivered are never sent twice.',
        'togglePublish' => 'Show or hide this project on the website without deleting it.',
        'toggleFeature' => 'Add or remove this project from Selected Work on the home page.',
        'open' => 'Open this item.',
        'create' => 'Create a new item, or save the one you have just filled in.',
    ],

    'columns' => [
        'reference' => 'The Inquiry ID the visitor was given. You can search by it.',
        'delivery' => 'Shows whether the emails for this enquiry were delivered. A resend option appears when one failed.',
        'status' => 'Where this item currently stands.',
        'services_count' => 'How many services are in this group.',
        'project_type' => 'Visual, case study or digital.',
    ],
];