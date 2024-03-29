<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'issuance_id', 'responsible_office'];

    public function issuance()
    {
        return $this->belongsTo(Issuances::class, 'issuance_id');
    }
}
