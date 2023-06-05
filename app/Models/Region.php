<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    public $table = "kangemi_update";
    public $timestamps = false;

    public function office(){
        return $this->hasOne('App\Models\Office','name','region');
    }
}
