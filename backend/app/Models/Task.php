<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Campos que permitiremos que se guarden mediante la API
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'user_id'
    ];

    // Relación inversa: Una tarea pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
