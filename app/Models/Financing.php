<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'code' => 'string',
            'name' => 'string',
            'description' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public function annualInstitutionalBudgets(): HasMany
    {
        return $this->hasMany(AnnualInstitutionalBudget::class);
    }

    public function simulatedExpenses(): HasMany
    {
        return $this->hasMany(SimulatedExpense::class);
    }
}
