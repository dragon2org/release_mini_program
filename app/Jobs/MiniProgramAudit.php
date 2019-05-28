<?php

namespace App\Jobs;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\MiniProgram;
use App\Models\Release;
use App\Models\ReleaseItem;
use App\ReleaseConfigurator;
use App\Releaser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;


class MiniProgramAudit extends BaseReleaseJobWithLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $miniProgram;

    protected $config;

    protected $release;

    protected $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReleaseItem $task)
    {
        $this->task = $task;
        $this->miniProgram = $task->miniProgram;
        $this->config = json_decode($task->original_config, true);
        $this->release = $task->release;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proccess($this, function (Application $app) {
            $itemList = $app->code->getPage();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull audit page_list", $itemList);

            $category = $app->code->getCategory();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull audit category", $category);

            $itemList = $itemList['page_list'] ?? [];

            $category = $category['category_list'] ?? [];

            $i = 0;
            $auditItems = [];
            foreach ($category as $c) {
                $item = $c;
                $item['address'] = $itemList[$i];
                $item['tag'] = $c['first_class'] ?? '服务';
                $item['title'] = $i === 0 ? '首页' : '页面' . ($i + 1);
                $auditItems[] = $item;
                $i++;
            }
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push audit audit_config", $auditItems);

            $response = $app->code->submitAudit($auditItems);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push audit response", $response);

            $this->task->building($auditItems, $response, ReleaseItem::STATUS_SUCCESS, '');

            if (isset($response['auditid'])) {
                $this->release->audit_id = $response['auditid'];
                $this->release->status = Release::RELEASE_STATUS_AUDITING;
                $this->release->save();
            } else {
                $this->release->status = Release::RELEASE_STATUS_AUDIT_FAILED;
                $this->release->save();
            }
            return $response;
        });
    }
}
