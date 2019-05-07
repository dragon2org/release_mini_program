<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiniProgramExt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mini_program_ext';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mini_program_ext_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'component_id', 'mini_program_id', 'company_id', 'template_id', 'config'
    ];

}
