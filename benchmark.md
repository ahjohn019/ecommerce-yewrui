# Product Cache Benchmark

This project includes an Artisan command that benchmarks the product index with and without cache.

## Command

```bash
php artisan products:benchmark
```

You can also pass filters that match the product index API:

```bash
php artisan products:benchmark --search=mouse --category_id=1 --per-page=15
```

Inside Docker:

```bash
docker compose exec app php artisan products:benchmark
```

## What it measures

The command compares the same product list query twice:

1. `before index & cache`
2. `after index & cache`

The first run happens after the cache namespace is bumped, so the cache entry is cold.
The second run uses the same filters and page size, so it should reuse the cached result.

## What the output means

Each line shows:

- elapsed time in milliseconds
- how many product-related SQL queries ran
- how many cache-related SQL queries ran

Example:

```text
Product index cache:
  before index & cache: 265.15 ms, product queries: 8, cache queries: 0
  after index & cache: 1.94 ms, product queries: 0, cache queries: 1
```

In the Laravel log you will also see entries like:

```text
[debug] Product cache miss {"key":"products:1:paginate:...","ttl":600,"elapsed_ms":12.34}
[debug] Product cache hit {"key":"products:1:paginate:...","ttl":600,"elapsed_ms":1.02}
```

## Why the numbers differ

- `before index & cache` is slower because Laravel has to build the product list and store it in cache.
- `after index & cache` is faster because the same query result is served from cache.
- Product query counts should drop a lot on the second run.
- Cache query counts may still appear because the cache store itself can read from a backend.

## Related files

- [`app/Console/Commands/BenchmarkProductCache.php`](C:/laragon/www/assessment_question_two/app/Console/Commands/BenchmarkProductCache.php)
- [`app/Services/ProductService.php`](C:/laragon/www/assessment_question_two/app/Services/ProductService.php)
- [`app/Services/CacheService.php`](C:/laragon/www/assessment_question_two/app/Services/CacheService.php)
- [`app/Http/Controllers/Api/ProductController.php`](C:/laragon/www/assessment_question_two/app/Http/Controllers/Api/ProductController.php)
