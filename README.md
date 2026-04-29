# Luigi Giussani Foundation Scholarship Management System

A comprehensive scholarship management system built with Laravel, Filament, and Inertia.js.

## Features

- **Applicant Portal**: Multi-step application form with document uploads
- **Admin Dashboard**: Filament-powered admin panel for managing applications and scholars
- **Role-Based Access Control**: System Admin, Committee Member, Applicant, and Scholar roles
- **Automated Scoring**: AI-powered application scoring system
- **Email Notifications**: Automated emails for all key actions using Resend.com

## Email Integration

This application uses **Resend.com** for sending transactional emails. See [RESEND_SETUP.md](RESEND_SETUP.md) for detailed setup instructions.

### Email Types
- Application submission confirmation
- Application status updates (Under Review, Approved, Rejected)
- Email verification for new registrations
- Password reset for all users
- System user creation notifications with credentials

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy `.env.example` to `.env` and configure:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database and run migrations:
   ```bash
   php artisan migrate --seed
   ```

5. Set up Resend for emails (see [RESEND_SETUP.md](RESEND_SETUP.md))

6. Build frontend assets:
   ```bash
   npm run build
   ```

7. Start the queue worker:
   ```bash
   php artisan queue:work
   ```

8. Serve the application:
   ```bash
   php artisan serve
   ```

## Quick Start

### For Development
```bash
composer dev
```
This runs the server, queue worker, logs, and Vite concurrently.

### Access Points
- **Applicant Portal**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

## Technology Stack

- **Backend**: Laravel 11
- **Admin Panel**: Filament 3
- **Frontend**: Inertia.js + React
- **Database**: SQLite (configurable)
- **Email**: Resend.com
- **Queue**: Database (configurable)

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

