<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class ExpenseLog extends Model
{
protected $fillable = [
'date',
'description',
'amount'
];
}
