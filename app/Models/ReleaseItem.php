<?php

namespace App\Models;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Jobs\MiniProgramAudit;
use App\Jobs\MiniProgramRelease;
use App\Jobs\SetMiniProgramCodeCommit;
use App\Jobs\SetMiniProgramDomain;
use App\Jobs\SetMiniProgramSupportVersion;
use App\Jobs\SetMiniProgramTester;
use App\Jobs\SetMiniProgramVisitStatus;
use App\Jobs\SetMiniProgramWebViewDomain;
use App\Logs\ReleaseInQueueLog;
use App\Logs\RetryReleaseInQueueLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReleaseItem extends Model
{
    use SoftDeletes;

    const CONFIG_KEY_DOMAIN = 'domain';

    const CONFIG_KEY_WEB_VIEW_DOMAIN = 'web_view_domain';

    const CONFIG_KEY_AUDIT = 'audit';

    const CONFIG_KEY_CODE_COMMIT = 'code_commit';

    const CONFIG_KEY_RELEASE = 'release';

    const CONFIG_KEY_SUPPORT_VERSION = 'support_version';

    const CONFIG_KEY_TESTER = 'tester';

    const CONFIG_KEY_VISIT_STATUS = 'visit_status';

    const NAME_REVERT_AUDIT = 'revert_audit';

    const SUPPORT_CONFIG_KEY = [
        'domain', 'web_view_domain', 'audit', 'code_commit', 'release', 'support_version', 'tester', 'visit_status'
    ];

    const STATUS_PREPARE = 0;

    const STATUS_PROCESSING = 1;

    const STATUS_SUCCESS = 2;

    const STATUS_FAILED = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'release_item';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_item_id';

    protected $fillable = [
        'release_id', 'name', 'original_config', 'push_config', 'online_config', 'response', 'status', 'mini_program_id',
    ];

    public function miniProgram()
    {
        return $this->belongsTo(MiniProgram::class, 'mini_program_id', 'mini_program_id');
    }


    public function release()
    {
        return $this->belongsTo(Release::class, 'release_id', 'release_id');
    }

    public static function make(Release $release, MiniProgram $miniProgram, $config)
    {
        $result = [];
        foreach($config as $key => $value){
            if(!in_array($key, self::SUPPORT_CONFIG_KEY)){
                continue;
            }

            $task =  self::create([
                'release_id' => $release->release_id,
                'mini_program_id' => $miniProgram->mini_program_id,
                'name' => $key,
                'original_config' => json_encode($value),
                'status' => self::STATUS_PREPARE
            ]);
            $className = 'App\Jobs\SetMiniProgram' . Str::studly($key);
            $className::dispatch($task);
            ReleaseInQueueLog::info($release->trade_no, $miniProgram, $config, $release->release_id, $className);
            $result[] = $task;
        }

        $result[] = ReleaseItem::createCommitTask($release, $miniProgram, $config);

        return collect($result);
    }

    public static function createCommitTask(Release $release, MiniProgram $miniProgram, $config)
    {
        $key = self::CONFIG_KEY_CODE_COMMIT;
        $task =  self::create([
            'release_id' => $release->release_id,
            'mini_program_id' => $miniProgram->mini_program_id,
            'name' => self::CONFIG_KEY_CODE_COMMIT,
            'original_config' => json_encode($config),
            'status' => self::STATUS_PREPARE
        ]);

        $tradeNo = $release->trade_no;
        $className = 'App\Jobs\SetMiniProgram' . Str::studly($key);
        $className::dispatch($task);
        ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $release->release_id, $className);

        return $task;
    }

    public static function createAuditTask(Release $release, MiniProgram $miniProgram, $config)
    {
        $key = self::CONFIG_KEY_AUDIT;
        $task =  self::create([
            'release_id' => $release->release_id,
            'mini_program_id' => $miniProgram->mini_program_id,
            'name' => $key,
            'original_config' => json_encode($config),
            'status' => self::STATUS_PREPARE
        ]);

        $tradeNo = $release->trade_no;
        MiniProgramAudit::dispatch($task);
        ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $release->release_id, MiniProgramAudit::class);

        return $task;
    }

    public static function createReleaseTask(Release $release, MiniProgram $miniProgram, $config)
    {
        $task =  self::create([
            'release_id' => $release->release_id,
            'mini_program_id' => $miniProgram->mini_program_id,
            'name' => self::CONFIG_KEY_RELEASE,
            'status' => self::STATUS_PREPARE
        ]);

        $tradeNo = $release->trade_no;
        MiniProgramRelease::dispatch($task);
        ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $release->release_id, MiniProgramRelease::class);

        return $task;
    }

    public function building($pushConfig, $response, $status, $onlineConfig = [])
    {
        if(isset($data['online_config'])) $this->online_config = json_encode($onlineConfig);
        $this->push_config = json_encode($pushConfig);
        $this->response = json_encode($response);
        $this->status = $status;

        $this->save();
    }

    public function applyBuilding()
    {
        $this->status = self::STATUS_PROCESSING;
        $this->save();
    }

    public function applyBuildFailed()
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }

    public function applyBuildSuccess()
    {
        $this->status = self::STATUS_SUCCESS;
        $this->save();
    }

    public function getStatusTrans()
    {
        switch ($this->status){
            case self::STATUS_PREPARE:
                return '正在等待';
            case self::STATUS_PROCESSING:
                return '正在构建';
            case self::STATUS_SUCCESS:
                return '构建成功';
            case self::STATUS_FAILED:
                return '构建失败';
            default:
                return '未知';
        }
    }

    public function scopeComponentTemplate($query, $releaseId)
    {
        return $query->where('release_id', $releaseId);
    }

    public static function statistical($releaseId)
    {
        $collect = collect([
            'prepare' => ReleaseItem::STATUS_PREPARE,
            'processing' => ReleaseItem::STATUS_PROCESSING,
            'success' => ReleaseItem::STATUS_SUCCESS,
            'failed' => ReleaseItem::STATUS_FAILED,
        ]);

        $return = $collect->map(function ($status) use ($releaseId) {
            return  (new self())->componentTemplate($releaseId)->where('status', $status)->count();
        });

        return $return;
    }

    public function retry($releaseItemId, $config = null)
    {
        $releaseItem = ReleaseItem::find($releaseItemId);

        if(!isset($releaseItem)){
            throw new UnprocessableEntityHttpException(trans('任务不存在'));
        }

        if($config){
            $releaseItem->original_config = json_encode($config);
            $releaseItem->save();
        }

        $map = [
            ReleaseItem::CONFIG_KEY_DOMAIN => SetMiniProgramDomain::class,
            ReleaseItem::CONFIG_KEY_WEB_VIEW_DOMAIN => SetMiniProgramWebViewDomain::class,
            ReleaseItem::CONFIG_KEY_TESTER => SetMiniProgramTester::class,
            ReleaseItem::CONFIG_KEY_VISIT_STATUS => SetMiniProgramVisitStatus::class,
            ReleaseItem::CONFIG_KEY_SUPPORT_VERSION => SetMiniProgramSupportVersion::class,
            ReleaseItem::CONFIG_KEY_CODE_COMMIT => SetMiniProgramCodeCommit::class,
            ReleaseItem::CONFIG_KEY_AUDIT => MiniProgramAudit::class,
            ReleaseItem::CONFIG_KEY_RELEASE => MiniProgramRelease::class,
        ];
        if(!isset($map[$releaseItem->name])){
            throw new UnprocessableEntityHttpException(trans('不支持的操作类型'));
        }

        $class = $map[$releaseItem->name];
        $class::dispatch($releaseItem)->onConnection('sync');
        RetryReleaseInQueueLog::info($releaseItem->release->trade_no, $releaseItem->miniProgram, json_decode($releaseItem->original_config, true), $releaseItem->release->release_id, $class);

        return true;
    }
}
