<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = ['id', 'name', 'contact', 'organization', 'address' , 'latitude', 'longitude'];

}