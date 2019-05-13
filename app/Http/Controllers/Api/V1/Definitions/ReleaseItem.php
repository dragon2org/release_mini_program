<?php


namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="ReleaseItem", type="object")
 */
class ReleaseItem
{
    /**
     * @SWG\Property(type="integer", description="任务id")
     */
    public $release_item_id;
    /**
     * @SWG\Property(type="integer", description="构建id")
     */
    public $release_id;

    /**
     * @SWG\Property(type="string", description="构建类型:")
     */
    public $name;

    /**
     * @SWG\Property(type="object", description="原始配置; 建议前段序列化后使用")
     */
    public $original_config;

    /**
     * @SWG\Property(type="object", description="线上配置; 建议前段序列化后使用")
     */
    public $online_config;

    /**
     * @SWG\Property(type="object", description="推送配置; 建议前段序列化后使用")
     */
    public $push_config;

    /**
     * @SWG\Property(type="object", description="请求响应; 建议前段序列化后使用")
     */
    public $response;

    /**
     * @SWG\Property(type="integer", description="状态")
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