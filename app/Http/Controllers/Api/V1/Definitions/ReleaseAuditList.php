<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:42
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(
 *     definition="ReleaseAuditList",
 *     type="object"
 * )
 */
class ReleaseAuditList
{
    /**
     * @SWG\Property(type="integer", description="审核状态，其中0为审核成功，1为审核失败，2为审核中，3已撤回")
     */
    public $status;

    /**
     * @SWG\Property(type="string", description="当status=1，审核被拒绝时，返回的拒绝原因")
     */
    public $reason;

    /**
     * @SWG\Property(type="string", description="当status=1，审核被拒绝时，会返回审核失败的小程序截图示例。")
     */
    public $screenshot;

    /**
     * @SWG\Property(type="string", description="记录创建时间")
     */
    public $created_at;
}
