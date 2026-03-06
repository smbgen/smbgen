# Contributing to smbgen

Thank you for considering contributing to smbgen! This guide will help you get started and ensure smooth collaboration.

---

## Table of Contents

- [Getting Started](#getting-started)
- [Code Standards](#code-standards)
- [Development Workflow](#development-workflow)
- [Pull Request Process](#pull-request-process)
- [Testing Requirements](#testing-requirements)
- [Code Review Guidelines](#code-review-guidelines)
- [Communication](#communication)

---

## Getting Started

### Prerequisites

Before contributing, ensure you have:
- ✅ Completed setup in [DEVELOPMENT_SETUP.md](DEVELOPMENT_SETUP.md)
- ✅ Read [MULTI_TENANCY_IMPLEMENTATION.md](MULTI_TENANCY_IMPLEMENTATION.md) for architecture understanding
- ✅ Access to GitHub repository and team communication channels
- ✅ Local development environment working

### First-Time Contributors

1. **Fork the repository** (if external contributor)
2. **Clone your fork** or the main repository
3. **Create a feature branch** from `main`
4. **Make your changes** following our standards
5. **Submit a pull request** for review

---

## Code Standards

### Laravel Best Practices

Follow the guidelines in [`.github/copilot-instructions.md`](.github/copilot-instructions.md), especially:

#### 1. **Do Things the Laravel Way**
- Use `php artisan make:*` commands to generate files
- Leverage Eloquent ORM over raw queries
- Use proper relationships with return type hints
- Avoid `DB::` facade; prefer `Model::query()`

```php
// ❌ Bad
$users = DB::table('users')->where('active', 1)->get();

// ✅ Good
$users = User::query()->where('active', true)->get();
```

#### 2. **PHP Standards**

**Type Declarations (Required):**
```php
// ✅ Always use explicit return types and parameter types
protected function isAccessible(User $user, ?string $path = null): bool
{
    return $user->canAccess($path);
}

// ❌ Never omit return types
protected function isAccessible($user, $path = null)
{
    return $user->canAccess($path);
}
```

**Constructor Property Promotion (PHP 8+):**
```php
// ✅ Use constructor promotion
public function __construct(
    public GitHub $github,
    protected StripeService $stripe
) {}

// ❌ Don't use old-style constructors
public function __construct(GitHub $github, StripeService $stripe)
{
    $this->github = $github;
    $this->stripe = $stripe;
}
```

**Curly Braces (Always):**
```php
// ✅ Always use braces, even for one-liners
if ($user->isAdmin()) {
    return true;
}

// ❌ Never omit braces
if ($user->isAdmin())
    return true;
```

**Enums (TitleCase Keys):**
```php
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case TenantOwner = 'tenant_owner';
    case TenantAdmin = 'tenant_admin';
}
```

#### 3. **Form Validation**

Always create Form Request classes:
```php
// ❌ Don't validate inline
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
    ]);
}

// ✅ Use Form Requests
public function store(StoreTenantRequest $request)
{
    $validated = $request->validated();
}
```

#### 4. **Configuration vs Environment**

```php
// ❌ Never use env() outside config files
if (env('FEATURE_BOOKING')) { ... }

// ✅ Always use config()
if (config('business.features.booking')) { ... }

// For multi-tenancy:
if (tenant_has_feature('booking')) { ... }
```

#### 5. **Database Best Practices**

**Use Eloquent Relationships:**
```php
// ✅ Eager load to prevent N+1
$tenants = Tenant::with('users', 'plan')->get();

// ✅ Use relationship methods with return types
public function users(): HasMany
{
    return $this->hasMany(User::class);
}
```

**Prevent N+1 Queries:**
```php
// ❌ N+1 query problem
foreach ($tenants as $tenant) {
    echo $tenant->plan->name; // Query per tenant!
}

// ✅ Eager load
$tenants = Tenant::with('plan')->get();
foreach ($tenants as $tenant) {
    echo $tenant->plan->name; // Single query
}
```

**Migrations:**
```php
// Always make migrations reversible
public function up(): void
{
    Schema::create('tenants', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('tenants');
}
```

---

### Frontend Standards

#### 1. **Tailwind CSS**

```html
<!-- ✅ Use Tailwind utility classes -->
<div class="flex items-center gap-4 p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold">Title</h2>
</div>

<!-- ✅ Use gap instead of margins for spacing -->
<div class="flex gap-8">
    <div>Item 1</div>
    <div>Item 2</div>
</div>

<!-- ❌ Don't use margins for flex spacing -->
<div class="flex">
    <div class="mr-8">Item 1</div>
    <div>Item 2</div>
</div>

<!-- ✅ Support dark mode if other components do -->
<div class="bg-white dark:bg-gray-800">
    <p class="text-gray-900 dark:text-gray-100">Text</p>
</div>
```

#### 2. **Livewire (v3)**

```php
// ✅ Use wire:model.live for real-time updates
<input wire:model.live="search" type="text">

// ✅ Use wire:loading for better UX
<button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</button>

// ✅ Add wire:key in loops
@foreach ($items as $item)
    <div wire:key="item-{{ $item->id }}">
        {{ $item->name }}
    </div>
@endforeach

// ✅ Use lifecycle hooks
public function mount(User $user): void
{
    $this->user = $user;
}

public function updatedSearch(): void
{
    $this->resetPage();
}
```

---

### Testing Standards

#### 1. **Test Everything You Change**

Every change must be tested. Write new tests or update existing ones.

```php
// tests/Feature/TenantManagementTest.php

use App\Models\Tenant;
use App\Models\User;
use function Pest\Laravel\{actingAs, get, post};

it('allows super admin to create tenant', function () {
    $admin = User::factory()->superAdmin()->create();
    
    actingAs($admin)
        ->post(route('super-admin.tenants.store'), [
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan_id' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    expect(Tenant::where('slug', 'acme')->exists())->toBeTrue();
});

it('prevents non-super-admin from accessing super admin panel', function () {
    $user = User::factory()->create(['role' => 'tenant_admin']);
    
    actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertForbidden();
});
```

#### 2. **Test Coverage Requirements**

- **All new features:** Must have tests
- **Bug fixes:** Add test that reproduces bug, then fix
- **Minimum coverage:** 80% for critical paths

#### 3. **Run Tests Before Committing**

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TenantManagementTest

# Run with coverage (requires xdebug)
php artisan test --coverage --min=80
```

---

## Development Workflow

### 1. Branch Naming Convention

```bash
# Feature branches
feature/tenant-management
feature/subscription-billing
feature/super-admin-dashboard

# Bug fixes
bugfix/booking-date-validation
bugfix/email-sending-failure

# Hotfixes (critical production issues)
hotfix/payment-webhook-error

# Refactoring
refactor/extract-stripe-service

# Documentation
docs/update-readme
docs/add-api-documentation
```

### 2. Commit Message Format

```bash
<type>(<scope>): <subject>

# Examples:
feat(tenancy): add tenant model and migration
fix(booking): validate date ranges correctly
refactor(auth): extract OAuth logic to service
test(billing): add subscription cancellation tests
docs(readme): update setup instructions
chore(deps): upgrade Laravel to 12.x
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `refactor`: Code improvement without behavior change
- `test`: Adding/updating tests
- `docs`: Documentation changes
- `chore`: Maintenance, dependencies, tooling
- `perf`: Performance improvements
- `style`: Code formatting (not CSS)

### 3. Daily Workflow

```bash
# Start of day: Sync with main
git checkout main
git pull origin main

# Create feature branch
git checkout -b feature/your-feature

# Make changes, test frequently
php artisan test
vendor/bin/pint

# Commit frequently with clear messages
git add .
git commit -m "feat(tenancy): add tenant model"

# Push to remote
git push origin feature/your-feature

# Create Pull Request
gh pr create --base main --title "Add tenant management"
```

---

## Pull Request Process

### 1. Before Creating PR

✅ **Checklist:**
- [ ] All tests pass locally (`php artisan test`)
- [ ] Code formatted with Pint (`vendor/bin/pint`)
- [ ] No debug statements (dd, dump, ray, console.log)
- [ ] No commented-out code blocks
- [ ] Environment variables added to `.env.example` (if new)
- [ ] Migration `down()` methods implemented
- [ ] Related documentation updated
- [ ] Self-review of code changes

### 2. PR Title & Description

**Title Format:**
```
[Type] Brief description (< 60 chars)

Examples:
[Feature] Add tenant management CRUD
[Fix] Correct booking date validation
[Refactor] Extract Stripe logic to service
```

**Description Template:**
```markdown
## Description
Brief summary of what this PR does.

## Related Issue
Closes #123 (if applicable)

## Type of Change
- [ ] New feature
- [ ] Bug fix
- [ ] Refactoring
- [ ] Documentation
- [ ] Testing

## Changes Made
- Added Tenant model with relationships
- Created tenant CRUD controllers
- Implemented tenant-aware middleware
- Added tests for tenant isolation

## Testing
- [ ] All tests pass locally
- [ ] New tests added for new functionality
- [ ] Tested manually on local environment
- [ ] Tested on staging (if deployed)

## Screenshots (if UI changes)
[Add screenshots here]

## Checklist
- [ ] Code follows project standards
- [ ] Self-reviewed code
- [ ] Commented complex logic
- [ ] Updated documentation
- [ ] No breaking changes (or documented if yes)

## Notes for Reviewers
Any specific areas you'd like reviewers to focus on.
```

### 3. PR Size Guidelines

**Keep PRs Small:**
- ✅ **Ideal:** < 400 lines changed
- ⚠️ **Acceptable:** 400-800 lines
- ❌ **Too Large:** > 800 lines (split into multiple PRs)

**Why?**
- Faster reviews
- Easier to spot issues
- Less likely to introduce bugs
- Can be merged/deployed faster

### 4. Review Process

**As Author:**
1. Create PR with clear description
2. Request review from team member(s)
3. Respond to feedback promptly
4. Make requested changes in new commits
5. Resolve conversations when addressed
6. **Do not merge your own PR** (unless emergency hotfix)

**As Reviewer:**
1. Review within 24 hours
2. Check code quality, logic, tests
3. Run tests locally if major change
4. Approve or request changes with clear feedback
5. Use "Request Changes" if blocking issues exist
6. Use "Comment" for non-blocking suggestions
7. Approve when satisfied

---

## Code Review Guidelines

### What to Look For

#### ✅ Approve If:
- Code follows project standards
- Tests are present and pass
- Logic is sound
- No obvious bugs
- Documentation updated (if needed)
- Small enough to review effectively

#### ❌ Request Changes If:
- Missing tests for new functionality
- Code doesn't follow standards
- Security vulnerabilities present
- Breaking changes without migration plan
- Missing error handling
- Database queries are inefficient (N+1 problems)

### Giving Feedback

**Be Constructive:**
```markdown
❌ "This code is bad"
✅ "Consider extracting this logic into a service class for better testability"

❌ "Wrong approach"
✅ "This could cause N+1 queries. Try eager loading the relationship instead"

❌ "Fix this"
✅ "Suggestion: Use a Form Request here for cleaner validation"
```

**Use Conventional Comments:**
- `nit:` Minor issue, non-blocking (e.g., formatting, naming)
- `suggestion:` Improvement idea, but current implementation is fine
- `question:` Need clarification on approach
- `issue:` Blocking problem that must be fixed
- `praise:` Highlight good work!

**Examples:**
```markdown
nit: Consider renaming `$x` to `$tenant` for clarity

suggestion: You could extract this into a helper method for reusability

question: Why did we choose this approach over using a job?

issue: This query will cause N+1 problems. Need to eager load relationships.

praise: Nice test coverage! This will prevent regressions.
```

---

## Testing Requirements

### Test Types Required

#### 1. **Unit Tests** (Fast, Isolated)
```php
// Test models, services, helpers in isolation

test('tenant can check if feature is enabled', function () {
    $plan = Plan::factory()->create([
        'features' => ['booking' => true, 'cms' => false],
    ]);
    
    $tenant = Tenant::factory()->create(['plan_id' => $plan->id]);
    
    expect($tenant->hasFeature('booking'))->toBeTrue();
    expect($tenant->hasFeature('cms'))->toBeFalse();
});
```

#### 2. **Feature Tests** (Full Stack)
```php
// Test controllers, routes, full request/response cycle

it('creates a new tenant', function () {
    $admin = User::factory()->superAdmin()->create();
    
    actingAs($admin)
        ->post(route('super-admin.tenants.store'), [
            'name' => 'Test Corp',
            'slug' => 'test',
            'plan_id' => Plan::first()->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
        
    expect(Tenant::where('slug', 'test')->exists())->toBeTrue();
});
```

#### 3. **Browser Tests** (UI Testing - Optional but Recommended)
```php
// Test critical user flows end-to-end

test('tenant owner can upgrade subscription', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($tenantOwner)
                ->visit('/admin/billing')
                ->clickLink('Upgrade Plan')
                ->select('plan', 'professional')
                ->press('Upgrade')
                ->assertSee('Subscription upgraded successfully');
    });
});
```

### Test Coverage Goals

| Component | Minimum Coverage |
|-----------|-----------------|
| Models | 90% |
| Services | 85% |
| Controllers | 80% |
| Middleware | 100% |
| Helpers | 90% |

### Test Data

**Use Factories:**
```php
// ✅ Use factories for test data
$tenant = Tenant::factory()->create();
$user = User::factory()->superAdmin()->create();

// ❌ Don't create manually
$tenant = new Tenant();
$tenant->name = 'Test';
$tenant->save();
```

**Create Factory States:**
```php
// database/factories/TenantFactory.php

public function active(): static
{
    return $this->state(fn (array $attributes) => [
        'status' => 'active',
    ]);
}

public function trial(): static
{
    return $this->state(fn (array $attributes) => [
        'status' => 'trial',
        'trial_ends_at' => now()->addDays(14),
    ]);
}

// Usage
$activeTenant = Tenant::factory()->active()->create();
$trialTenant = Tenant::factory()->trial()->create();
```

---

## Communication

### Channels

- **GitHub Issues:** Bug reports, feature requests
- **Pull Requests:** Code review discussions
- **Slack/Discord:** Quick questions, daily standup
- **Weekly Meetings:** Planning, blockers, demos

### Asking for Help

**Good Question:**
```markdown
## Context
I'm working on tenant isolation and need to ensure queries are scoped.

## What I've Tried
- Added `tenant_id` to User model
- Applied global scope in User::booted()

## Problem
Tests are failing because super admin users have null tenant_id

## Question
Should super admin users bypass the global scope? How do other models handle this?

## Code
[Link to branch/file]
```

**Less Helpful Question:**
```markdown
"Tenant stuff not working, help?"
```

### Reporting Bugs

**Use GitHub Issues with Template:**
```markdown
**Bug Description**
Clear description of the bug

**Steps to Reproduce**
1. Go to /admin/tenants
2. Click create
3. Submit form
4. See error

**Expected Behavior**
Tenant should be created

**Actual Behavior**
500 error with "tenant_id cannot be null"

**Environment**
- OS: Windows 11
- PHP: 8.4.15
- Laravel: 12.x
- Browser: Chrome 120

**Screenshots**
[Attach if helpful]

**Error Logs**
```
[stack trace]
```

---

## Recognition

### What Gets Recognized

- 🏆 High-quality PRs with tests
- 🏆 Helpful code reviews
- 🏆 Documentation improvements
- 🏆 Bug reports with reproduction steps
- 🏆 Helping other contributors

### Hall of Fame

Top contributors will be:
- Listed in README.md
- Mentioned in release notes
- Given special roles in team channels

---

## Code of Conduct

### Our Standards

**Be Respectful:**
- Welcome newcomers
- Be patient with questions
- Assume good intentions
- Give constructive feedback

**Be Professional:**
- Focus on the code, not the person
- Accept criticism gracefully
- Admit when you're wrong
- Learn from mistakes

**Be Collaborative:**
- Help others succeed
- Share knowledge
- Document your work
- Review PRs promptly

### Unacceptable Behavior

- Personal attacks or insults
- Harassment of any kind
- Publishing private information
- Deliberately introducing bugs
- Ignoring code review feedback repeatedly

---

## Getting Your First PR Merged

### Good First Issues

Look for issues labeled:
- `good first issue`
- `documentation`
- `help wanted`

### First PR Checklist

- [ ] Read DEVELOPMENT_SETUP.md
- [ ] Set up local environment
- [ ] Create feature branch
- [ ] Make one small, focused change
- [ ] Add tests
- [ ] Run `php artisan test` and `vendor/bin/pint`
- [ ] Create PR with clear description
- [ ] Respond to review feedback
- [ ] Celebrate when merged! 🎉

---

## Questions?

- **Documentation:** Check [DEVELOPMENT_SETUP.md](DEVELOPMENT_SETUP.md) and [MULTI_TENANCY_IMPLEMENTATION.md](MULTI_TENANCY_IMPLEMENTATION.md)
- **Technical Questions:** Ask in Slack/Discord #dev channel
- **Process Questions:** Ask the maintainers

---

**Thank you for contributing to smbgen! Every contribution makes a difference.** ❤️

---

**Last Updated:** December 28, 2025  
**Maintained By:** Development Team
