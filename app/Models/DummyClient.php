<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DummyClient extends Model
{
    use HasFactory;

    public $table = "dummy_clients_3";

    public $timestamps = false;
}
