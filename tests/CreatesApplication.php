<?php

namespace Tests;

use App\Helpers\Obj;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::setRounds(4);

        return $app;
    }

    protected function seeJsonPrototype(array $prototypes)
    {
        $json = json_decode($this->response->getContent());
        if (is_null($json) || $json === false) {
            return $this->fail('Invalid JSON was returned from the route. Perhaps an exception was thrown?');
        }

        foreach($prototypes as $name => $prototype){
            $this->assertInternalType($prototype, Obj::get($json, $name));
        }
    }

    protected function seeJsonPrototypeByDocs($routeName, $method)
    {
        $docs = json_decode($this->docs(), true);
        if (is_null($docs) || $docs === false) {
            return $this->fail('Invalid JSON was api-docs.json');
        }

        $properties = $docs['paths'][urldecode(route($routeName, [], false))][$method]['responses']['200']['schema']['properties'];

        $prototypes = Arr::dot($this->prototypes($properties, $docs));

        return $this->seeJsonPrototype($prototypes);
    }

    private function prototypes($properties, $docs)
    {
        $prototypes = [];
        foreach($properties as $propertyName => $property){
            if(isset($property['items']) && isset($property['items']['$ref'])){
                $model = array_reverse(explode('/', $property['items']['$ref']))[0];
                $definition = $docs['definitions'][$model];
                $prototypes[$propertyName] = $definition['type'];
                $prototypes[$propertyName] = $this->prototypes($definition['properties'], $docs);
            }else{
                $prototypes[$propertyName] = $property['type'];
            }
        }
        return $prototypes;
    }

    private function docs()
    {
        defined('API_HOST') or define('API_HOST', '');

        $appDir = base_path()."/".Config::get('swaggervel.app-dir');

        $excludeDirs = Config::get('swaggervel.excludes');

        $swagger =  \Swagger\scan($appDir, [
            'exclude' => $excludeDirs
        ]);

        return $swagger;
    }
}
