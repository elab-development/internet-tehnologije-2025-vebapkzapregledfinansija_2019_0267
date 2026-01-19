# AI Copilot Instructions - Licne Finansije

## Project Overview
**Licne Finansije** is a Laravel 12 + React personal finance management application. It's a full-stack isomorphic app with SSR support, using Inertia.js to bridge the Laravel backend and React frontend.

**Domain**: Personal finance tracking with budgets, transactions, categories, financial goals, reminders, and documents.

## Architecture

### Stack & Key Technologies
- **Backend**: Laravel 12 with Fortify (auth/2FA), Inertia.js
- **Frontend**: React 19 with TypeScript, Tailwind CSS, Radix UI components
- **Build**: Vite with SSR support (`resources/js/ssr.tsx`)
- **Database**: SQLite (default, configurable via `DB_CONNECTION` env)
- **Testing**: Pest PHP (with Laravel plugin), PHPUnit

### Data Models & Domain
The core financial domain is in `app/Models/`:
- **User**: Authenticated users with roles (enum `Uloga`: KORISNIK, PREMIUM) and 2FA support
- **Transakcija** (Transaction): Income/expense entries with enum `TipTransakcije` (PRIHOD/RASHOD), linked to User & Kategorija
- **Kategorija** (Category): Transaction categories
- **Budzet** (Budget): Monthly/yearly spending limits per user
- **FinansijskiCilj** (Financial Goal): User savings goals
- **Podsetnik** (Reminder): Notifications for financial events
- **Dokument** (Document): Attached financial documents

**Key Pattern**: All models use `idKorisnik` foreign key (not Laravel's conventional `user_id`). Amounts are decimal:2.

### Frontend Organization
- `resources/js/pages/`: Page components (auth, dashboard, settings)
- `resources/js/components/`: Reusable UI components (Radix UI + custom)
- `resources/js/hooks/`: Custom hooks (e.g., `use-appearance` for theme)
- `resources/js/layouts/`: Layout wrappers
- `resources/js/lib/`: Utility functions

Inertia resolves pages dynamically: `./pages/${name}.tsx` via Vite glob import.

## Development Workflow

### Setup & Installation
```bash
composer run-script setup   # One-command setup (install, migrate, build)
```

### Development Commands
- **Full dev environment**: `composer run dev` (concurrently: artisan serve, queue listener, Vite dev)
- **With SSR**: `composer run dev:ssr` (adds pail logging and Inertia SSR process)
- **Frontend dev**: `npm run dev` (Vite)
- **Frontend build**: `npm run build` (or `npm run build:ssr` with SSR)
- **Tests**: `composer run test` (runs Pest)
- **Linting/Formatting**: `npm run lint`, `npm run format`, `npm run types`

### Database
- Migrations in `database/migrations/` (Pest-based tests use `:memory:` SQLite)
- Factories in `database/factories/` (BudzetFactory, UserFactory exist)
- Default env: `.env.example` copied to `.env` on setup

## Conventions & Patterns

### PHP/Laravel
- **Enum usage**: `TipTransakcije` and `Uloga` enums in `app/Enums/` with string backing
- **Foreign keys**: Use `idKorisnik`, `idKategorija` naming (non-standard)
- **Factories/Seeders**: Located in `Database\Factories` and `Database\Seeders` namespaces
- **Two-Factor Auth**: Built-in via Fortify; `User` model uses `TwoFactorAuthenticatable` trait

### React/Frontend
- **Component styling**: Tailwind CSS + Radix UI; see `components.json` for shadcn/ui setup
- **Inertia integration**: Pages automatically receive server props; use `usePage<T>()` for typed access
- **TypeScript**: Strict mode in `tsconfig.json`; components are TSX by default
- **Theme**: Dark/light mode via `use-appearance` hook (initializes on load)

### Project-Specific Quirks
- **Serbian naming**: Domain models use Serbian names (Transakcija, Kategorija, etc.)
- **Enum casting**: `Transakcija` casts `tipTransakcije` to `TipTransakcije` enum
- **Decimal precision**: Financial amounts always cast to `decimal:2`

## Key Files & Responsibilities
- `routes/web.php`: Main routes (Inertia renders pages from `resources/js/pages/`)
- `routes/settings.php`: Settings routes (user/profile management via Fortify)
- `config/inertia.php`: SSR config (enabled by default, listens on 13714)
- `vite.config.ts`: Vite plugins (React, Tailwind, Wayfinder for form variants)
- `composer.json`: Script aliases for `dev`, `dev:ssr`, `test`

## Testing & Quality
- **Pest** is the primary test runner (configured in `phpunit.xml`)
- Tests organized: `tests/Unit/` and `tests/Feature/`
- Test environment uses in-memory SQLite
- ESLint + Prettier for frontend; Laravel Pint for PHP (configured via `composer.json`)

## Common Tasks
- **Adding a model**: Create in `app/Models/`, migration, factory, then controller
- **Adding a page**: Create `.tsx` in `resources/js/pages/`, route in `web.php`
- **Financial calculations**: Use `decimal:2` casts and model relationships (see `Transakcija->korisnik()`)
- **Database changes**: Create migration, update model fillables & casts, regenerate types if needed
