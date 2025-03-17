<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    use HasFactory;

    // protected $connection = 'dilg_bohol'; 
    protected $connection = 'mysql';

    protected $table = 'legal_opinions';

    protected $fillable = ['title', 'link', 'category', 'reference', 'date', 'download_link', 'extracted_texts'];

    // protected $fillable = ['category', 'issuance_id', 'responsible_office'];

    public function issuance()
    {
        return $this->belongsTo(Issuances::class, 'issuance_id');
    }

    public function pdfs()
    {
        return $this->hasMany(LegalOpinionPdf::class);
    }

}
