# Contributing to ClientBridge

Thank you for your interest in contributing to ClientBridge! We welcome contributions from the community.

## 🚀 Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/clientbridge-laravel.git`
3. Create a new branch: `git checkout -b feature/your-feature-name`
4. Make your changes
5. Test thoroughly
6. Commit with clear messages
7. Push to your fork
8. Open a Pull Request

## 📋 Development Guidelines

### Code Style

- Follow [Laravel conventions](https://laravel.com/docs/contributions#coding-style)
- Use Laravel Pint for formatting: `vendor/bin/pint`
- Write descriptive variable and method names
- Add comments for complex logic

### Testing

- Write tests for new features using Pest PHP
- Ensure all tests pass: `php artisan test`
- Aim for high test coverage
- Test both happy paths and edge cases

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/YourTest.php

# Run with filter
php artisan test --filter=testMethodName
```

### Commits

Use clear, descriptive commit messages:

```
Add user notification preferences

- Add migration for notification_preferences table
- Create NotificationPreference model
- Add UI for managing preferences
- Include tests for preference updates
```

### Pull Requests

- Keep PRs focused on a single feature/fix
- Reference any related issues
- Include screenshots for UI changes
- Update documentation as needed
- Ensure CI/CD checks pass

## 🐛 Reporting Bugs

When reporting bugs, please include:

- Laravel version
- PHP version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Error messages/logs

## 💡 Suggesting Features

We welcome feature suggestions! Please:

- Check if the feature already exists or is planned
- Explain the use case
- Describe the expected behavior
- Consider implementation complexity

## 📝 Documentation

- Update README.md for major changes
- Document new features in code comments
- Update .env.example for new config options
- Keep the ARCHITECTURE.md file current

## 🔒 Security

If you discover a security vulnerability, please email security@clientbridge.app instead of opening a public issue.

## 📜 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## ❓ Questions

Feel free to open an issue for questions or join our community discussions.

---

Thank you for contributing! 🎉
