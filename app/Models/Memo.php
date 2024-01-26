<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = ['responsible_office', 'issuance_id'];

    public function issuance()
    {
        return $this->belongsTo(Issuances::class);
    }

}
