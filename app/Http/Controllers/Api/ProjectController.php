<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Traits\LoadInclude;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use LoadInclude;

    public function index()
    {
        // $query = Project::query();

        // $this->loadIncludes($query, [
        //     'subscribers',
        //     'alertRules',
        //     'notifications',
        //     'webhookSources',
        // ]);

        // return ProjectResource::collection(
        //     $query->paginate()
        // );
        return ProjectResource::collection(
            Project::paginate()
        );
    }

    public function show(Request $request, Project $project)
    {
        // $this->loadIncludes(
        //     $project->newQuery()->whereKey($project),
        //     [
        //         'subscribers',
        //         'alertRules',
        //         'notifications',
        //         'webhookSources',
        //     ]
        // );

        // $project->loadMissing(
        //     request('includes')
        //         ? explode(',', request('includes'))
        //         : []
        // );

        // return new ProjectResource($project);
        $includes = explode(
            ',',
            $request->query('includes', '')
        );

        $allowed = [
            'subscribers',
            'alertRules',
            'notifications',
            'webhookSources',
        ];

        $project->load(
            array_intersect($includes, $allowed)
        );

        return new ProjectResource($project);
    }

    public function store(StoreProjectRequest $request)
    {
        $organization = $request->attributes->get('organization');

        $project = Project::create([
            'uuid' => Str::uuid(),
            'organization_id' => $organization->id,
            ...$request->validated(),
        ]);

        return new ProjectResource($project);
    }

    public function update(
        UpdateProjectRequest $request,
        Project $project
    ) {
        $project->update(
            $request->validated()
        );

        return new ProjectResource($project);
    }
}
