# Perseus - Support Ticket & Knowledgebase

Aplikasi support ticket dan knowledgebase yang dibangun dengan Laravel 12, React 19, dan Filament v3.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.4 |
| Frontend | React 19, TypeScript, Inertia.js v2 |
| Styling | Tailwind CSS v4 |
| Admin Panel | Filament v3 |
| Database | PostgreSQL |
| Server | Laravel Octane (FrankenPHP) |
| Auth | Laravel Fortify (2FA support) |
| Roles | Spatie Laravel Permission |
| Testing | Pest v4 |

## Fitur

### Support Ticket System
- Buat dan kelola tiket support
- Prioritas: Low, Medium, High, Urgent
- Status: Open, In Progress, Waiting Reply, Resolved, Closed
- Department assignment dan agent assignment
- Kategori tiket per department
- Reply dengan file attachment (jpg, png, pdf, doc, zip, dll)
- Auto-generated reference number (TKT-xxx)

### Knowledgebase
- Kategori artikel dengan slug URL
- Full-text search (PostgreSQL)
- Draft/Published workflow
- View counter
- Rich text content

### Admin Panel (`/admin`)
- Dashboard dengan statistik overview
- Manage tiket (assign, reply, ubah status/prioritas)
- Manage department dan kategori
- CRUD knowledgebase artikel dan kategori
- User management dengan role-based access
- Roles: Super Admin, Agent

### Customer Portal
- Daftar tiket milik user
- Buat tiket baru dengan attachment
- Lihat detail dan reply tiket
- Browse knowledgebase per kategori
- Search artikel knowledgebase

## Requirements

- PHP 8.2+
- PostgreSQL 15+
- Node.js 20+
- Composer 2+
- [Laravel Herd](https://herd.laravel.com/) (recommended)

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/frankyso/perseus.git
cd perseus
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=perseus
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database setup

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 5. Build frontend

```bash
npm run build
```

### 6. Jalankan aplikasi

Jika menggunakan Laravel Herd, aplikasi otomatis tersedia di `http://perseus.test`.

Atau jalankan secara manual:

```bash
# Development (dengan hot reload)
composer run dev

# Dengan Octane
php artisan octane:start --server=frankenphp --watch
```

## Default Accounts

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@example.com | password |
| Agent | agent@example.com | password |

## Testing

```bash
# Jalankan semua test
php artisan test

# Compact output
php artisan test --compact

# Filter specific test
php artisan test --filter=TicketTest
```

**Test coverage:** 79 tests, 277 assertions

## Development

```bash
# Start dev server (Laravel + Vite + Queue + Logs)
composer run dev

# Lint PHP
vendor/bin/pint

# Lint & format JS/TS
npm run lint
npm run format

# Type check
npm run types:check

# Run full CI check
composer run ci:check
```

## Struktur Project

```
app/
├── Filament/           # Admin panel resources & widgets
│   ├── Resources/      # Ticket, Department, KB, User resources
│   └── Widgets/        # Stats overview widget
├── Http/
│   ├── Controllers/
│   │   ├── Knowledgebase/  # Public KB controller
│   │   ├── Settings/       # User settings
│   │   └── Ticket/         # Customer ticket controller
│   └── Requests/
│       └── Ticket/     # Form request validation
├── Models/             # Eloquent models
└── Providers/
    └── Filament/       # Admin panel provider

resources/js/
├── components/         # Reusable React components
├── layouts/            # App & Auth layouts
├── pages/
│   ├── tickets/        # Ticket pages (index, create, show)
│   └── knowledgebase/  # KB pages (index, category, show, search)
└── types/              # TypeScript type definitions

tests/Feature/
├── Admin/              # Admin access tests
├── Knowledgebase/      # KB feature tests
├── Models/             # Model relationship tests
└── Ticket/             # Ticket CRUD & reply tests
```

## License

MIT
