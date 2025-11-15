<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'request_code',
        'request_type',
        'related_table',
        'related_id',
        'request_data',
        'current_data',
        'office_id',
        'amount',
        'reason',
        'status',
        'requested_by',
        'reviewed_by',
        'requested_at',
        'reviewed_at',
        'rejection_reason',
        'requested_by_id',
        'reviewed_by_id',
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
            'related_id' => 'integer',
            'request_data' => 'array',
            'current_data' => 'array',
            'office_id' => 'integer',
            'amount' => 'decimal:2',
            'requested_by' => 'integer',
            'reviewed_by' => 'integer',
            'requested_at' => 'timestamp',
            'reviewed_at' => 'timestamp',
            'requested_by_id' => 'integer',
            'reviewed_by_id' => 'integer',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): BelongsTo
    {
        return $this->belongsTo(Related::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
