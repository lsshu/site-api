<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Lsshu\Site\Api\Resources\CollectionResource;
use Lsshu\Site\Api\Resources\ModelResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    protected string $model = Model::class;
    protected string|ModelResource $modelResource = ModelResource::class;
    protected string|CollectionResource $modelCollectionResource = CollectionResource::class;

    /***
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $data = $this->model::paginate();
        return new $this->modelCollectionResource($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /***
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->request($request);
        $this->model::create($data);
        return $this->response(null, 201, '请求成功！');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $object = $this->model::findOrFail($id);
        return new $this->modelResource($object);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /***
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $this->request($request);
        $this->model::where("id", $id)->update($data);
        return $this->response(null, 200, '请求成功！');
    }

    /***
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->model::where("id", $id)->delete();
        return $this->response(null, 200, '请求成功！');
    }

    /***
     * 检查请求的数据 并返回提交的数据
     * @param Request $request
     * @return mixed
     */
    protected function request(Request $request)
    {
        if ($request->hasHeader('X-Site-Api') && $request->accepts(['application/json'])) {
            return $this->input($request);
        }
        return $this->errorResponse(401, "");
    }

    /***
     * 处理提交的数据
     * @param Request $request
     * @return mixed
     */
    protected function input(Request $request)
    {
        return $request->all();
    }

    protected function errorResponse($statusCode, $message = null, $code = 0)
    {
        throw new HttpException($statusCode, $message, null, [], $code);
    }

    /***
     * @param $data null|array|boolean
     * @param $statusCode int
     * @param $message null|string
     * @param $code int
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($data = null, $statusCode = 200, $message = "success", $code = 0)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $statusCode, ['X-Site-Api' => true]);
    }
}
