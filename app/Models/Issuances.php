<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuances extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'reference_no', 'type', 'url_link', 'date', 'keyword'];

    public function latest()
    {
        return $this->hasOne(Latest::class);
    }

    public function joint(){
        return $this->hasOne(Joint::class);
    }

    public function memo(){
        return $this->hasOne(Memo::class);
    }


}
