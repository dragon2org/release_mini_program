<?php

namespace App\Models;

use App\Jobs\SetMiniProgramDomain;
use App\Jobs\SetMiniProgramWebViewDomain;
use App\Logs\ReleaseInQueueLog;
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

    const SUPPORT_CONFIG_KEY = [
        'domain', 'web_view_domain', 'audit', 'code_commit', 'release', 'support_version', 'tester', 'visit_status'
    ];

    const STATUS_PREPARE = 0;

    const STATUS_ING = 1;

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
        'release_id', 'name', 'original_config', 'status', 'mini_program_id',
    ];

    public function miniProgram()
    {
        return $this->belongsTo(MiniProgram::class, 'mini_program_id', 'mini_program_id');
    }


    public function release()
    {
        return $this->belongsTo(Release::class, 'release_id', 'release_id');
    }

    public static function createReleaseLog(Release $release, $type, $params = [])
    {
        $param = array_merge([
            'online_config' => [],
            'push_config' => [],
            'response' => [],
            'original_config' => [],
        ], $params);

        $model = (new self());
        $model->name = $type;
        $model->release_id = $release->release_id;
        $model->online_config = json_encode($params['online_config'], JSON_UNESCAPED_UNICODE);
        $model->push_config = json_encode($params['push_config'], JSON_UNESCAPED_UNICODE);
        $model->original_config = json_encode($params['original_config'], JSON_UNESCAPED_UNICODE);
        $model->response = json_encode($params['response'], JSON_UNESCAPED_UNICODE);

        $errcode = $params['response']['errcode'] ?? null;
        $model->status = 0;
        if($errcode === 0){
            $model->status = 1;
        }
        return $model->save();
    }

    public static function make(Release $release, MiniProgram $miniProgram, $templateId, $config)
    {
        $taskNum = 0;
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
            $tradeNo = $release->trade_no;
            $className = 'App\Jobs\SetMiniProgram' . Str::studly($key);
            $className::dispatch($task);
            ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, $className);
            $taskNum++;
        }

        return $taskNum;
    }

    public function building($pushConfig, $response, $status, $onlineConfig = [])
    {
        if(isset($data['online_config'])) $this->online_config = json_encode($onlineConfig);
        $this->push_config = json_encode($pushConfig);
        $this->response = json_encode($response);
        $this->status = $status;

        $this->save();
    }
}
