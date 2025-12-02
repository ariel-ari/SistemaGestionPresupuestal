<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SimulatedExpense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'simulation_type',
        'office_id',
        'goal_id',
        'financing_id',
        'classifier_id',
        'description',
        'amount',
        'estimated_date',
        'notes',
        'is_approved',
        'converted_to_real',
        'real_expense_allocation_id',
        'created_by',
        'created_by_id',
        'real_expense_allocation_id_id',
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
            'office_id' => 'integer',
            'goal_id' => 'integer',
            'financing_id' => 'integer',
            'classifier_id' => 'integer',
            'amount' => 'decimal:2',
            'estimated_date' => 'date',
            'is_approved' => 'boolean',
            'converted_to_real' => 'boolean',
            'real_expense_allocation_id' => 'integer',
            'created_by' => 'integer',
            'created_by_id' => 'integer',
            'real_expense_allocation_id_id' => 'integer',
        ];
    }

    // public function office(): BelongsTo
    // {
    //     return $this->belongsTo(Office::class);
    // }

    // public function goal(): BelongsTo
    // {
    //     return $this->belongsTo(Goal::class);
    // }

    // public function financing(): BelongsTo
    // {
    //     return $this->belongsTo(Financing::class);
    // }

    // public function classifier(): BelongsTo
    // {
    //     return $this->belongsTo(Classifier::class);
    // }

    // public function createdBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function realExpenseAllocation(): BelongsTo
    // {
    //     return $this->belongsTo(ExpenseAllocation::class);
    // }

    // public function realExpenseAllocation(): BelongsTo
    // {
    //     return $this->belongsTo(ExpenseAllocation::class);
    // }

    // public function createdBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}
