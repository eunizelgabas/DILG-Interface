<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Republic extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'republic_acts';

    protected $fillable = ['title', 'link', 'reference', 'date', 'download_link'];

}
