<?php

namespace App\AlertMetrics;

use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class MetricsAggregator
{
    /**
     * Get the count of alerts for a given date.
     *
     * Used by the dashboard and digest scheduler to track alert volume.
     * Results are cached for 1 hour to reduce database load.
     *
     * @param  string  $date  Date in Y-m-d format
     * @return int
     */
    public function getDailyAlertCount(int $projectId, string $date): int 
    {
        $cacheKey = "alert-metrics::{$projectId}::{$date}";

        return Cache::remember(
            $cacheKey,
            3600,
            function () use ($projectId, $date) {
                return \App\Models\Notification::query()
                    ->where('project_id', $projectId)
                    ->whereDate('created_at', $date)
                    ->count();
            }
        );
    }

    /**
     * Get hourly breakdown of alert counts for a date.
     *
     * @param  string  $date  Date in Y-m-d format
     * @return array
     */
    public function getHourlyBreakdown(int $projectId, string $date): array {
        $cacheKey =
            "alert-metrics::hourly::{$projectId}::{$date}";

        return Cache::remember(
            $cacheKey,
            1800,
            function () use ($projectId, $date) {
                return \App\Models\Notification::query()
                    ->where('project_id', $projectId)
                    ->whereDate('created_at', $date)
                    ->selectRaw(
                        'HOUR(created_at) as hour, COUNT(*) as count'
                    )
                    ->groupByRaw('HOUR(created_at)')
                    ->pluck('count', 'hour')
                    ->toArray();
            }
        );
    }

    /**
     * Record that an alert was processed.
     *
     * Increments the daily counter and invalidates stale cache.
     *
     * @param  int  $notificationId
     * @return void
     */
    public function recordAlert(int $notificationId, int $projectId): void
    {
        $today = now()->toDateString();
        $counterKey = "alert-metrics::counter::{$today}";

        Cache::increment($counterKey);

        // Invalidate the cached count so next read is fresh
        Cache::forget("alert-metrics::{$projectId}::{$today}");
        Cache::forget("alert-metrics::hourly::{$projectId}::{$today}");
    }

    /**
     * Get alert count for a specific time window.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return int
     */
    public function getAlertCountForWindow(string $startDate, string $endDate, int $projectId): int
    {
        return Notification::query()->where('project_id', $projectId)->whereBetween('created_at',[$startDate, $endDate])->count();
    }

    public function recordAlertBatch(int $subscriberId, string $date, array $alertIds): void
    {
        $key = "digest:alerts:{$subscriberId}:{$date}";

        // Store or merge alert IDs safely
        $existing = Cache::get($key, []);

        $merged = array_unique(array_merge($existing, $alertIds));

        Cache::put($key, $merged, now()->addDays(2));

        // Optional logging for debugging
        Log::info('MetricsAggregator: Alert batch recorded', [
            'subscriber_id' => $subscriberId,
            'date' => $date,
            'count' => count($merged),
        ]);
    }
}
