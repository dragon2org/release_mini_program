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
                'errmsg' => $this->message
            ],
            'status' => 'F'
        ], 422);
    }
}