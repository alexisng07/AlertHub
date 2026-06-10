<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\SubscriberResource;
use App\Http\Requests\StoreSubscriberRequest;

class SubscriberController extends Controller
{
    public function index(Project $project)
    {
        return SubscriberResource::collection(
            $project->subscribers()
                ->paginate(15)
        );
    }

    public function store(
        StoreSubscriberRequest $request,
        Project $project
    ) {
        $subscriber = $project
            ->subscribers()
            ->create(
                $request->validated()
            );

        return new SubscriberResource(
            $subscriber
        );
    }
}