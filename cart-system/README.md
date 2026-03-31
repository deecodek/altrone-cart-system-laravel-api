# 🛒 Laravel Cart System API

A simple RESTful API for e-commerce built with Laravel 11.

## Quick Start

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

## Sample Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | Admin |
| customer@example.com | password | Customer |

## API Endpoints

### Auth
- `POST /api/register` - Register
- `POST /api/login` - Login

### Cart (Requires Auth)
- `GET /api/cart` - View cart
- `POST /api/cart/items` - Add item
- `PATCH /api/cart/items/{id}` - Update item
- `DELETE /api/cart/items/{id}` - Remove item

### Checkout (Requires Auth)
- `POST /api/checkout` - Process checkout
- `GET /api/orders` - List orders
- `GET /api/orders/{id}` - View order

### Products, Users, Vendors (Requires Auth)
- Full CRUD for each resource

## Example Usage

### 1. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@example.com","password":"password"}'
```

### 2. Add to Cart
```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"quantity":2}'
```

### 3. Checkout
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Testing

```bash
php artisan test
```

## Features

- ✅ User authentication (Sanctum)
- ✅ Shopping cart management
- ✅ Multi-vendor checkout
- ✅ Order tracking
- ✅ Payment processing
- ✅ Event notifications
- ✅ Request validation
- ✅ Comprehensive tests

## Project Structure

```
app/
├── Http/
│   ├── Controllers/     # Request handling
│   ├── Requests/        # Validation
│   └── Resources/       # API responses
├── Models/              # Database models
├── Services/            # Business logic
├── Events/              # Domain events
└── Listeners/           # Event handlers
```

## Logs

- **App logs:** `storage/logs/laravel.log`
- **Notifications:** `storage/logs/notifications.log`

## Tech Stack

- Laravel 11
- MySQL
- Laravel Sanctum (Auth)
- Pest PHP (Testing)

---

For full documentation, see the extended README.
