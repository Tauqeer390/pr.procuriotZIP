<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileItem extends Model

{
    protected $table = 'file_items';
    protected $fillable = ['title', 'file_name', 'file_path'];
}
