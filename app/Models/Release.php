<?php

namespace App\Models;


use App\Exceptions\UnprocessableEntityHttpException;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Release extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'release';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_id';

    CONST RELEASE_STATUS_SETTING = 0;

    CONST RELEASE_STATUS_UNCOMMITTED = 10;

    CONST RELEASE_STATUS_COMMITTED = 11;

    CONST RELEASE_STATUS_COMMIT_FAILED = 12;

    CONST RELEASE_STATUS_AUDITING = 20;

    CONST RELEASE_STATUS_AUDIT_SUCCESS = 21;

    CONST RELEASE_STATUS_AUDIT_FAILED = 22;

    CONST RELEASE_STATUS_AUDIT_REVERTED = 23;

    CONST RELEASE_STATUS_RELEASED = 30;

    CONST RELEASE_CATEGORY_SETTING = 'setting';

    CONST RELEASE_CATEGORY_COMMIT = 'commit';

    CONST RELEASE_CATEGORY_AUDIT = 'audit';

    CONST RELEASE_CATEGORY_RELEASE = 'release';

    public $buildItems = [];

    public function item()
    {
        return $this->hasMany(ReleaseItem::class, 'release_id', 'release_id');
    }

    public function miniProgram()
    {
        return $this->hasOne(MiniProgram::class, 'mini_program_id', 'mini_program_id');
    }


    public function make(MiniProgram $miniProgram, $templateId, $config)
    {
        try {
            $tradeNo = $this->genTradeNo($miniProgram->mini_program_id);
            $model = (new self());
            $model->component_id = $miniProgram->component_id;
            $model->mini_program_id = $miniProgram->mini_program_id;
            $model->template_id = $templateId;
            $model->trade_no = $tradeNo;
            $model->config = json_encode($config, JSON_UNESCAPED_UNICODE);
            $model->config_version = sha1($model->config);
            $model->status = Release::RELEASE_STATUS_SETTING;
            $model->category = Release::RELEASE_CATEGORY_RELEASE;
            $model->save();

            $collect = ReleaseItem::make($model, $miniProgram, $config);
        } catch (\Exception $e) {
            throw  $e;
        }

        return [
            'task_num' => $collect->count(),
            'app_id' => $miniProgram->app_id,
            'trade_no' => $tradeNo
        ];
    }

    /**
     * @param MiniProgram $miniProgram
     * @param $templateId
     * @param string $config
     * @param array $response
     * @return Release
     */
    public function syncMake(MiniProgram $miniProgram, $templateId, string $config, array $response)
    {
        $tradeNo = $this->genTradeNo($miniProgram->mini_program_id);
        $model = (new self());
        $model->component_id = $miniProgram->component_id;
        $model->mini_program_id = $miniProgram->mini_program_id;
        $model->template_id = $templateId;
        $model->trade_no = $tradeNo;
        $model->config = $config;
        $model->status = Release::RELEASE_STATUS_COMMITTED;
        $model->category = Release::RELEASE_CATEGORY_COMMIT;
        $model->save();

        $task = (new ReleaseItem());
        $task->release_id = $model->release_id;
        $task->name = ReleaseItem::CONFIG_KEY_CODE_COMMIT;
        $task->status = ReleaseItem::STATUS_SUCCESS;
        $task->push_config = $config;
        $task->response = json_encode($response);
        $task->mini_program_id = $miniProgram->mini_program_id;
        $task->save();

        return $model;
    }

    protected function genTradeNo($id)
    {
        $id = str_pad($id, 4, 0, 0);
        return 'R' . date('YmdHis') . $id . Str::random(5);
    }

    /**
     * @param \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application $app
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function callback(Application $app, ReleaseAudit $audit)
    {
        $response = $app->code->getAuditStatus($this->audit_id);

        $audit->component_id = $this->component_id;
        $audit->release_id = $this->release_id;
        $audit->status = $response['status'];
        $audit->save();

        $this->status = $this->getStatus($response['status']);
        $this->save();

        if ($audit->isSuccess() && $this->shouldRelease()) {
            ReleaseItem::createReleaseTask($this, $this->miniProgram, $this->config);
        }

        return true;
    }

    public function getStatus($originStatus)
    {
        $map = [
            ReleaseAudit::ORIGIN_AUDIT_STATUS_SUCCESS => Release::RELEASE_STATUS_AUDIT_SUCCESS,
            ReleaseAudit::ORIGIN_AUDIT_STATUS_FAILED => Release::RELEASE_STATUS_AUDIT_FAILED,
            ReleaseAudit::ORIGIN_AUDIT_STATUS_AUDITING => Release::RELEASE_STATUS_AUDITING,
            ReleaseAudit::ORIGIN_AUDIT_STATUS_REVERTED => Release::RELEASE_STATUS_AUDIT_REVERTED
        ];

        return $map[$originStatus];
    }

    public static function statistical($componentId, $templateId)
    {
        $collect = collect([
            'unset' => Release::RELEASE_STATUS_SETTING,
            'setting' => Release::RELEASE_STATUS_SETTING,
            'setted' => Release::RELEASE_STATUS_SETTING,
            'uncommitted' => Release::RELEASE_STATUS_UNCOMMITTED,
            'committed' => Release::RELEASE_STATUS_COMMITTED,
            'auditing' => Release::RELEASE_STATUS_AUDITING,
            'audit_failed' => Release::RELEASE_STATUS_AUDIT_FAILED,
            'audit_success' => Release::RELEASE_STATUS_AUDIT_SUCCESS,
            'audit_reverted' => Release::RELEASE_STATUS_AUDIT_REVERTED,
            'released' => Release::RELEASE_STATUS_RELEASED,
        ]);

        $return = $collect->map(function ($status) use ($componentId, $templateId) {
            return (new self())->componentTemplate($componentId, $templateId)->where('status', $status)->count();
        });

        return $return;
    }

    public function scopeComponentTemplate($query, $componentId, $templateId)
    {
        return $query->where('template_id', $templateId)->where('component_id', $componentId);
    }

    public function getStatusTrans()
    {
        return '';
    }

    public static function lastRelease(MiniProgram $miniProgram, Release $release)
    {
        $model  = (new self())
            ->where('release_id', '<', $release->release_id)
            ->where('mini_program_id', $miniProgram->mini_program_id)
            ->orderBy('release_id', 'desc')
            ->first();

        return $model;
    }

    public function retry($config)
    {
        if(!is_null($config)){
            $newConfig = json_encode($this->config, true);
            if(sha1($newConfig) === $this->config_version){
                throw new UnprocessableEntityHttpException(trans('配置文件未发生变动'));
            }
            $this->config = $newConfig;
            $this->save();
        }else{
            $config = json_decode($this->config, true);
        }

        $collect = ReleaseItem::make($this, $this->miniProgram, $config, true);

        return [
            'task_num' => $collect->count(),
            'app_id' => $this->miniProgram->app_id,
            'trade_no' => $this->trade_no
        ];
    }

    public function syncConfig($miniProgram, $config)
    {
        try {
            $tradeNo = $this->genTradeNo($miniProgram->mini_program_id);
            $model = (new self());
            $model->component_id = $miniProgram->component_id;
            $model->mini_program_id = $miniProgram->mini_program_id;
            $model->template_id = 0;
            $model->trade_no = $tradeNo;
            $model->config = json_encode($config, JSON_UNESCAPED_UNICODE);
            $model->config_version = sha1($model->config);
            $model->status = Release::RELEASE_STATUS_SETTING;
            $model->category = Release::RELEASE_CATEGORY_SETTING;
            $model->save();

            $collect = ReleaseItem::make($model, $miniProgram, $config, true);
        } catch (\Exception $e) {
            throw  $e;
        }

        return [
            'task_num' => $collect->count(),
            'app_id' => $miniProgram->app_id,
            'trade_no' => $tradeNo
        ];
    }

    public function shouldCommit()
    {
        return $this->category !== Release::RELEASE_CATEGORY_SETTING;
    }

    public function shouldAudit()
    {
        return  in_array($this->category,  [self::RELEASE_CATEGORY_AUDIT, self::RELEASE_CATEGORY_RELEASE]);
    }

    public function shouldRelease()
    {
        return  in_array($this->category,  [self::RELEASE_CATEGORY_RELEASE]);
    }
}
