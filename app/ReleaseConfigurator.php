<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/27
 * Time: 10:07 PM
 */

namespace App;


use Illuminate\Contracts\Support\Arrayable;

class ReleaseConfigurator implements Arrayable
{
    protected $domain;

    protected $webViewDomain;

    protected $tester;

    protected $extJson;

    protected $pageList;

    protected $visitStatus;

    protected $supportVersion;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function toArray()
    {
        return [];
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}