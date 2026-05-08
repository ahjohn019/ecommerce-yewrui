<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

class CacheService extends ResponseService
{
    private const CACHE_VERSION_KEY = 'products:cache:version';

    public function __construct(
        private readonly CacheRepository $cache
    ) {
    }

    public function remember(string $key, int $ttl, \Closure $callback): mixed
    {
        return $this->cache->remember($this->cacheKey($key), $ttl, $callback);
    }

    public function bumpVersion(): void
    {
        $this->cache->put(self::CACHE_VERSION_KEY, (string) microtime(true), now()->addDay());
    }

    public function version(): string
    {
        return (string) $this->cache->get(self::CACHE_VERSION_KEY, '1');
    }

    public function cacheKey(string $suffix): string
    {
        return 'products:'.$this->version().':'.$suffix;
    }
}
