<?php

namespace App\Models;

use App\Models\Traits\SoftDeletes;
use App\ReleaseConfigurator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReleaseAudit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'release_audit';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_audit_id';

    const ORIGIN_AUDIT_STATUS_SUCCESS = 0;

    const ORIGIN_AUDIT_STATUS_FAILED = 1;

    const ORIGIN_AUDIT_STATUS_AUDITING = 2;

    const ORIGIN_AUDIT_STATUS_REVERTED = 3;
}
