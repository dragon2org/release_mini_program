<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseAudit extends Model
{
    use SoftDeletes;
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

    public function isSuccess()
    {
        return $this->status === self::ORIGIN_AUDIT_STATUS_SUCCESS ? true : false;
    }
}
