<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Latest extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'outcome', 'issuance_id'];

    public function issuance()
    {
        return $this->belongsTo(Issuances::class);
    }
}
