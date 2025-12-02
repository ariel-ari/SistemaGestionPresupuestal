<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetTransfer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'transfer_code',
        'transaction_number',
        'origin_office_budget_assignment_id',
        'origin_office_id',
        'origin_annual_budget_id',
        'destination_office_budget_assignment_id',
        'destination_office_id',
        'destination_annual_budget_id',
        'amount',
        'reason',
        'resolution_number',
        'status',
        'requested_by',
        'approved_by',
        'requested_at',
        'approved_at',
        'executed_at',
        'rejection_reason',
        'requested_by_id',
        'approved_by_id',
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
            'origin_office_budget_assignment_id' => 'integer',
            'origin_office_id' => 'integer',
            'origin_annual_budget_id' => 'integer',
            'destination_office_budget_assignment_id' => 'integer',
            'destination_office_id' => 'integer',
            'destination_annual_budget_id' => 'integer',
            'amount' => 'decimal:2',
            'requested_by' => 'integer',
            'approved_by' => 'integer',
            'requested_at' => 'timestamp',
            'approved_at' => 'timestamp',
            'executed_at' => 'timestamp',
            'requested_by_id' => 'integer',
            'approved_by_id' => 'integer',
        ];
    }

    // public function requestedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function approvedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function originOfficeBudgetAssignment(): BelongsTo
    // {
    //     return $this->belongsTo(OfficeBudgetAssignment::class);
    // }

    // public function originOffice(): BelongsTo
    // {
    //     return $this->belongsTo(Office::class);
    // }

    // public function originAnnualBudget(): BelongsTo
    // {
    //     return $this->belongsTo(AnnualInstitutionalBudget::class);
    // }

    // public function destinationOfficeBudgetAssignment(): BelongsTo
    // {
    //     return $this->belongsTo(OfficeBudgetAssignment::class);
    // }

    // public function destinationOffice(): BelongsTo
    // {
    //     return $this->belongsTo(Office::class);
    // }

    // public function destinationAnnualBudget(): BelongsTo
    // {
    //     return $this->belongsTo(AnnualInstitutionalBudget::class);
    // }

    // public function requestedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function approvedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}
