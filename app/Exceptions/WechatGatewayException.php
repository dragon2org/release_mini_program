<?php


namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

class WechatGatewayException extends HttpException
{
    protected $message = 'Wechat Gateway Error';

    protected $code = 0;
    /**
     * @param string     $message  The internal exception message
     * @param int        $code     The internal exception code
     */
    public function __construct($message = null, $code = -1)
    {
        parent::__construct(422, $message, null, array(), $code);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'message' => '微信网关服务错误: ' . $this->message,
            'errors' => [
                'errcode' => $this->code,
                'errmsg' => $this->getErrorMessage(),
            ],
            'status' => 'F'
        ], 422);
    }

    public function getErrorMessage()
    {
        return $this->errorMap[$this->code] ?? $this->message;
    }

    public $errorMap = [
        -1 => '系统繁忙',
        86000 => '不是由第三方代小程序进行调用',
        86001 => '不存在第三方的已经提交的代码',
        85006 => '标签格式错误',
        85007 => '类目填写错误',
        85008 => '类目填写错误',
        85009 => '已经有正在审核的版本',
        85010 => 'item_list有项目为空',
        85011 => '标题填写错误',
        85023 => '审核列表填写的项目数不在1-5以内',
        85077 => '小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）',
        86002 => '小程序还未设置昵称、头像、简介。请先设置完后再重新提交',
        85085 => '近7天提交审核的小程序数量过多，请耐心等待审核完毕后再次提交',
        85086 => '提交代码审核之前需提前上传代码',
        85087 => '小程序已使用api navigateToMiniProgram，请声明跳转appid列表后再次提交',
    ];
}