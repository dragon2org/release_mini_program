<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponentExt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'component_ext';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'component_ext_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'component_id',
    ];
}
