<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiniProgram extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mini_program';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mini_program_id';

    public function tester()
    {
        return $this->hasMany(Tester::class, 'mini_program_id', 'mini_program_id');
    }
}
