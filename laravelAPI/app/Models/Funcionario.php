<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employees extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'employees',
        'matricula',
        'tipo',
        'data_admissao',
        'hour_value'
    ];

    // Relacionamento com as horas
    public function hours(): HasMany
    {
        return $this->hasMany(Hour::class);
    }
}
