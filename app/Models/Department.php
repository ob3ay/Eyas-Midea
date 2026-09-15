<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'manager_employee_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function managerUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Employee::class,
            'department_id',
            'employee_id',
            'id',
            'id'
        )->whereColumn('employees.id', 'departments.manager_employee_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Employee::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
