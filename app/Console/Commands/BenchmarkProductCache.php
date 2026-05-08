<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BenchmarkProductCache extends Command
{
    protected $signature = 'products:benchmark {--search=} {--category_id=} {--min_price=} {--max_price=} {--stock_level=} {--low_stock_threshold=} {--per-page=15}';

    protected $description = 'Benchmark cached and uncached product index queries';

    public function __construct(
        private readonly CacheService $cache,
        private readonly ProductService $products
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $filters = $this->resolveFilters();
        $perPage = $this->resolvePerPage();
        $this->info('Benchmarking product index with per-page '.$perPage);

        if ($filters === []) {
            $this->line('No filters supplied. Benchmarking the default index query.');
        } else {
            $this->line('Filters: '.json_encode($filters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        $this->cache->bumpVersion();
        [$beforeCache, $beforeCacheMs, $beforeCacheQueries] = $this->benchmark(fn () => $this->products->paginate($filters, $perPage));
        [$afterCache, $afterCacheMs, $afterCacheQueries] = $this->benchmark(fn () => $this->products->paginate($filters, $perPage));

        $this->newLine();
        $this->outputBenchmark('Product index cache', [
            'before index & cache' => [
                'ms' => $beforeCacheMs,
                'product_queries' => $beforeCacheQueries['product'],
                'cache_queries' => $beforeCacheQueries['cache'],
            ],
            'after index & cache' => [
                'ms' => $afterCacheMs,
                'product_queries' => $afterCacheQueries['product'],
                'cache_queries' => $afterCacheQueries['cache'],
            ],
        ]);

        if ($beforeCache->total() !== $afterCache->total()) {
            $this->warn('Index results did not match.');
        }

        return self::SUCCESS;
    }

    private function resolveFilters(): array
    {
        $filters = [];
        $searchField = ['search', 'category_id', 'min_price', 'max_price', 'stock_level', 'low_stock_threshold'];

        foreach ($searchField as $key) {
            $value = $this->option($key);

            if (!empty($value)) {
                $filters[$key] = is_numeric($value) ? $value + 0 : $value;
            }
        }

        return $filters;
    }

    private function resolvePerPage(): int
    {
        return max(1, (int) $this->option('per-page'));
    }

    private function benchmark(callable $callback): array
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $startedAt = hrtime(true);
        $result = $callback();
        $elapsedMs = (hrtime(true) - $startedAt) / 1_000_000;
        $queryCounts = [
            'product' => 0,
            'cache' => 0,
        ];

        foreach (DB::getQueryLog() as $query) {
            $bucket = $this->classifyQuery($query['query'] ?? '');

            if ($bucket !== null) {
                $queryCounts[$bucket]++;
            }
        }

        DB::disableQueryLog();

        return [$result, $elapsedMs, $queryCounts];
    }

    private function outputBenchmark(string $title, array $timings): void
    {
        $this->line($title.':');

        foreach ($timings as $label => $metrics) {
            $this->line('  '.$label.': '.number_format($metrics['ms'], 2).' ms, product queries: '.$metrics['product_queries'].', cache queries: '.$metrics['cache_queries']);
        }
    }

    private function classifyQuery(string $sql): ?string
    {
        $sql = strtolower($sql);

        if (
            str_contains($sql, ' from `products`')
            || str_contains($sql, ' from "products"')
            || str_contains($sql, ' join `products`')
            || str_contains($sql, ' join "products"')
            || str_contains($sql, ' from `categories`')
            || str_contains($sql, ' from "categories"')
            || str_contains($sql, ' join `categories`')
            || str_contains($sql, ' join "categories"')
            || str_contains($sql, ' from `suppliers`')
            || str_contains($sql, ' from "suppliers"')
            || str_contains($sql, ' join `suppliers`')
            || str_contains($sql, ' join "suppliers"')
            || str_contains($sql, ' from `product_supplier`')
            || str_contains($sql, ' from "product_supplier"')
            || str_contains($sql, ' join `product_supplier`')
            || str_contains($sql, ' join "product_supplier"')
        ) {
            return 'product';
        }

        if (
            str_contains($sql, ' from `cache`')
            || str_contains($sql, ' from "cache"')
            || str_contains($sql, ' from `cache_locks`')
            || str_contains($sql, ' from "cache_locks"')
            || str_contains($sql, ' insert into `cache`')
            || str_contains($sql, ' update `cache`')
        ) {
            return 'cache';
        }

        return null;
    }
}
