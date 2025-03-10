<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalOpinionPdf extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_opinion_id',
        'pdf_file',
        'file_path',
        'extracted_text'
    ];

    public function legalOpinion()
{
    return $this->belongsTo(Legal::class);
}

}
