<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subclassifier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classifier_id',
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
            'classifier_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function classifier(): BelongsTo
    {
        return $this->belongsTo(Classifier::class);
    }

    public function expenseAllocations(): HasMany
    {
        return $this->hasMany(ExpenseAllocation::class);
    }
}
