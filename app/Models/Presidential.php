<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presidential extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'presidential_directives';

    protected $fillable = [
        'title',
        'link',
        'reference',
        'date',
        'download_link'
    ];
}
