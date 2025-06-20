<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Client;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\User;

class WebDevelopment extends Model {
    protected $table = 'web_development';
    
    protected $fillable = [
        'client_id',
        'project_name',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget',
        'technology_stack'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'float',
        'technology_stack' => 'array'
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function milestones() {
        return $this->hasMany(Milestone::class);
    }

    public function assignedUsers() {
        return $this->belongsToMany(User::class, 'project_assignments');
    }
}
