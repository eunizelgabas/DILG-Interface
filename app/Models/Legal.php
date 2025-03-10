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

    protected $casts = [
        'title' => 'string', // Ensure it's returned as a properly formatted string
        'link' => 'string', // Ensure it's returned as a properly formatted string
        'category' => 'string', // Ensure it's returned as a properly formatted string
        'reference' => 'string', // Ensure it's returned as a properly formatted string
        'date' => 'string', // Ensure it's returned as a properly formatted string
        'download_link' => 'string', // Ensure it's returned as a properly formatted string
        'extracted_texts' => 'string', // Ensure it's returned as a properly formatted string
    ];

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
