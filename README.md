# TheSchool

A modernized school management application built with Laravel, jQuery, Docker, automated testing, and CI quality gates.

The project originated as a legacy PHP application and was rebuilt around Laravel while preserving the original application's core domain: administrators, students, courses, authentication, and role-based authorization.

The main purpose of the project is to demonstrate backend modernization, automated testing, containerization, and DevOps practices.

## Features

### Administration

- Administrator authentication
- Role-based authorization
- Administrator management
- Student management
- Course management
- Student-to-course relationships
- Image upload and replacement
- Protected owner account
- Validation and error handling

### Roles

The application supports three administrator roles:

| Role | Administrators | Students | Courses |
|---|---|---|---|
| Owner | Manage | Manage | Manage |
| Manager | Manage | Manage | Manage |
| Sales | No | Manage | Manage |

Additional restrictions protect the `owner` role:

- New owners cannot be created through the administrator API.
- The owner role cannot be assigned to another administrator.
- Non-owner administrators cannot modify the owner.
- The owner account cannot be deleted.

## Technology Stack

### Backend

- PHP 8.3
- Laravel 12
- MariaDB
- Redis

### Frontend

- JavaScript
- jQuery
- Vite
- HTML/CSS

### Infrastructure

- Docker
- Docker Compose
- Apache
- NGINX

### Testing and Code Quality

- PHPUnit
- Cypress
- PHPStan / Larastan
- PHP_CodeSniffer
- PHP-CS-Fixer
- Infection mutation testing
- Semgrep
- Checkov

### CI

GitHub Actions automatically runs the project's test and quality checks.

The pipeline includes:

- PHPUnit feature tests
- Cypress end-to-end tests
- frontend build validation
- PHPStan static analysis
- PHPCS
- PHP-CS-Fixer validation
- Infection mutation testing
- Semgrep security analysis
- Checkov infrastructure/container checks

## Mutation Testing

The project uses Infection to evaluate the effectiveness of the PHPUnit test suite rather than relying only on code coverage.

Current result:

```text
Mutation Code Coverage: 100%
Mutation Score Indicator (MSI): 80.60%
Killed mutants: 241
Escaped mutants: 58
Total mutants: 299
```

Mutation testing helped identify tests that executed code without sufficiently verifying its behavior.

## Project Structure

```text
TheSchool/
├── .github/
│   └── workflows/
├── cypress/
├── server/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Middleware/
│   │   └── Models/
│   ├── database/
│   │   └── migrations/
│   ├── resources/
│   ├── routes/
│   ├── tests/
│   │   └── Feature/
│   ├── composer.json
│   ├── infection.json5
│   ├── phpstan.neon
│   └── phpcs.xml
├── docker-compose.yml
└── README.md
```

## Main API Resources

The backend exposes endpoints for:

```text
/api/login
/api/logout
/api/auth

/api/administrator
/api/student
/api/course
```

Protected API routes require authentication and are additionally restricted according to administrator role.

## Running the Application

### Requirements

The recommended way to run the project is Docker.

You need:

- Docker
- Docker Compose

The PHP application targets PHP 8.3 or later.

### Start the environment

From the project root:

```bash
docker compose up -d --build
```

The application is available at:

```text
http://localhost:8090
```

### Stop the environment

```bash
docker compose down
```

## Running Tests

### PHPUnit

```bash
docker compose exec app php artisan test
```

### PHPStan

```bash
docker compose exec app vendor/bin/phpstan analyse
```

### PHP_CodeSniffer

```bash
docker compose exec app vendor/bin/phpcs
```

### PHP-CS-Fixer

```bash
docker compose exec app vendor/bin/php-cs-fixer fix --dry-run --diff
```

### Infection

```bash
docker compose exec app vendor/bin/infection \
    --threads=10 \
    --no-progress \
    --min-msi=80
```

### Cypress

Cypress tests cover authentication, authorization, navigation, students, courses, and administrator workflows.

Run the application first and then execute the Cypress test suite using the project's configured frontend environment.

## Testing Strategy

The test suite focuses on observable application behavior rather than implementation details.

Examples include:

- authentication success and failure
- role authorization
- CRUD operations
- validation failures
- duplicate email protection
- owner-account restrictions
- password hashing and preservation
- image upload, replacement, preservation, and deletion
- missing image-file edge cases
- student/course relationship synchronization
- missing-resource responses

Mutation testing is used as an additional quality gate to find weak assertions and untested behavioral branches.

## Security and Quality

The project applies several automated checks:

- Laravel request validation
- role-based access control
- password hashing
- protected authentication routes
- static analysis
- coding-standard validation
- mutation testing
- source-code security scanning
- Docker/infrastructure scanning

Secrets and environment-specific configuration are not intended to be committed to the repository.

## Modernization

This project is based on an older school-management application and demonstrates a modernization workflow.

The modernization includes:

```text
Legacy application
       ↓
Laravel backend
       ↓
Authentication and authorization
       ↓
Automated PHPUnit tests
       ↓
Cypress E2E tests
       ↓
Static analysis and code quality
       ↓
Security scanning
       ↓
Mutation testing
       ↓
GitHub Actions CI
       ↓
Dockerized application
```

Rather than rewriting only the application code, the project adds automated mechanisms for verifying that the modernized application continues to behave correctly.

## Purpose

This repository is a portfolio project demonstrating a combination of:

**PHP backend development + testing + security + DevOps automation**

It is intended to show how a legacy web application can be progressively modernized and surrounded by automated quality gates.

## License

See the repository license for details.