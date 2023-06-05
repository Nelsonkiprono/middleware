<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    use HasFactory;

    public $table = "m_loan_transaction";

    public function loantransaction(){
        return $this->belongsTo('App\Models\Loan','loan_id','id');
    }
}
