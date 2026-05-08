# Assessment Question Two

Laravel 11 API for products, categories, suppliers, and Sanctum authentication.

## Requirements

- Docker and Docker Compose
- Node.js and npm
- Composer if you want to run Laravel locally outside Docker

## Quick Start

1. Add this to your hosts file:
   ```text
   127.0.0.1 assessment_question_two.com
   ```
2. Copy the environment file if needed:
   ```bash
   copy .env.example .env
   ```
3. Start the containers:
   ```bash
   docker compose up --build
   ```
4. Run migrations and seed data:
   ```bash
   docker compose exec app php artisan migrate --seed
   ```
5. If you need a fresh reset:
   ```bash
   docker compose exec app php artisan migrate:fresh --seed
   ```

The app is available at:

- API: `http://assessment_question_two.com/`
- Swagger docs: `http://assessment_question_two.com/api/documentation`
- Vite: `http://localhost:5173`

## Local Setup

If you prefer to run the app without Docker:

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Install frontend dependencies:
   ```bash
   npm install
   ```
3. Configure your `.env` database values for your local MySQL setup.
4. Generate the app key:
   ```bash
   php artisan key:generate
   ```
5. Run the migrations:
   ```bash
   php artisan migrate
   ```
6. Seed the database:
   ```bash
   php artisan db:seed
   ```
7. Start the app:
   ```bash
   php artisan serve
   ```

## Docker Notes

- Docker uses MySQL from `docker-compose.yml`.
- `APP_URL` should stay set to `http://assessment_question_two.com`.
- Swagger docs use the same host as the app URL.
- Docker sets `CACHE_STORE=redis` so cache reads stay fast and do not depend on MySQL.
- You can keep `.env` aligned with Docker by setting `CACHE_STORE=redis` locally too.

## Testing

Run the feature test suite:

```bash
vendor/bin/phpunit --testsuite Feature
```

Run all tests:

```bash
vendor/bin/phpunit
```

If you are running inside Docker:

```bash
docker compose exec app vendor/bin/phpunit
```

## Seeders

The database seeder runs these 11 seeders:

1. `AdminUserSeeder`
2. `DemoUserSeeder`
3. `CategorySeeder`
4. `ClearanceCategorySeeder`
5. `SupplierSeeder`
6. `ExtraSupplierSeeder`
7. `ProductSeeder`
8. `BenchmarkProductSeeder`
9. `LowStockProductSeeder`
10. `InactiveProductSeeder`
11. `ProductSupplierSeeder`

## Default Auth Data

- Admin email: `admin@example.com`
- Demo user email: `demo@example.com`
- Use the password you configure locally when creating test users

## API Endpoints

Auth:

- `POST /api/register`
- `POST /api/login`
- `GET /api/me`
- `POST /api/logout`

Products:

- `GET /api/products`
- `POST /api/products`
- `GET /api/products/{product}`
- `PUT /api/products/{product}`
- `PATCH /api/products/{product}`
- `DELETE /api/products/{product}`

## Swagger Docs

Open the interactive API documentation here:

```bash
http://assessment_question_two.com/api/documentation
```

## Swagger Payloads

Use the Swagger UI for request payloads, example responses, and endpoint testing.

- Base API path: `http://assessment_question_two.com/api`
- Auth header format: `Authorization: Bearer YOUR_TOKEN_HERE`

The Swagger docs include:

- register and login payloads
- authenticated profile and logout examples
- product list filters and pagination
- create, update, show, and delete product examples
- success response envelopes with `success` and `code`

If you want a fast copy-paste reference, these are the main payloads:

### Register

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "YOUR_PASSWORD",
  "password_confirmation": "YOUR_PASSWORD"
}
```

### Login

```json
{
  "email": "john@example.com",
  "password": "YOUR_PASSWORD"
}
```

### Create Product

```json
{
  "category_id": 1,
  "supplier_ids": [1, 2],
  "name": "Gaming Mouse",
  "slug": "gaming-mouse",
  "sku": "MOU-001",
  "description": "Wireless gaming mouse",
  "price": 99.9,
  "sale_price": 79.9,
  "stock_quantity": 20,
  "image_path": null,
  "is_active": true
}
```

## Product Filters

Use these query parameters on `GET /api/products`:

- `search`
- `category_id`
- `min_price`
- `max_price`
- `stock_level` with `in_stock`, `out_of_stock`, or `low_stock`
- `low_stock_threshold`
- `per_page`

## Notes

- Product deletes are soft deletes.
- API responses use the shared `success` and `code` envelope.
- Validation uses Form Request classes.
- Response formatting uses API Resources.
