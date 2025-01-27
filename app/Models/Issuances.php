<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuances extends Model
{
    use HasFactory;

    protected $connection = 'dilg_bohol'; // Use the DILG Bohol database connection
    protected $table = 'bohol_issuances';  
    
    protected $fillable = ['title', 'reference_no', 'type', 'url_link', 'date', 'keyword'];

    public function latest()
    {
        return $this->hasOne(Latest::class, 'issuance_id');
    }

    public function joint(){
        return $this->hasOne(Joint::class, 'issuance_id');
    }

    public function memo(){
        return $this->hasOne(Memo::class, 'issuance_id');
    }

    public function presidential(){
        return $this->hasOne(Presidential::class, 'issuance_id');
    }

    public function draft(){
        return $this->hasOne(Draft::class, 'issuance_id');
    }

    public function republic()
    {
        return $this->hasOne(Republic::class, 'issuance_id');
    }

    public function legal(){
        return $this->hasOne(Legal::class, 'issuance_id');
    }

}
