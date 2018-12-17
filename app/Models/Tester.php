<?php

namespace App\Models;

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
}
