<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joint extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'link',
        'reference',
        'date',
        'download_link',
    ];
    // protected $fillable = ['responsible_office', 'issuance_id'];

    public function issuance()
    {
        return $this->belongsTo(Issuances::class, 'issuance_id');
    }
}
