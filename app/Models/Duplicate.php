<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duplicate extends Model
{
    use HasFactory;

    public $table = "duplicateloans";

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction','loan_id','loan_id');
    }
}
