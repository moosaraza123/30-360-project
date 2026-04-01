# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel 11 application for calculating bond/derivative day count conventions (30/360, Actual/365, etc.) with user authentication, calculation history, email subscriptions, and export features.

## Commands

```bash
# Start full dev environment (server + queue + logs + Vite)
composer dev

# Start individual services
php artisan serve
npm run dev
php artisan queue:listen

# Build frontend assets
npm run build

# Database
php artisan migrate
php artisan db:seed

# Run all tests
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/Feature/Auth/AuthenticationTest.php

# Run a specific test method
vendor/bin/phpunit --filter test_users_can_authenticate_using_the_login_screen

# Code formatting (Laravel Pint)
./vendor/bin/pint

# Tinker (interactive REPL)
php artisan tinker
```

## Architecture

### Modular Structure

The core business logic lives in `Modules/DayCountCalculator/` using [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules). The main `app/` directory contains only standard Laravel auth scaffolding.

```
Modules/DayCountCalculator/
├── app/
│   ├── DTOs/           # CalculationRequest, CalculationResult, ComparisonResult
│   ├── Entities/       # Eloquent models: Calculation, SavedCalculation, Subscriber
│   ├── Features/DayCount/  # Strategy pattern — one class per convention
│   ├── Http/Controllers/   # CalculatorController, ComparisonController, AdminController, SubscriberController
│   └── Repositories/   # CalculationRepository, SubscriberRepository
├── Providers/          # Service, Route, Event providers
├── resources/views/    # Blade templates for calculator, comparison, admin, educational, subscription
└── routes/web.php      # Module routes
```

### Day Count Convention Strategy Pattern

Each of the 9 supported conventions is implemented as a standalone Feature class in `Modules/DayCountCalculator/app/Features/DayCount/`:

- `Calculate30360USFeature.php` — 30/360 US Bond Basis
- `Calculate30360BondBasisFeature.php`
- `Calculate30E360Feature.php` — European Eurobond
- `Calculate30E360ISDAFeature.php`
- `CalculateActual360Feature.php`
- `CalculateActual364Feature.php`
- `CalculateActual365Feature.php`
- `CalculateActualActualFeature.php` — ICMA
- `CalculateActualActualISDAFeature.php`

### Data Flow

1. Request → `CalculationRequest` DTO (validates/binds input)
2. `CalculatorController` dispatches to the appropriate Feature class
3. Feature class computes result → `CalculationResult` DTO (includes step-by-step breakdown)
4. Result stored in `calculations` table (with `calculation_steps` as JSON)
5. Authenticated users can save via `saved_calculations` table

### Key Database Tables

- `calculations` — all calculations; indexed on `convention_type+created_at`, `session_id`, `user_id`
- `saved_calculations` — user-saved refs to calculations (names, notes, favorites)
- `subscribers` — email subscription list with verification tokens
- `users` — standard Laravel auth

Guest users are tracked via `calculator_session_id` in session storage.

### Frontend Stack

- **Vite** with two entry points: `resources/css/app.css` + `resources/js/app.js` (base) and `Modules/DayCountCalculator/resources/assets/` (module)
- **Tailwind CSS** + **Bootstrap 5** for styling
- **Alpine.js** for reactive UI
- **Chart.js** for admin analytics
- **Flatpickr** for date pickers
- **Laravel Excel** + **DomPDF** for exports

### Routes

- `routes/web.php` — dashboard and profile (auth-guarded)
- `routes/auth.php` — registration, login, password reset, email verification
- `Modules/DayCountCalculator/routes/web.php` — all calculator, comparison, subscription, and admin routes

## Environment

- **DB:** MySQL (`devlocal` database by default)
- **Session/Cache/Queue:** database-backed
- **Mail:** log driver in development (emails go to `storage/logs/`)
