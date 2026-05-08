# Assessment Question Two

Laravel 11 API for products, categories, suppliers, and Sanctum authentication.

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL running locally

## Setup

1. Clone or open the project.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install frontend dependencies:
   ```bash
   npm install
   ```
4. Copy the environment file if needed:
   ```bash
   copy .env.example .env
   ```
5. Configure your `.env` database values:
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=ecommerce-yewrui`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=`
6. Generate the app key:
   ```bash
   php artisan key:generate
   ```
7. Run the migrations:
   ```bash
   php artisan migrate
   ```
8. Seed the database:
   ```bash
   php artisan db:seed
   ```
9. Start the app:
   ```bash
   php artisan serve
   ```

## Testing

Run the feature test suite:

```bash
vendor/bin/phpunit --testsuite Feature
```

## Seeders

The database seeder runs these 10 seeders:

1. `AdminUserSeeder`
2. `DemoUserSeeder`
3. `CategorySeeder`
4. `ClearanceCategorySeeder`
5. `SupplierSeeder`
6. `ExtraSupplierSeeder`
7. `ProductSeeder`
8. `LowStockProductSeeder`
9. `InactiveProductSeeder`
10. `ProductSupplierSeeder`

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
http://127.0.0.1:8000/api/documentation
```

## Swagger Reference Payloads

Use the Swagger UI for the full request payloads, example responses, and endpoint testing:

- Open: `http://127.0.0.1:8000/api/documentation`
- Base API path: `http://127.0.0.1:8000/api`
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
