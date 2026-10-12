<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'full_name',
        'national_id',
        'department_id',
        'section_id',
        'manager_employee_id',
        'position',
        'phone',
        'avatar',
        'employment_status',
        'hire_date',
        'contract_end_date',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'contract_end_date' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_employee_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(EmployeeRequest::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_employee_id');
    }

    public function latestEvaluation(): HasOne
    {
        return $this->hasOne(PerformanceEvaluation::class)->latestOfMany('evaluation_date');
    }

    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }
}
