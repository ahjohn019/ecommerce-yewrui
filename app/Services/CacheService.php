<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Log;

class CacheService extends ResponseService
{
    public const LIST_CACHE_TTL = 600;
    public const ITEM_CACHE_TTL = 600;

    private const CACHE_VERSION_KEY = 'products:cache:version';

    public function __construct(
        private readonly CacheRepository $cache
    ) {
    }

    /**
     * Remember a value in cache using the product cache namespace.
     */
    public function remember(string $key, int $ttl, \Closure $callback): mixed
    {
        $cacheKey = $this->cacheKey($key);
        $startedAt = hrtime(true);
        $miss = false;
        $result = $this->cache->remember($cacheKey, $ttl, function () use ($callback, &$miss) {
            $miss = true;

            return $callback();
        });
        $elapsedMs = (hrtime(true) - $startedAt) / 1_000_000;

        if (config('app.debug')) {
            Log::debug('Product cache '.($miss ? 'miss' : 'hit'), [
                'key' => $cacheKey,
                'ttl' => $ttl,
                'elapsed_ms' => round($elapsedMs, 2),
            ]);
        }

        return $result;
    }

    /**
     * Bump the cache version so all existing product cache keys become stale.
     */
    public function bumpVersion(): void
    {
        $this->cache->put(self::CACHE_VERSION_KEY, (string) microtime(true), now()->addDay());
    }

    /**
     * Get the current cache version used for product cache key namespacing.
     */
    public function version(): string
    {
        return (string) $this->cache->get(self::CACHE_VERSION_KEY, '1');
    }

    /**
     * Build a versioned cache key for product-related data.
     */
    public function cacheKey(string $suffix): string
    {
        return 'products:'.$this->version().':'.$suffix;
    }
}
