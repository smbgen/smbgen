# Contributing to smbgen

Thank you for helping make smbgen better.
If you want to contribute code, documentation, or ideas, this guide will help you get started.

## Before You Start

- Review the existing issue templates:
  - `.github/ISSUE_TEMPLATE/bug_report.yml`
  - `.github/ISSUE_TEMPLATE/feature_request.yml`
  - `.github/ISSUE_TEMPLATE/improvement.yml`
- Search existing issues and pull requests to avoid duplicates.
- Make sure your changes follow the project style and conventions.

## Local Setup

1. Fork the repository and clone your fork.
2. Install PHP and Node dependencies:

```bash
composer install
npm install
```

3. Copy environment settings:

```bash
cp .env.example .env
php artisan key:generate
```

4. Create the default SQLite database:

```bash
touch database/database.sqlite
php artisan migrate
```

## Branches & Commits

- Create a branch with a descriptive name:
  - `feature/add-client-portal`
  - `fix/billing-button`
  - `docs/update-readme`
- Use clear, conventional commit messages:
  - `feat: add billing card to dashboard`
  - `fix: correct invoice label`
  - `docs: update contribution instructions`

## Code Quality

- Run tests before submitting a PR:

```bash
php artisan test
```

- Format code with Pint:

```bash
vendor/bin/pint
```

- Keep changes focused and small when possible.

## Pull Requests

When opening a PR:

- Provide a short summary of the change.
- Link related issues or feature requests.
- Describe any manual testing performed.
- Ensure the PR passes tests and code formatting checks.

## Issue Reporting

Use the issue templates to file a bug report, feature request, or improvement idea. Include:

- What you expected to happen
- What actually happened
- Steps to reproduce
- Environment details, if relevant

## Security Issues

If you believe you have found a security vulnerability, please see `.github/SECURITY.md` for reporting instructions.

## Code of Conduct

All contributors must follow the project's code of conduct: see `.github/CODE_OF_CONDUCT.md`.
