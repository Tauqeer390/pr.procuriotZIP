<?php

namespace App;

use App\Beacon;
use Illuminate\Database\Eloquent\Model;

class BeaconProduct extends Model
{
  public function beacon()
    {
        return $this->belongsTo(Beacon::class, 'beacon_id', 'id');
    }

}
