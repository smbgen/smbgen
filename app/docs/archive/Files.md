
# 📁 Shared Documents Feature (Client ↔ Admin)

## ✅ Feature Breakdown

### Admin Dashboard
- Accessible at `/admin/clients/{client}/files`
- View **client-specific files**
- Upload, delete, or download any file
- (Optional) Add tags, notes, or visibility restrictions

### Client Dashboard
- "My Documents" navigation card
- List of downloadable files
- Upload button with file type/size constraints
- (Optional) Read-only access or delete rights

---

## 🧱 Technical Architecture

### Migration
```bash
php artisan make:model ClientFile -m
```

```php
$table->id();
$table->foreignId('client_id')->constrained()->onDelete('cascade');
$table->string('filename');
$table->string('original_name');
$table->string('path');
$table->string('uploaded_by')->default('admin'); // or 'client'
$table->timestamps();
```

---

### Storage
```bash
php artisan storage:link
```

Files stored at:
```
storage/app/client_files/{client_id}
```

---

### Routes
```php
// Client Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [ClientFileController::class, 'index'])->name('client.files');
    Route::post('/documents/upload', [ClientFileController::class, 'store'])->name('client.files.upload');
});

// Admin Routes
Route::middleware(['auth', 'companyAdministrator'])->prefix('admin')->group(function () {
    Route::get('/clients/{client}/files', [AdminClientFileController::class, 'index'])->name('admin.client.files');
    Route::post('/clients/{client}/files', [AdminClientFileController::class, 'store'])->name('admin.client.files.upload');
    Route::delete('/clients/{client}/files/{file}', [AdminClientFileController::class, 'destroy'])->name('admin.client.files.destroy');
});
```

---

### Views
- Admin: Full file manager per client
- Client: Lightweight "My Documents" section

---

### Controllers
- `ClientFileController` → Handles client upload/view
- `AdminClientFileController` → Handles admin file access per client

**Storage usage:**
```php
Storage::putFileAs("client_files/{$client->id}", $file, $filename);
```

**Access Control Tip:**  
Ensure users only access their own files using policies or scoped queries.

---

## 🌱 Ready for Implementation
Let me know if you want blade views and controllers next.
