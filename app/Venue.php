<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';

    protected $fillable = ['vendor_id', 'name', 'description','markers', 'place_name' , 'organization', 'latitude', 'longitude','floor_number',"category", "geoJson_file",];

    public function users()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

}
