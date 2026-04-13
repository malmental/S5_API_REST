<p align="center">
  <img src="docs/images/incidensly_letter.png" alt="INCIDENsly Logo" width="400">
</p>

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://img.shields.io/badge/Laravel-12.x-F05340?style=flat&logo=laravel" alt="Laravel">
  </a>
  <a href="https://www.php.net" target="_blank">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php" alt="PHP">
  </a>
  <a href="https://www.php-fig.org/psr/psr-12/" target="_blank">
    <img src="https://img.shields.io/badge/PSR-12-青?style=flat" alt="PSR-12">
  </a>
  <a href="https://github.com/orlandorojas/INCIDENsly/blob/main/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License">
  </a>
</p>

**[➪ Live Demo API Docs](https://incidensly-webapp-production.up.railway.app/docs/)**

---

Incident manager system with a complete REST API and automated tests. Built with Laravel 12 and OAuth2 authentication via Passport.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Local Installation](#local-installation)
- [Docker Installation](#docker-installation)
- [Configuration](#configuration)
- [Running Tests](#running-tests)
- [Static Analysis](#static-analysis-phpstan)
- [API Usage](#api-usage)
- [Endpoints](#endpoints)
- [Architecture](#architecture)
- [Data Model](#data-model)
- [Security & Authorization](#security--authorization)
- [Tech Stack](#tech-stack)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### App 
- **RESTful API** — Versioned endpoints (v1) following REST practices
- **OAuth2 Authentication** — Access tokens via Laravel Passport
- **Incident Management** — Full CRUD with filters, full-text search, and pagination
- **Nested Comments** — Threaded comment system with hierarchical replies (parent_id)
- **Tag System** — Flexible tagging with N:M relationship between incidences
- **Aggregated Metrics** — Statistics by status and priority (admin only)
- **User Management** — Complete admin dashboard

### Docs
- **Interactive Documentation** — Auto-generated API docs with Scribe + OpenAPI 3.0


---

## Requirements

| Requirement | Minimum Version | Notes |
|-------------|----------------|-------|
| PHP | 8.2+ | Requires extensions: pdo, mbstring, xml, json |
| Composer | 2.x | PHP dependency manager |
| Node.js | 18.x | Optional, only for asset compilation |
| NPM | 9.x | Optional |
| SQLite | 3.x | Default; also supports MySQL 8+ and PostgreSQL 13+ |
| Docker | 20.x | For containerized installation |

---

## Local Installation

### 1. Clone the repository

```bash
git clone https://github.com/malmental/S5_API_REST.git
cd S5_API_REST
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
```

Edit `.env` for your environment. Default configured for SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

> **Note:** If using SQLite, you must create the database file before running migrations:
> ```bash
> touch database/database.sqlite
> ```

### 4. Generate application keys

```bash
php artisan key:generate
```

### 5. Run database migrations

```bash
php artisan migrate:fresh
```

> **Note:** `migrate:fresh` drops all tables and recreates them. Use `php artisan migrate` if you have existing data you want to preserve.

### 6. Generate Passport keys

```bash
php artisan passport:install
```

This command creates:
- OAuth Clients for Password Grant and Personal Access
- RSA encryption keys

### 7. Seed the database (optional)

Populate the database with sample data for testing:

```bash
php artisan db:seed
```

This creates:
- 1 Admin user: `admin@telsur.cl` / `password`
- 1 Regular user: `noadmin@telsur.cl` / `password`
- Sample incidences, comments, and tags

### 8. Install JavaScript dependencies

```bash
npm install
npm run build
```

### 9. Start the server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

---

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Environment (local, production) | `local` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_URL` | Application base URL | `http://localhost` |
| `DB_CONNECTION` | Driver: sqlite, mysql, pgsql | `sqlite` |
| `CACHE_STORE` | Cache driver | `database` |
| `QUEUE_CONNECTION` | Queue driver | `database` |
| `SESSION_DRIVER` | Session driver | `database` |

---

## Running Tests

```bash
# Run full suite
php artisan test

# Specific feature tests
php artisan test --filter=IncidenceTest
php artisan test --filter=UserTest
php artisan test --filter=AuthTest
php artisan test --filter=CommentTest
php artisan test --filter=TagTest
```

### Run a specific test

I don't recommend to do this, but you can run a specific test method by reading the name on the file and running command. 

Here's a little help:

```bash
php artisan test --filter=test_authenticated_user_can_create_incidence
```

---

## Static Analysis (PHPStan)

This is undoubtly a great tool but it's propense to produce false positives in certain contexts, which for us, it can be confusing. 

If you want to run it, you can do it with the following command:

```bash
# Full analysis
./vendor/bin/phpstan analyse

# With more memory
./vendor/bin/phpstan analyse --memory-limit=512M
```

The project is configured at **level 5** (out of 9). You can adjust this in `phpstan.neon` if you want to be more strict or more easy on it.

---

## API Usage

### Interactive Documentation

Access the complete API documentation at:

```
http://localhost:8000/docs
```
<p align="center">
  <img src="docs/images/incidensly_api_docs.png" alt="INCIDENsly Logo" width="400">
</p>

Includes:
- Complete endpoint reference
- Try It Out (test endpoints directly from browser)
- Request/response examples

As you had run the seeder, you have a full database with sample data to test the endpoints. 

You will be provided with two users to test authentication and authorization:
````
Admin user: admin@telsur.cl
Password: password

Regular user: noadmin@telsur.cl
Password: password
````



### Postman Collection

Import the ready-to-use Postman collection:

```
docs/INCIDENsly_API.postman_collection.json
```

It includes all endpoints with pre-configured requests and sample data.

### Authentication Format

The API uses Bearer Token authentication via Laravel Passport.

```http
Authorization: Bearer {token}
Content-Type: application/json
```

### Example: Login

<p align="center">
  <img src="docs/images/auth_api_docs.png" alt="INCIDENsly Logo" width="400">
</p>

And with this information you can test all the endpoints that require authentication, just remember to replace `{token}` with the actual token you receive from the login response.

For non admin users, you can use the regular user credentials to test endpoints that require authentication but not admin privileges.

Down below you can find some examples of how to use the API with filters, pagination and full-text search. I will leave this here as a reference for you to test the API and see how it works with different parameters from the business logic perspective.

##### NOTE:

Add | json_pp at the end of the curl command to pretty print the JSON response in the terminal.

```bash
curl "http://localhost:8000/api/v1/incidences?status=open" | json_pp
``` 

### Example: List Incidences with Filters

```bash
# Filter by status
curl "http://localhost:8000/api/v1/incidences?status=open" 

# Filter by priority
curl "http://localhost:8000/api/v1/incidences?priority=high"

# Full-text search
curl "http://localhost:8000/api/v1/incidences?search=server"

# Combined filters
curl "http://localhost:8000/api/v1/incidences?status=open&priority=high&search=server"

# Filter by tags
curl "http://localhost:8000/api/v1/incidences?tags=1,2,3"

# Pagination
curl "http://localhost:8000/api/v1/incidences?per_page=20&page=2"
```

---

## Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/register` | Register new user | No |
| POST | `/api/v1/login` | Login | No |
| POST | `/api/v1/logout` | Invalidate tokens | Yes |
| GET | `/api/v1/me` | Get current profile | Yes |

### Incidences

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/incidences` | List all (paginated) | No |
| GET | `/api/v1/incidences/{id}` | View incidence detail | No |
| POST | `/api/v1/incidences` | Create incidence | Yes |
| PUT | `/api/v1/incidences/{id}` | Update incidence | Yes* |
| DELETE | `/api/v1/incidences/{id}` | Delete incidence | Yes* |
| GET | `/api/v1/my-incidences` | My incidences | Yes |

*Owner or admin only

### Comments

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/incidences/{id}/comments` | List comments | No |
| POST | `/api/v1/incidences/{id}/comments` | Create comment | Yes |
| GET | `/api/v1/comments/{id}` | View comment | No |
| PUT | `/api/v1/comments/{id}` | Update comment | Yes* |
| DELETE | `/api/v1/comments/{id}` | Delete comment | Yes* |

*Owner or admin only

### Tags

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/tags` | List all | No |
| GET | `/api/v1/tags/{id}` | View tag detail | No |
| POST | `/api/v1/tags` | Create tag | Yes |
| PUT | `/api/v1/tags/{id}` | Update tag | Yes* |
| DELETE | `/api/v1/tags/{id}` | Delete tag | Yes* |

*Owner or admin only

### Metrics

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/metrics` | Aggregated statistics | Yes |

### Users (Admin)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/users` | List users | Yes (Admin) |
| GET | `/api/v1/users/{id}` | View user | Yes (Admin) |
| GET | `/api/v1/users/{id}/incidences` | User's incidences | Yes (Admin) |
| DELETE | `/api/v1/users/{id}` | Delete user | Yes (Admin) |

---`

### Design Decisions

| Pattern | Implementation | Benefit |
|---------|---------------|---------|
| **API Versioning** | Prefix `/api/v1` | Backward compatibility for future v2 |
| **AuthorizesUser Trait** | `authorizeOwner()` and `authorizeOwnerOrAdmin()` | DRY across controllers |
| **API Resources** | IncidenceResource, CommentResource, etc. | Consistent transformation |
| **Form Requests** | Validation centralized per operation | Reusability and clarity |
| **FirstOrCreate** | Tags with case-insensitive search | Atomically prevents duplicates |
| **Soft Deletes** | Not implemented (admin can permanently delete) | Simplicity, bye !|

---

## Data Model

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│    User     │         │  Incidence   │         │     Tag     │
├─────────────┤         ├──────────────┤         ├─────────────┤
│ id          │──┐      │ id           │      ┌──│ id          │
│ name        │  │      │ title        │      │  │ name        │
│ email       │  └──◄───│ user_id (FK) │      │  │ user_id(FK) │
│ is_admin    │         │ assigned_to  │◄─────┘  └─────────────┘
│ password    │         │ status       │                │
└─────────────┘         │ priority     │               N:M
                        └──────────────┘          incidence_tag
                               │ 1:N              (pivot table)
                               ▼                        │
                        ┌──────────────┐                │
                        │   Comment    │◄───────────────┘
                        ├──────────────┤
                        │ id           │
                        │ body         │
                        │ user_id (FK) │
                        │ incidence_id │
                        │ parent_id    │──►(self-reference)
                        └──────────────┘
```

### Eloquent Relationships

- **User → Incidences**: `hasMany` (created by user)
- **User → assignedIncidences**: `hasMany` (assigned to user)
- **Incidence → user**: `belongsTo` (creator)
- **Incidence → assignedUser**: `belongsTo` (assignee)
- **Incidence → tags**: `belongsToMany`
- **Incidence → comments**: `hasMany`
- **Comment → user**: `belongsTo`
- **Comment → incidence**: `belongsTo`
- **Comment → parent/children**: `belongsTo`/`hasMany` (self-referencing)

---

## Security & Authorization

### Middleware Applied

- `auth:api` — Requires valid Passport token
- `is_admin` — Verifies `user.is_admin = true`

### `AuthorizesUser` Trait

```php
// Authorize owner OR admin
if ($response = $this->authorizeOwnerOrAdmin($incidence)) {
    return $response;
}

// Authorize owner only
if ($response = $this->authorizeOwner($comment)) {
    return $response;
}
```

---


### Code Style

The project uses **Laravel Pint** for automatic formatting:

```bash
./vendor/bin/pint
```

---

## Known Issues & Bugs

### Bugs to Fix

- **$request undefined in CommentController and TagController**: Methods use `$request->per_page` but `$request` is not injected as a parameter. Relies on global `request()` helper which works but is confusing and should be fixed for clarity.

- **Protected tests in TagTest**: Two tests are marked `protected` instead of `public`, causing PHPUnit to skip them during test execution (`test_anyone_can_view_tags` and `test_authenticated_user_can_create_tag`).

- **IncidencePolicy not used**: A policy exists at `app/Policies/IncidencePolicy.php` but controllers use the `AuthorizesUser` trait instead. The policy is never registered or used, creating unnecessary duplication.

- **N+1 query in syncTags()**: When creating/updating incidences with multiple tags, the `syncTags()` method in `IncidenceController` performs one query per tag (up to 10). Should be optimized to batch queries.

- **Missing database indexes**: The `incidences` table has no indexes on frequently filtered columns (`status`, `priority`, `created_at`). This will cause performance issues at scale.

### Configuration Issues (Fixed)

- **Duplicate Passport migrations**: Multiple sets of Passport migrations existed (timestamp `2026_04_13_*`). Removed duplicate sets. Ensure only one set exists in `database/migrations/`.

- **SQLite path in .env.example**: Previously set to `/absolute/path/to/database.sqlite`. Updated to relative path `database/database.sqlite`.

---

## Future Improvements

### High Priority

- [ ] Fix `$request` variable injection in CommentController and TagController
- [ ] Change protected tests to public in TagTest
- [ ] Register and use IncidencePolicy or remove it to avoid duplication
- [ ] Add database indexes on `incidences.status`, `incidences.priority`, `incidences.created_at`
- [ ] Optimize `syncTags()` to batch tag lookups

### Medium Priority

- [ ] Implement soft deletes for incidences and comments (data recovery)
- [ ] Add comprehensive caching layer for metrics endpoint
- [ ] Add rate limiting to API endpoints (`throttle` middleware)
- [ ] Implement API versioning strategy for v2 (content negotiation or URL prefix)
- [ ] Add role-based access control (RBAC) with multiple roles instead of boolean `is_admin`

### Nice to Have

- [ ] Add email/Slack notifications on incidence status changes
- [ ] Add image/file attachments for incidences
- [ ] Implement WebSocket for real-time updates (Laravel Echo + Pusher)
- [ ] Add API scopes for granular token permissions
- [ ] Implement Horizon for queue management
- [ ] Add audit log for incidence changes
- [ ] Implement draft/archived incidence states

---
