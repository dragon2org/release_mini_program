<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseItem extends Model
{
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

    const CONFIG_KEY_DOMAIN = 'domain';

    const CONFIG_KEY_WEB_VIEW_DOMAIN = 'web_view_domain';

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
}
