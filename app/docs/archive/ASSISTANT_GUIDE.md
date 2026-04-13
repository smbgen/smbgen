# 🤖 AI Assistant Guide - SMBGen

## 🖥️ **Environment Setup**

### **Operating System**: Windows 10/11
### **Development Environment**: Herd (Laravel Herd)
### **Shell**: Git Bash with custom bash profile

### **Key Paths**:
- **PHP**: `C:/Users/alexr/.config/herd/bin/php.bat`
- **Composer**: `C:/Users/alexr/.config/herd/bin/composer.bat`
- **Node/NPM**: Standard Windows installation
- **Project Root**: `C:/Users/alexr/Documents/GitHub/smbgen`

### **Bash Profile Configuration**:
```bash
# ~/.bash_profile includes these aliases:
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"
alias composer="composer.bat"
```

### **Cursor/IDE Setup**:
**Settings.json configuration** (swap `php84` to `php85` if you switch PHP versions in Herd):
```json
{
  "php.validate.executablePath": "C:\\Users\\alexr\\.config\\herd\\bin\\php84\\php.exe",
  "intelephense.environment.phpExecutablePath": "C:\\Users\\alexr\\.config\\herd\\bin\\php84\\php.exe",
  "terminal.integrated.env.windows": {
    "PATH": "C:\\Users\\alexr\\.config\\herd\\bin;%PATH%"
  }
}
```

## 🚀 **Project Overview**

### **Framework**: Laravel 12.x
### **Frontend**: Tailwind CSS v3.4.0 + Livewire
### **Database**: MySQL (via Herd)
### **Local Domain**: `smbgen.test`

### **Key Features**:
- Multi-role authentication (company_administrator, client, user)
- Client portal with appointments, messages, cyber audit
- Admin dashboard with lead management
- Google OAuth integration
- AI-powered cyber audit assistant

## � **QuickBooks Billing Integration**

### **Status**: Implemented (Feature Flag: `FEATURE_BILLING=false`)
### **Integration**: QuickBooks Online API
### **Documentation**: See `QUICKBOOKS_BILLING.md`

### **Features**:
- Invoice creation and management
- QuickBooks sync (push invoices to QB)
- Customer auto-creation by email
- Payment link generation
- Email integration with QB payment URLs

### **Key Components**:
- **Service**: `App\Services\QuickBooksService`
- **Controller**: `App\Http\Controllers\Admin\AdminBillingController`
- **Model**: `App\Models\Invoice` (with QB fields)
- **Routes**: `/admin/billing/*`, `/admin/quickbooks/*`
- **Component**: `resources/views/components/dashboard/quickbooks-integration.blade.php`

### **Environment Setup**:
```bash
FEATURE_BILLING=false              # Set to true to enable
QUICKBOOKS_CLIENT_ID=your-id
QUICKBOOKS_CLIENT_SECRET=your-secret
QUICKBOOKS_REDIRECT_URI="${APP_URL}/admin/quickbooks/callback"
QUICKBOOKS_ENVIRONMENT=sandbox     # or 'production'
```

### **Removed**: Stripe integration (deprecated in favor of QuickBooks)

## �🛠️ **Common Commands**

### **PHP/Composer** (Use these paths):
```bash
"/c/Users/alexr/.config/herd/bin/php.bat" artisan [command]
"/c/Users/alexr/.config/herd/bin/composer.bat" [command]
```

### **Node/NPM** (Standard):
```bash
npm install
npm run build
npm run dev
```

### **Laravel Commands**:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan migrate
php artisan tinker
```

### **Testing**:
```bash
# Run Pest tests with Herd
"C:\Users\alexr\.config\herd\bin\php.bat" vendor\bin\pest -q

# Or with bash aliases
php vendor/bin/pest -q
```

## 📁 **Project Structure**

### **Key Directories**:
- `app/Http/Controllers/Admin/` - Admin controllers
- `app/Http/Controllers/Auth/` - Authentication controllers
- `resources/views/admin/` - Admin views
- `resources/views/auth/` - Authentication views
- `resources/css/` - Tailwind CSS
- `public/build/` - Compiled assets

### **Important Files**:
- `bootstrap/app.php` - Laravel 12 service providers
- `tailwind.config.js` - Tailwind configuration
- `vite.config.js` - Vite build configuration
- `postcss.config.js` - PostCSS configuration

## 🔐 **Authentication System**

### **User Roles**:
- `company_administrator` - Full admin access
- `client` - Client portal access
- `user` - Basic user access

### **Role-Based Routing**:
- Admin users → `/admin/dashboard`
- Client users → `/dashboard`
- Regular users → `/dashboard`

### **Middleware**:
- `CompanyAdministrator` - Checks for `company_administrator` role

## 🎨 **Frontend Framework**

### **Styling**:
- **Primary**: Tailwind CSS (dark theme)
- **Components**: Livewire
- **Icons**: Emoji icons (👤, 📧, 🔒, etc.)
- **Buttons**: Custom classes (`btn-primary`, `btn-secondary`, `btn-success`, `btn-danger`)

### **Custom CSS Classes** (in `resources/css/app.css`):
```css
.btn-primary { /* Blue button */ }
.btn-secondary { /* Gray button */ }
.btn-success { /* Green button */ }
.btn-danger { /* Red button */ }
.card { /* Dark card styling */ }
```

## 🚨 **Common Issues & Solutions**

### **500 Errors**:
1. Check `storage/logs/laravel.log`
2. Clear all caches: `php artisan config:clear && php artisan cache:clear`
3. Rebuild assets: `npm run build`
4. Check permissions: `chmod -R 755 storage bootstrap/cache`

### **Asset Loading Issues**:
- Local development uses `@vite` directive
- Production uses direct asset loading from `public/build/`
- Fallback mechanism in layouts for Herd environment

### **Authentication Issues**:
- Ensure `company_administrator` role exists in database
- Check `bootstrap/app.php` for auth service providers
- Verify middleware configuration

### **Build Issues**:
- Use Tailwind CSS v3.4.0 (not v4)
- Ensure `postcss.config.js` exists
- Check `vite.config.js` configuration

## 📝 **Development Workflow**

### **Before Making Changes**:
1. Check current git status
2. Ensure assets are built: `npm run build`
3. Clear caches if needed

### **After Making Changes**:
1. Test locally on `smbgen.test`
2. Build assets: `npm run build`
3. Commit with descriptive message
4. Push to repository

### **VPS Deployment**:
1. `git pull`
2. `composer install --no-dev --optimize-autoloader`
3. `php artisan config:clear && php artisan cache:clear`
4. `npm install && npm run build`
5. Check permissions and logs

## 🔍 **Debugging Tips**

### **Check Laravel Logs**:
```bash
tail -50 storage/logs/laravel.log
grep -A 5 -B 5 "ERROR\|Exception\|Fatal" storage/logs/laravel.log
```

### **Check Routes**:
```bash
php artisan route:list
```

### **Check Database**:
```bash
php artisan tinker --execute="echo 'Users:'; \App\Models\User::all(['id', 'name', 'email', 'role']);"
```

### **Check Assets**:
```bash
ls -la public/build/
cat public/build/manifest.json
```

## 🎯 **Current Status**

### **✅ Completed**:
- Bootstrap to Tailwind CSS migration
- Role-based authentication system
- Admin dashboard conversion
- Client portal styling
- Profile management forms
- Cyber audit interface
- Appointments and messages views

### **🔄 In Progress**:
- Client management views (create/edit/index)
- Additional page conversions

### **📋 TODO**:
- Review remaining Bootstrap classes
- Test all functionality
- Optimize performance
- Add additional features

## 💡 **AI Assistant Guidelines**

### **When Making Changes**:
1. **Always use the correct PHP/Composer paths**
2. **Check for existing Bootstrap classes before converting**
3. **Maintain dark theme consistency**
4. **Use emoji icons instead of Bootstrap icons**
5. **Test locally before committing**
6. **Provide clear commit messages**

### **When Debugging**:
1. **Check Laravel logs first**
2. **Verify route definitions**
3. **Check database connections**
4. **Ensure assets are built**
5. **Clear caches if needed**

### **When Adding Features**:
1. **Follow existing patterns**
2. **Use Tailwind CSS classes**
3. **Maintain role-based access**
4. **Add proper validation**
5. **Include error handling**

---

**Last Updated**: December 2024
**Version**: 1.0
**Maintainer**: Alex Ramsey
