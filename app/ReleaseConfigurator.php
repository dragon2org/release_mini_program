<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/27
 * Time: 10:07 PM
 */

namespace App;


use function GuzzleHttp\default_ca_bundle;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class ReleaseConfigurator implements Arrayable
{
    protected $domain;

    protected $webViewDomain;

    protected $tester;

    protected $extJson;

    protected $pageList;

    protected $visitStatus;

    protected $supportVersion;

    protected $rawBody;

    public function __construct(array $rawBody)
    {
        $this->rawBody = $rawBody;
        $this->parseRawBody();
    }

    protected function parseRawBody()
    {
        foreach($this->rawBody as $name => $value){
            switch ($name) {
                case 'domain':
                    $this->domain = $value;
                    $this->domain['action'] =  'set';
                    break;
                case 'web_view_domain':
                    $this->webViewDomain = $value;
                    $this->webViewDomain['action'] = 'set';
                    break;
                case 'ext_json':
                    $this->extJson = json_encode($value);
                    break;
                case 'page_list':
                    $this->pageList = $value;
                    break;
                case 'visit_status':
                    $this->visitStatus = $value;
                    break;
                case 'support_version':
                    $this->supportVersion = $value;
                    break;
                default:
                    if(in_array($name, ['tester'])){
                        $this->{$name} = $value;
                    }
                    break;
            }
        }
    }


    public function toArray()
    {
        return $this->rawBody;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function getDomain()
    {
        return array_merge($this->domain, ['action' => 'set']);
    }
}