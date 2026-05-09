<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderService extends ResponseService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with('user:id,name');

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            return Order::create($data)->load('user:id,name');
        });
    }

    public function delete(Order $order): bool
    {
        return DB::transaction(fn () => (bool) $order->delete());
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->when(filled($filters['user_id'] ?? null), fn (Builder $query) => $query->where('user_id', $filters['user_id']));
        $query->when(filled($filters['status'] ?? null), fn (Builder $query) => $query->where('status', $filters['status']));
        $query->when(filled($filters['min_total'] ?? null), fn (Builder $query) => $query->where('total', '>=', $filters['min_total']));
        $query->when(filled($filters['max_total'] ?? null), fn (Builder $query) => $query->where('total', '<=', $filters['max_total']));
        $query->when(filled($filters['from_date'] ?? null), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['from_date']));
        $query->when(filled($filters['to_date'] ?? null), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['to_date']));

        return $query;
    }
}
