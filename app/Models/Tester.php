<?php

namespace App\Models;

use App\Models\Traits\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Tester extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tester';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tester_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wechat_id',
        'userstr',
        'mini_program_id',
        'app_id',
    ];
}
