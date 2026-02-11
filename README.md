# PortFlow Backend

PortFlow is a modular backend for a Port Operations Management Platform.
This repository focuses on architecture quality, domain rules, and API reliability for a decoupled SPA + API product.

## Why this project exists

This project is a portfolio case to demonstrate:

- Modular monolith design (`Delivery by Feature`)
- Domain-driven organization (Domain, Application, Infrastructure, Http)
- Clean architecture and SOLID-oriented code
- Explicit use cases through action classes
- Testable business rules with unit and feature coverage

## Architecture at a glance

PortFlow uses a decoupled architecture:

- Frontend: Vue 3 SPA (separate app)
- Backend: Laravel 12 REST API
- Authentication: Laravel Sanctum
- Data and platform targets: PostgreSQL, Redis, S3

### Module layout

```text
app/Modules/
  Users/
  Containers/
  Ships/        (planned)
  Berths/       (planned)
  Operations/   (planned)
  Billing/      (planned)
```

Each module is split into:

- `Domain` for entities, rules, invariants
- `Application` for use cases/actions
- `Infrastructure` for persistence and integrations
- `Http` for controllers, requests, resources
- `Routes` for module endpoints

## Implemented modules

### Users

- Register and login
- Logout with Sanctum
- Profile read/update
- Password change
- Role-based access control
- Admin user management (list, show, assign role, deactivate)

### Containers

- Create/list/get/update containers
- Container status changes
- Domain-level status transition validation
- Container type and status modeled via enums

## API overview

### Public

- `POST /api/auth/register`
- `POST /api/auth/login`

### Authenticated (`auth:sanctum`)

- `POST /api/auth/logout`
- `GET /api/users/me`
- `PUT /api/users/me`
- `PUT /api/users/me/password`
- `GET /api/containers`
- `POST /api/containers`
- `GET /api/containers/{id}`
- `PUT /api/containers/{id}`
- `PATCH /api/containers/{id}/status`

### Admin only

- `GET /api/users`
- `GET /api/users/{id}`
- `PUT /api/users/{id}/role`
- `DELETE /api/users/{id}`

## Tech stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- Pest + PHPUnit
- Vite tooling

## Local setup

### Requirements

- PHP 8.2+
- Composer
- Node.js + npm
- SQLite (fast local/testing) or PostgreSQL

### Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

### Run in development

```bash
composer run dev
```

That command starts:

- API server
- Queue listener
- Vite dev server

## Testing

Run all tests:

```bash
composer test
```

Current coverage includes:

- Authentication and user management flows
- Container API behavior
- Container state machine/domain transition rules

Testing is configured with in-memory SQLite in `phpunit.xml`.

## Roadmap

- Ships and berths modules
- Port operations events and history timeline
- Billing rules (storage, demurrage, reefer energy)
- Dashboard KPIs
- AWS deployment documentation

## Related documentation

- `PortFlow-Architecture.md` for full architecture and phased delivery plan

---

PortFlow is built as a realistic backend engineering case study for logistics systems.
