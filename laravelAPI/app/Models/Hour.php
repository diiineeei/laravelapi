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
        'funcionario_id'
    ];

    // Relacionamento com o funcionário
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}
