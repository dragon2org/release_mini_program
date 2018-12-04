<?php

namespace Http\Controllers\Api\V1\Response;


/**
 * @SWG\Definition(
 *   definition="ExceptionReponse",
 *   type="object"
 * )
 */
class ExceptionReponse
{
    /**
     * @SWG\Property(type="string", description="状态, 值为F")
     */
    public $status;

    /**
     * @SWG\Property(
     *     type="object",
     *     @SWG\Property(
     *         property="message",
     *         type="string",
     *         description="错误描述"
     *     ),
     * )
     */
    public $error;
}
