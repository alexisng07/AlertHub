<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\AlertRuleResource;
use App\Http\Requests\StoreAlertRuleRequest;

class AlertRuleController extends Controller
{
    public function index(Project $project)
    {
        return AlertRuleResource::collection(
            $project->alertRules()
                ->paginate(15)
        );
    }

    public function store(
        StoreAlertRuleRequest $request,
        Project $project
    ) {
        $rule = $project
            ->alertRules()
            ->create(
                $request->validated()
            );

        return new AlertRuleResource($rule);
    }
}