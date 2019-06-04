# 部署日志

## web入口

- public/index.php   允许外网访问
- gateway/index.php  内网访问

路由隔离实现方式
```php
    //产品环境不加载服务网关路由
    env('APP_ENV') === 'prod' && define('ACCEPT_SERVICE_API', false);
    
    //file App\Providers\RouteServiceProvider
    public function map()
    {
        if(defined('ACCEPT_SERVICE_API') && ACCEPT_SERVICE_API === true){
            $this->mapApiRoutes();
        }

        $this->mapWebRoutes();
    }
```
