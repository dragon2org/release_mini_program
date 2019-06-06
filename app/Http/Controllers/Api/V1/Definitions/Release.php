<?php


namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Release", type="object")
 */
class Release
{
    /**
     * @SWG\Property(type="integer", description="构建id")
     */
    public $release_id;

    /**
     * @SWG\Property(type="integer", description="模板id")
     */
    public $template_id;

    /**
     * @SWG\Property(type="string", description="发布版本的用户版本")
     */
    public $user_version;

    /**
     * @SWG\Property(type="string", description="发布版本的用户描述")
     */
    public $user_desc;

    /**
     * @SWG\Property(type="object", description="构建配置;为方便查看，返回的json对象.前端需要做序列化处理看情况展示。")
     */
    public $config;

    /**
     * @SWG\Property(type="string", description="内部名称")
     */
    public $desc;

    /**
     * @SWG\Property(type="integer", description="审核id")
     */
    public $audit_id;

    /**
     * @SWG\Property(type="string", description="流水号")
     */
    public $trade_no;

    /**
     * @SWG\Property(type="integer", description="构建状态")
     */
    public $status;

    /**
     * @SWG\Property(type="string", description="构建状态中文")
     */
    public $status_trans;

    /**
     * @SWG\Property(type="string", description="创建时间")
     */
    public $created_at;

    /**
     * @SWG\Property(type="string", description="更新时间")
     */
    public $updated_at;
}