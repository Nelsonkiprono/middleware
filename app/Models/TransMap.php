<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransMap extends Model
{
    use HasFactory;

    public $table = "m_loan_transaction_repayment_schedule_mapping";
}
