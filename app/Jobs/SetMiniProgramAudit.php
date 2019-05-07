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


class SetMiniProgramAudit extends BaseReleaseJobWithLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $miniProgram;

    protected $config;

    protected $release;

    const VERSION = '1.0.0';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MiniProgram $miniProgram, Release $release)
    {
        $this->miniProgram = $miniProgram;
        $this->config = $release->getReleaseConfigurator();
        $this->release = $release;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proccess($this, function(Application $app){
            $itemList = $app->code->getPage();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull audit page_list", $itemList);

            $category = $app->code->getCategory();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull audit category", $category);

            $itemList = $itemList['page_list'] ?? [];

            $category = $category['category_list'] ?? [];

            $i=0;
            $auditItems = [];
            foreach($category as $c){
                $item = $c;
                $item['address'] = $itemList[$i];
                $item['tag'] = $c['first_class'] ?? '服务';
                $item['title'] = $i ===0 ? '首页' : '页面'. ($i + 1);
                $auditItems[] = $item;
                $i++;
            }
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push audit audit_config", $auditItems);

            $response = $app->code->submitAudit($auditItems);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push audit response", $response);

            ReleaseItem::createReleaseLog($this->release, ReleaseItem::CONFIG_KEY_AUDIT, [
                'online_config' => '',
                'original_config'=> '',
                'push_config' => $auditItems,
                'response' => $response
            ]);

            if(isset($response['auditid'])){
                $this->release->audit_id =$response['auditid'];
                $this->release->save();
            }
            return true;
        });
    }
}
