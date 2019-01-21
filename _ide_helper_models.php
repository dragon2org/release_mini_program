<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\MiniProgram
 *
 * @property int $mini_program_id 自增id
 * @property int $component_id 关联的三方平台ID
 * @property int $company_id 公司id
 * @property string $inner_name 内部名称
 * @property string $inner_desc 内部描述
 * @property string $nick_name 授权方昵称
 * @property string $head_img 小程序头像
 * @property string $app_id 小程序AppID
 * @property string $user_name 原始ID,审核推送要用
 * @property string $principal_name 小程序主体名称
 * @property string $qrcode_url 二维码图片的URL
 * @property string $desc 小程序平台描述
 * @property string $authorizer_refresh_token 获取（刷新）授权公众号或小程序的接口调用凭据
 * @property string $user_version 当前版本
 * @property string $field1 备用字段
 * @property string $field2 备用字段2
 * @property int $deleted 软删除标志
 * @property \Illuminate\Support\Carbon $created_at 记录添加时间
 * @property \Illuminate\Support\Carbon $updated_at 记录更新时间
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tester[] $tester
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereAuthorizerRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereComponentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereField1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereField2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereHeadImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereInnerDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereInnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereMiniProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereNickName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram wherePrincipalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereQrcodeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MiniProgram whereUserVersion($value)
 */
	class MiniProgram extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Component
 *
 * @property int $component_id 自增id
 * @property string $inner_name 内部名称
 * @property string $inner_desc 内部描述
 * @property string $name 三方平台名称
 * @property string $desc 三方平台描述
 * @property string $app_id 三方平台AppID
 * @property string $app_secret 三方平台AppSecret
 * @property string $verify_token 三方平台消息验证token
 * @property string $verify_ticket 三方平台消息验证verify_ticket
 * @property string $aes_key 三方平台消息解密解密Key
 * @property string $validate_filename 三方平台验证域名-文件名
 * @property string $validate_content 三方平台验证域名-文件内容
 * @property string $field1 备用字段
 * @property string $field2 备用字段2
 * @property int $deleted 软删除标志
 * @property \Illuminate\Support\Carbon $created_at 记录添加时间
 * @property \Illuminate\Support\Carbon $updated_at 记录更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereAesKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereAppSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereComponentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereField1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereField2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereInnerDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereInnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereValidateContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereValidateFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereVerifyTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Component whereVerifyToken($value)
 */
	class Component extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tester
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tester query()
 */
	class Tester extends \Eloquent {}
}

