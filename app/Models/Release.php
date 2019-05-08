<?php

namespace App\Models;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Jobs\MiniProgramRelease;
use App\ReleaseConfigurator;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
        $taskNum = 0;
        try {
            DB::beginTransaction();
            $tradeNo = $this->genTradeNo($miniProgram->mini_program_id);
            $model = (new self());
            $model->component_id = $miniProgram->component_id;
            $model->mini_program_id = $miniProgram->mini_program_id;
            $model->template_id = $templateId;
            $model->trade_no = $tradeNo;
            $model->config = json_encode($config, JSON_UNESCAPED_UNICODE);
            $model->save();

            $taskNum = ReleaseItem::make($model, $miniProgram, $templateId, $config);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw  $e;
        }

        return $taskNum;
    }

    protected function genTradeNo($id)
    {
        $id = str_pad($id, 4, 0, 0);
        return 'R' .  date('YmdHis') . $id . Str::random(5);
    }

    public function getReleaseConfigurator()
    {
        $config = json_decode($this->config, true);
        return new ReleaseConfigurator($config);
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
            MiniProgramRelease::dispatch($this->miniProgram, $this);
        }

        return true;
    }

    public function getStatus($originStatus)
    {
        $map = [
            ReleaseAudit::ORIGIN_AUDIT_STATUS_SUCCESS => Release::RELEASE_STATUS_AUDIT_SUCCESS,
            ReleaseAudit::ORIGIN_AUDIT_STATUS_FAILED => Release::RELEASE_STATUS_AUDIT_FAILED,
            ReleaseAudit::ORIGIN_AUDIT_STATUS_AUDITING => Release::RELEASE_STATUS_AUDITING,
            self::RELEASE_STATUS_AUDIT_REVERTED => Release::RELEASE_STATUS_AUDIT_REVERTED
        ];

        return $map[$originStatus];
    }
}
