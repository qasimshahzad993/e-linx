<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'budget',
        'type',
        'status',
        'company_name',
        'project_name',
        'project_stage',
        'timeline',
        'target_audience',
        'required_features',
        'reference_url',
        'service_details',
    ];

    protected function casts(): array
    {
        return [
            'required_features' => 'array',
            'service_details' => 'array',
        ];
    }
}
