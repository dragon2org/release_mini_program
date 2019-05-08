<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ComponentTemplate extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'component_template';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'component_template_id';
}