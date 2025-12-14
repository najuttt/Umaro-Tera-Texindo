<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppLog extends Model
{
protected $fillable = [
'date',
'hpp_total',
'note'
];
}