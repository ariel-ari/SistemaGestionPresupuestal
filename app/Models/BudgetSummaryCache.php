<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSummaryCache extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'office_id',
        'goal_id',
        'financing_id',
        'classifier_id',
        'summary_level',
        'total_budget',
        'total_assigned',
        'total_executed',
        'total_available',
        'execution_percentage',
        'last_calculated_at',
        'is_valid',
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
            'total_budget' => 'decimal:2',
            'total_assigned' => 'decimal:2',
            'total_executed' => 'decimal:2',
            'total_available' => 'decimal:2',
            'execution_percentage' => 'decimal:2',
            'last_calculated_at' => 'timestamp',
            'is_valid' => 'boolean',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function financing(): BelongsTo
    {
        return $this->belongsTo(Financing::class);
    }

    public function classifier(): BelongsTo
    {
        return $this->belongsTo(Classifier::class);
    }
}
