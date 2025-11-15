<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportStaging extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'import_batch_id',
        'row_number',
        'raw_data',
        'parsed_data',
        'status',
        'validation_errors',
        'imported_record_type',
        'imported_record_id',
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
            'import_batch_id' => 'integer',
            'raw_data' => 'array',
            'parsed_data' => 'array',
            'validation_errors' => 'array',
            'imported_record_id' => 'integer',
            'created_at' => 'timestamp',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function importedRecord(): BelongsTo
    {
        return $this->belongsTo(ImportedRecord::class);
    }
}
