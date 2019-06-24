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

    public function miniProgram()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id');
    }

    public function uncommitted()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id')
            ->whereIn('status', [
                Release::RELEASE_STATUS_UNCOMMITTED,
                Release::RELEASE_STATUS_COMMIT_FAILED,
            ]);
    }

    public function committed()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id')
            ->where('status', Release::RELEASE_STATUS_COMMITTED);
    }

    public function auditing()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id')
            ->where('status', Release::RELEASE_STATUS_AUDITING);
    }

    public function auditFailed()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id')
            ->where('status', Release::RELEASE_STATUS_AUDIT_FAILED);
    }

    public function released()
    {
        return $this->hasMany(Release::class, 'template_id', 'template_id')
            ->where('status', Release::RELEASE_STATUS_RELEASED);
    }
}