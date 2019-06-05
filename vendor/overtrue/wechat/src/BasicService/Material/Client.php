<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\BasicService\Material;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Http\StreamResponse;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Fetch material.
     *
     * @param string $mediaId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $mediaId)
    {
        $response = $this->requestRaw('cgi-bin/material/get_material', 'POST', ['json' => ['media_id' => $mediaId]]);

        if (false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }
}
