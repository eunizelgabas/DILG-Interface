<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'legal_opinions';

    protected $fillable = ['title', 'link', 'category', 'reference', 'date', 'download_link', 'extracted_texts'];

}
