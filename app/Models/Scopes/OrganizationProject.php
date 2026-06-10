<?php

namespace App\Models\Scopes;

trait OrganizationProject
{
    protected static function bootBelongsToOrganizationProjects(): void
    {
        static::addGlobalScope('organization_projects', function ($query) {

            $organization = request()->attributes->get('organization');

            if (!$organization) {
                return;
            }

            $query->whereHas('project', function ($projectQuery) use ($organization) {

                $projectQuery
                    ->withoutGlobalScopes()
                    ->where(
                        'organization_id',
                        $organization->id
                    );
            });
        });
    }
}
