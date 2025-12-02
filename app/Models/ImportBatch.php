<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'batch_code',
        'import_type',
        'file_name',
        'file_path',
        'file_size',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'status',
        'errors',
        'validation_summary',
        'imported_by',
        'started_at',
        'completed_at',
        'imported_by_id',
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
            'errors' => 'array',
            'validation_summary' => 'array',
            'imported_by' => 'integer',
            'started_at' => 'timestamp',
            'completed_at' => 'timestamp',
            'imported_by_id' => 'integer',
        ];
    }

    // public function importedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function importedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function importStagings(): HasMany
    // {
    //     return $this->hasMany(ImportStaging::class);
    // }
}
