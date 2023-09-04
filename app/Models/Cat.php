<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'message','name','gender','age','country','breeds','url','thumb'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
