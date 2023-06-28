<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hour extends Model
{
    use HasFactory;

    protected $table = 'hours';

    protected $fillable = [
        'year',
        'month',
        'total_hours',
        'employees_id'
    ];

    // Relacionamento com o funcionário
    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employees_id');
    }
}
