<?php

namespace App\Http;

use EllipseSynergie\ApiResponse\Laravel\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ApiResponse extends Response
{
    /**
     * {@inheritdoc}
     */
    public function withArray(array $array = [], array $headers = []){
        $status['status'] = ($this->statusCode == '200') ? 'T' : 'F';
        $array = array_merge($status, $array);

        if(config('app.debug')){
            $this->withDebug($array);
        }

        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function withCollection($data, $transformer, $resourceKey = null, Cursor $cursor = null, $meta = [], array $headers = []){
        $resource = new Collection($data, $transformer, $resourceKey);

        foreach ($meta as $metaKey => $metaValue) {
            $resource->setMetaValue($metaKey, $metaValue);
        }

        if (!is_null($cursor)) {
            $resource->setCursor($cursor);
        }

        $rootScope = $this->manager->createData($resource);

        $response = $rootScope->toArray();
        if($data instanceof LengthAwarePaginator){
            $response['pagination'] = [
                'total' => $data->total(),
                'per_page' => intval($data->perPage()),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ];
        }

        return $this->withArray($response, $headers);
    }


    /**
     * 带扩展数据
     * @param $data
     * @param $transformer
     * @param array $ext
     * @param null $resourceKey
     * @param Cursor|null $cursor
     * @param array $meta
     * @param array $headers
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function withCollectionWithExt($data, $transformer, $ext =[], $resourceKey = null, Cursor $cursor = null, $meta = [], array $headers = []){
        // var_dump( get_class($data), $data->toArray());exit;
        $resource = new Collection($data, $transformer, $resourceKey);

        foreach ($meta as $metaKey => $metaValue) {
            $resource->setMetaValue($metaKey, $metaValue);
        }

        if (!is_null($cursor)) {
            $resource->setCursor($cursor);
        }

        $rootScope = $this->manager->createData($resource);

        $response = $rootScope->toArray();
        if($data instanceof LengthAwarePaginator){
            $response['pagination'] = [
                'total' => $data->total(),
                'per_page' => intval($data->perPage()),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ];
        }

        //自定义扩展数据
        if (!empty($ext)){
            $response = array_merge($response, $ext);
        }
        return $this->withArray($response, $headers);
    }


    public function withCollectionArray($data, $transformer, $resourceKey = null, Cursor $cursor = null, $meta = [], array $headers = []){
        $response = array();
        foreach ($data as $key => $item) {
            $resource = new Collection($item, $transformer, $resourceKey);

            foreach ($meta as $metaKey => $metaValue) {
                $resource->setMetaValue($metaKey, $metaValue);
            }

            if (!is_null($cursor)) {
                $resource->setCursor($cursor);
            }
            $rootScope = $this->manager->createData($resource);
            $response[$key] = $rootScope->toArray()['data'];
        }

        return $this->withArray(['data' => $response], $headers);
    }


    public function transformatItem($data, $transformer, $resourceKey = null, $meta = [], array $headers = [])
    {
        if (empty($data)){
            return $data;
        }

        $resource = new Item($data, $transformer, $resourceKey);

        foreach ($meta as $metaKey => $metaValue) {
            $resource->setMetaValue($metaKey, $metaValue);
        }

        $rootScope = $this->manager->createData($resource);

        $data = $rootScope->toArray();
        return $data['data'];
    }

    public function transformatCollection($data, $transformer, $resourceKey = null, Cursor $cursor = null, $meta = [], array $headers = []){
        if (empty($data)){
            return $data;
        }

        $resource = new Collection($data, $transformer, $resourceKey);

        foreach ($meta as $metaKey => $metaValue) {
            $resource->setMetaValue($metaKey, $metaValue);
        }

        if (!is_null($cursor)) {
            $resource->setCursor($cursor);
        }

        $rootScope = $this->manager->createData($resource);

        $data = $rootScope->toArray();
        return $data['data'];
    }

    public function withItem($data, $transformer, $resourceKey = null, $meta = [], array $headers = [])
    {
        if (empty($data)){
            return $this->withArray([
                'data' => []
            ]);
        }

        return parent::withItem($data, $transformer, $resourceKey, $meta, $headers);
    }

    protected function withDebug(&$data)
    {
        $debug['header'] = request()->header();
        $debug['url'] = request()->getUri();
        $debug['queryParams'] = request()->query();
        $debug['body'] = request()->getContent();

        $data['request_params'] = $debug;
    }
}