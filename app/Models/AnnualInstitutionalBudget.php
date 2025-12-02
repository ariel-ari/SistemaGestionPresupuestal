<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnualInstitutionalBudget extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'goal_id',
        'process_id',
        'product_id',
        'activity_id',
        'purpose_id',
        'financing_id',
        'classifier_id',
        'amount',
        'version',
        'status',
        'parent_id',
        'superseded_by',
        'modification_type',
        'modification_reason',
        'resolution_number',
        'created_by',
        'modified_by',
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
            'year' => 'integer',
            'goal_id' => 'integer',
            'process_id' => 'integer',
            'product_id' => 'integer',
            'activity_id' => 'integer',
            'purpose_id' => 'integer',
            'financing_id' => 'integer',
            'classifier_id' => 'integer',
            'amount' => 'decimal:2',
            'version' => 'integer',
            'parent_id' => 'integer',
            'superseded_by' => 'integer',
            'created_by' => 'integer',
            'modified_by' => 'integer',
        ];
    }

    // public function goal(): BelongsTo
    // {
    //     return $this->belongsTo(Goal::class);
    // }

    // public function process(): BelongsTo
    // {
    //     return $this->belongsTo(Process::class);
    // }

    // public function product(): BelongsTo
    // {
    //     return $this->belongsTo(Product::class);
    // }

    // public function activity(): BelongsTo
    // {
    //     return $this->belongsTo(Activity::class);
    // }

    // public function purpose(): BelongsTo
    // {
    //     return $this->belongsTo(Purpose::class);
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
    //     return $this->belongsTo(User::class,'created_by');
    // }

    // public function modifiedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, ',modified_by');
    // }

    // public function parent(): BelongsTo
    // {
    //     return $this->belongsTo(AnnualInstitutionalBudget::class, 'parent_id');
    // }

    // public function supersededBy(): BelongsTo
    // {
    //     return $this->belongsTo(AnnualInstitutionalBudget::class,'superseded_by');
    // }

    // public function children(): HasMany
    // {
    //     return $this->hasMany(AnnualInstitutionalBudget::class, 'parent_id');
    // }

    // public function supersedes(): HasMany
    // {
    //     return $this->hasMany(AnnualInstitutionalBudget::class, 'superseded_by');
    // }

    // public function officeBudgetAssignments(): HasMany
    // {
    //     return $this->hasMany(OfficeBudgetAssignment::class);
    // }

    // public function scopeActive($query){
    //  return $query->where('status','active');   
    // }
}
