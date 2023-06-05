<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journalentry extends Model
{
    use HasFactory;

    public $table = "acc_gl_journal_entry";

    public $timestamps = false;

}
