<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidateFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'validate_file';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'validate_file_id';

    protected $fillable = [
        'filename',
        'content',
    ];
}