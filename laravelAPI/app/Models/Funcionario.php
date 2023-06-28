<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionarios';

    protected $fillable = [
        'funcionario',
        'matricula',
        'tipo',
        'data_admissao',
        'hour_value'
    ];

    // Relacionamento com as horas
    public function hours(): HasMany
    {
        return $this->hasMany(Hours::class);
    }
}
