<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope('organization', function ($query) {

            $organization = request()->attributes->get('organization');

            if (!$organization) {
                return;
            }

            $query->where('organization_id', $organization->id);
        });
    }

    protected $fillable = [
        'organization_id',
        'uuid',
        'name',
        'description',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    public function alertRules()
    {
        return $this->hasMany(AlertRule::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function webhookSources()
    {
        return $this->hasMany(WebhookSource::class);
    }
}
