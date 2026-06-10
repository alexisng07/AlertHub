<?php

namespace App\Traits;

trait LoadInclude
{
    protected function loadIncludes(
        $query,
        array $allowed
    ) {
        $includes = request('includes');

        if (!$includes) {
            return $query;
        }

        $relations = collect(
            explode(',', $includes)
        )
            ->map(fn ($item) => trim($item))
            ->intersect($allowed)
            ->values()
            ->all();

        return $query->with($relations);
    }
}
