<?php

namespace App\Models;

use App\Logs\CodeCommitLog;
use Illuminate\Database\Eloquent\Model;

class MiniProgram extends Model
{
    use SoftDeletes;

    const AUTHORIZATION_STATUS_AUTHORIZED = 10;

    const AUTHORIZATION_STATUS_UNAUTHORIZED = 20;

    protected  $fillable = [
        'app_id'
    ];
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

    public function getComponentMiniProgramList($componentId)
    {
        return (new self())
            ->where('component_id', $componentId)
            ->where('authorization_status', MiniProgram::AUTHORIZATION_STATUS_AUTHORIZED)
            ->get();
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id', 'component_id');
    }

    public function ext()
    {
        return $this->hasOne(MiniProgramExt::class, 'mini_program_id', 'mini_program_id')->orderBy('mini_program_ext_id', 'desc');
    }

    public function getTemplateConfig($templateId)
    {
        $config = $this->ext()->where('template_id', $templateId)->orderBy('mini_program_ext_id', 'desc')->first();
        return $config;
    }

    /**
     * 替换模板变量
     * @param string $extJson
     * @return mixed|string
     */
    public function assign(string $extJson)
    {
        CodeCommitLog::info($this, 'assign mini_program ext_json origin', ['ext_json' => $extJson]);

        $extJson = str_replace('$APP_ID$', $this->app_id,  $extJson);
        $extJson = str_replace('$COMPANY_ID$', $this->company_id,  $extJson);

        CodeCommitLog::info($this, 'assign mini_program ext_json result ', ['ext_json' => $extJson]);
        return $extJson;
    }

    public function onlineVersion()
    {
        return $this
            ->hasOne(Release::class, 'mini_program_id', 'mini_program_id')
            ->where('status', Release::RELEASE_STATUS_RELEASED)
            ->orderBy('release_id', 'desc');
    }

    public function buildVersion()
    {
        return $this
            ->hasOne(Release::class, 'mini_program_id', 'mini_program_id')
            ->where('category', Release::RELEASE_CATEGORY_RELEASE)
            ->whereNotIn('status', [Release::RELEASE_STATUS_RELEASED])
            ->orderBy('release_id', 'desc');
    }

    public function getBuildVersion()
    {
        if(!isset($this->buildVersion)){
            return '';
        }

        if($this->onlineVersion){
            if($this->onlineVersion->release_id > $this->buildVersion->release_id){
                return '';
            }
        }

        return $this->buildVersion->user_version;
    }
}
