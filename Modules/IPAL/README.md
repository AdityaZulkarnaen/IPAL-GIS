# Module IPAL (Instalasi Pengolahan Air Limbah)

Module ini terpisah dari source code utama (modular) untuk pengembangan fitur IPAL.

---

## Struktur Folder

```
Modules/IPAL/
├── Config/
│   └── config.php              # Konfigurasi module IPAL
├── Database/
│   ├── Migrations/             # Migration khusus IPAL (prefix tabel: ipal_)
│   └── Seeders/                # Seeder khusus IPAL
├── Http/
│   ├── Controllers/            # Controller IPAL
│   ├── Middleware/              # Middleware khusus IPAL
│   └── Requests/               # Form Request validation IPAL
├── Models/                     # Model Eloquent IPAL
├── Providers/
│   ├── IPALServiceProvider.php # Service Provider utama
│   └── EventServiceProvider.php# Event listener IPAL
├── Resources/
│   └── views/                  # Blade template IPAL
│       ├── layouts/
│       │   ├── main.blade.php  # Layout utama IPAL
│       │   └── partials/
│       │       └── sidebar.blade.php  # Sidebar menu IPAL
│       └── dashboard/
│           └── index.blade.php # Halaman dashboard
├── Routes/
│   ├── web.php                 # Route web IPAL (prefix: /ipal)
│   └── api.php                 # Route API IPAL (prefix: /api/ipal)
└── README.md                   # Dokumentasi ini
```

---

## Cara Kerja

1. **Service Provider** (`IPALServiceProvider`) mendaftarkan routes, views, migrations, dan config secara otomatis.
2. Semua route web IPAL di-prefix dengan `/ipal` dan name prefix `ipal.`
3. Semua route API IPAL di-prefix dengan `/api/ipal` dan name prefix `api.ipal.`
4. Views menggunakan namespace `ipal::` (contoh: `return view('ipal::dashboard.index')`)
5. Semua tabel database menggunakan prefix `ipal_` agar tidak bentrok dengan tabel utama.

---

## URL Akses

| Halaman | URL | Route Name |
|---------|-----|------------|
| Dashboard IPAL | `/ipal` atau `/ipal/dashboard` | `ipal.dashboard` |

---

## Perintah Artisan yang Relevan

```bash
# Setelah clone/pull, jalankan:
composer dump-autoload

# Jalankan migration IPAL:
php artisan migrate

# Jalankan seeder IPAL:
php artisan db:seed --class=Modules\\IPAL\\Database\\Seeders\\IPALDatabaseSeeder

# Publish config IPAL (opsional):
php artisan vendor:publish --tag=ipal-config

# Publish views IPAL (opsional, jika ingin override):
php artisan vendor:publish --tag=ipal-views
```

---

## Cara Menambah Fitur Baru

### 1. Tambah Route
Edit `Modules/IPAL/Routes/web.php`:
```php
Route::resource('pengolahan', PengolahanController::class);
```

### 2. Tambah Controller
Buat file baru di `Modules/IPAL/Http/Controllers/`:
```php
<?php
namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonfigurasiModel;

class PengolahanController extends Controller
{
    public function index()
    {
        $data_konfig = KonfigurasiModel::first();
        $service = ['data_konfig' => $data_konfig];
        
        return view('ipal::pengolahan.index', compact('service', ...));
    }
}
```

### 3. Tambah Model
Buat file di `Modules/IPAL/Models/` dengan prefix tabel `ipal_`:
```php
<?php
namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;

class PengolahanModel extends Model
{
    protected $table = 'ipal_pengolahan';
    protected $fillable = ['nama', 'deskripsi'];
}
```

### 4. Tambah Migration
Buat file di `Modules/IPAL/Database/Migrations/`:
```php
Schema::create('ipal_pengolahan', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->timestamps();
});
```

### 5. Tambah View
Buat file blade di `Modules/IPAL/Resources/views/`:
```blade
@extends('ipal::layouts.main')
@section('content')
    {{-- Konten di sini --}}
@endsection
```

### 6. Tambah Menu Sidebar
Edit `Modules/IPAL/Resources/views/layouts/partials/sidebar.blade.php`:
```blade
<a href="{{ route('ipal.pengolahan.index') }}" class="menu-item ...">
    <span class="menu-link">
        <span class="menu-icon">...</span>
        <span class="menu-title">Pengolahan</span>
    </span>
</a>
```

---

## Cara Menonaktifkan Module IPAL

### Opsi 1: Via .env
```env
IPAL_MODULE_ENABLED=false
```

### Opsi 2: Comment Service Provider
Di `config/app.php`, comment baris:
```php
// Modules\IPAL\Providers\IPALServiceProvider::class,
```

---

## Konvensi Penamaan

| Item | Konvensi | Contoh |
|------|----------|--------|
| Tabel Database | prefix `ipal_` | `ipal_data`, `ipal_pengolahan` |
| Route Name | prefix `ipal.` | `ipal.dashboard`, `ipal.pengolahan.index` |
| URL | prefix `/ipal` | `/ipal/dashboard`, `/ipal/pengolahan` |
| View Namespace | `ipal::` | `ipal::dashboard.index` |
| Namespace PHP | `Modules\IPAL\` | `Modules\IPAL\Http\Controllers\` |

---

## Catatan Penting

- **JANGAN** mengubah file di luar folder `Modules/IPAL/` kecuali benar-benar diperlukan.
- Jika perlu menggunakan model/service dari app utama, cukup `use App\Models\NamaModel`.
- Semua aset tema (CSS/JS Metronic) sudah tersedia melalui layout.
- Gunakan `@stack('ipal-styles')` dan `@stack('ipal-scripts')` untuk menambahkan CSS/JS khusus per halaman.
