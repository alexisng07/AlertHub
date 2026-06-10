<?php

namespace App\AlertMetrics\Events;

class DigestWindowCalculated
{
    public function __construct(
        public string $referenceId,
        public int $subscriberId,
        public int $projectId,
        public int $alertCount,
        public string $window,
        public string $date,
        public string $digestType,
    ) {}
}