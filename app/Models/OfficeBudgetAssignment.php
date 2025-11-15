<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeBudgetAssignment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'office_id',
        'subunit_id',
        'annual_institutional_budget_id',
        'amount',
        'version',
        'status',
        'parent_id',
        'superseded_by',
        'assignment_type',
        'comment',
        'related_transfer_id',
        'assigned_by',
        'modified_by',
        'assigned_by_id',
        'modified_by_id',
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
            'subunit_id' => 'integer',
            'annual_institutional_budget_id' => 'integer',
            'amount' => 'decimal:2',
            'parent_id' => 'integer',
            'superseded_by' => 'integer',
            'related_transfer_id' => 'integer',
            'assigned_by' => 'integer',
            'modified_by' => 'integer',
            'assigned_by_id' => 'integer',
            'modified_by_id' => 'integer',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function subunit(): BelongsTo
    {
        return $this->belongsTo(Subunit::class);
    }

    public function annualInstitutionalBudget(): BelongsTo
    {
        return $this->belongsTo(AnnualInstitutionalBudget::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OfficeBudgetAssignment::class);
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(OfficeBudgetAssignment::class);
    }

    public function relatedTransfer(): BelongsTo
    {
        return $this->belongsTo(RelatedTransfer::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenseAllocations(): HasMany
    {
        return $this->hasMany(ExpenseAllocation::class);
    }
}
