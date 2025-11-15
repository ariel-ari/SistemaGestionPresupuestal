<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseAllocation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'expense_code',
        'office_budget_assignment_id',
        'document_id',
        'subclassifier_id',
        'type',
        'report_number',
        'siaf_code',
        'reference_document',
        'reference_document_2',
        'reference_document_3',
        'date',
        'amount',
        'description',
        'subject',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by_id',
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
            'office_budget_assignment_id' => 'integer',
            'document_id' => 'integer',
            'subclassifier_id' => 'integer',
            'date' => 'date',
            'amount' => 'decimal:2',
            'created_by' => 'integer',
            'approved_by' => 'integer',
            'approved_at' => 'timestamp',
            'created_by_id' => 'integer',
            'approved_by_id' => 'integer',
        ];
    }

    public function officeBudgetAssignment(): BelongsTo
    {
        return $this->belongsTo(OfficeBudgetAssignment::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function subclassifier(): BelongsTo
    {
        return $this->belongsTo(Subclassifier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
