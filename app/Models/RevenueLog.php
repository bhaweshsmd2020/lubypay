<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueLog extends Model
{
    protected $table    = 'revenue_logs';
    protected $fillable = ['changed_by', 'transactional','operational', 'operational_a', 'operational_b'];
    public $timestamps  = false;
}
