Eclipz Media & Marketing Limited - Official Website

Crafting Timeless Visual Stories

The official website for Eclipz Media & Marketing Limited, a creative media and digital marketing agency based in Lagos, Nigeria. The platform showcases the company's services, projects, brand identity, and client-facing information through a responsive public website and a content management dashboard.

Live Website: https://eclipzmedia.com/

Overview

The Eclipz Media website is designed to establish a strong digital presence for the agency, communicate its services, showcase its creative work, and make it easier for potential clients to get in touch.

The application combines a public-facing website with an administrative dashboard that allows authorized team members to manage supported website content without editing application code.

Features

Public Website

- Responsive layouts for desktop, tablet, and mobile devices.
- Homepage presenting the agency's brand, services, and featured content.
- Services and company information pages.
- Portfolio pages featuring individual projects.
- Client and partner information.
- Contact form for project inquiries.
- Blog pages for company updates and editorial content.
- Search engine optimization foundations, including sitemap and robots directives.

Content Management

- Administrative dashboard powered by Filament.
- Project and service management.
- Client and partner management.
- Testimonial management.
- Company and contact information settings.
- Blog post management.
- Inquiry management.
- Administrative activity logging.

Technical Features

- Laravel-based application architecture.
- Database-backed content management.
- Server-rendered pages.
- Asset compilation and optimization with Vite.
- Automated application tests.
- Search engine discoverability support.

«Features may evolve as the website is maintained. Refer to the current implementation for the definitive feature set.»

Technology Stack

Technology| Purpose
PHP 8.3| Server-side application development
Laravel 11| Application framework
MySQL| Relational database
Filament 3| Administrative dashboard
Blade| Server-side templating
Tailwind CSS| Styling and responsive layouts
Alpine.js| Lightweight frontend interactions
Vite| Frontend asset compilation
PHPUnit / Laravel testing tools| Automated testing

Project Structure

eclipz-app/
├── app/
│ ├── Filament/ # Admin dashboard and resources
│ ├── Http/ # Controllers and middleware
│ └── Models/ # Eloquent models
├── bootstrap/ # Application bootstrapping
├── config/ # Application configuration
├── database/
│ ├── migrations/ # Database schema migrations
│ ├── factories/ # Model factories
│ └── seeders/ # Database seeders
├── public/ # Public entry point and assets
├── resources/
│ ├── css/ # Application styles
│ ├── js/ # Frontend JavaScript
│ └── views/ # Blade templates
├── routes/ # Application routes
├── storage/ # Application-generated files
├── tests/ # Automated tests
├── composer.json # PHP dependencies
├── package.json # Frontend dependencies and scripts
└── vite.config.js # Vite configuration

Local Development Setup

Prerequisites

- PHP 8.3 or a compatible version meeting the project's Composer requirements.
- Composer.
- Node.js and npm.
- MySQL.
- Git.

1. Clone the repository

git clone https://github.com/GeeneeMarx/eclipz-media
cd eclipz-app

2. Install dependencies

composer install
npm install

3. Configure the environment

Create a local environment file:

cp .env.example .env

On Windows PowerShell, use:

Copy-Item .env.example .env

Configure your local database and application settings in ".env". Create the corresponding MySQL database before running migrations.

Generate an application key:

php artisan key:generate

Never commit your ".env" file, production credentials, database passwords, or application keys to GitHub.

4. Prepare the database

php artisan migrate

Use seeders only when required by the development setup:

php artisan db:seed

Review the available seeders first, especially if they create administrative accounts or sample content.

5. Build frontend assets

For development:

npm run dev

For a production asset build:

npm run build

6. Start the local server

php artisan serve

Open the local URL shown in the terminal, typically:

http://127.0.0.1:8000

Testing

Run the automated test suite with:

php artisan test

Some tests may require a configured test database and appropriate environment settings. Configure these before running the suite.

Deployment

The live website is hosted at:

https://eclipzmedia.com/

Deployment requires the appropriate PHP environment, Composer dependencies, frontend build assets, database configuration, storage permissions, and production environment settings.

Database migrations should be reviewed and backed up before being applied to production. Production credentials must be configured securely on the hosting environment and must not be committed to the repository.

Search Engine Optimization

The application includes routes intended to support search engine discovery:

- "/sitemap.xml" — XML sitemap for public pages and eligible published content.
- "/robots.txt" — crawler directives and sitemap reference.

The production responses should be verified after deployment. Submitting a sitemap through Google Search Console does not guarantee that every URL will be indexed.

Security

- Keep environment variables and credentials private.
- Use secure HTTPS connections in production.
- Restrict administrative access to authorized users.
- Validate and authorize administrative actions.
- Back up production data before database changes.
- Keep dependencies and the application framework maintained.

Project Purpose

This project demonstrates the development of a production-oriented business website using Laravel, with a public-facing experience and an administrative content management system.

It also serves as an ongoing platform for presenting the agency's work, maintaining company information, and supporting future content and marketing initiatives.

Credits

Eclipz Media & Marketing Limited

Website development and maintenance: Marvellous Ese / Geenee

Official website: https://eclipzmedia.com/
