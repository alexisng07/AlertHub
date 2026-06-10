<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\OrganizationProject;

class Subscriber extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriberFactory> */
    use HasFactory;
    use OrganizationProject;

    protected $fillable = [
        'project_id',
        'email',
        'external_id',
        'name',
        'notification_count',
        'last_notified_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_notified_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
