<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    public function index(Project $project)
    {
        $query = $project->notifications();

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($channel = request('channel')) {
            $query->where('channel', $channel);
        }

        return NotificationResource::collection(
            $query->paginate(15)
        );
    }
}