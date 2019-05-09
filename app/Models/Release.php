<?php

namespace App\Models;


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

    CONST RELEASE_STATUS_UNCOMMITTED = 0;

    CONST RELEASE_STATUS_COMMITTED = 10;

    CONST RELEASE_STATUS_AUDITING = 11;

    CONST RELEASE_STATUS_AUDIT_FAILED = 13;

    CONST RELEASE_STATUS_AUDIT_SUCCESS = 12;

    CONST RELEASE_STATUS_AUDIT_REVERTED = 14;

    CONST RELEASE_STATUS_RELEASED = 20;

    public $buildItems = [];

    public function item()
    {
        return $this->hasMany(ReleaseItem::class, 'release_id', release_id);
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
        $model->save();

        $task = (new ReleaseItem());
        $task->release_id = $model->release_id;
        $task->name = ReleaseItem::CONFIG_KEY_CODE_COMMIT;
        $task->status = ReleaseItem::STATUS_SUCCESS;
        $task->push_config = $config;
        $task->response = json_encode($response);
        $task->mini_program_id = $miniProgram->mini_program_id;

        return $model;
    }

    protected function genTradeNo($id)
    {
        $id = str_pad($id, 4, 0, 0);
        return 'R' .  date('YmdHis') . $id . Str::random(5);
    }

    /**
     * @param \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application $app
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function callback(Application $app)
    {
        $response = $app->code->getAuditStatus($this->audit_id);

        $audit = (new ReleaseAudit());
        $audit->component_id = $this->component_id;
        $audit->mini_program_id = $this->mini_program_id;
        $audit->release_id = $this->release_id;
        $audit->status = $response['status'];
        $audit->reason = Arr::get($response, 'reason', '');
        $audit->screenshot = Arr::get($response, 'screenshot', '');
        $audit->save();

        $this->status = $this->getStatus($response['status']);
        $this->save();

        if($this->release_on_audited){
            $task = ReleaseItem::createReleaseTask($this, $this->miniProgram, $this->config);
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
}
