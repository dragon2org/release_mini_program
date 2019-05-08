<?php

namespace App\Models;

use App\Logs\CodeCommitLog;
use Illuminate\Database\Eloquent\Model;

class MiniProgram extends Model
{
    use SoftDeletes;

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
}
