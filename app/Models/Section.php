<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'department_id',
        'section_head_employee_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'section_head_employee_id');
    }

    public function headUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Employee::class,
            'section_id',
            'employee_id',
            'id',
            'id'
        )->whereColumn('employees.id', 'sections.section_head_employee_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Employee::class);
    }
}
