<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Lsshu\Site\Api\Resources\CollectionResource;
use Lsshu\Site\Api\Resources\ModelResource;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /***
     * @var string Model::class
     */
    protected string $model = Model::class;
    /***
     * 过滤条件
     * "name" => "=",
     * "name" => ["like", "%{}%"]
     * @var array
     */
    protected array $FilterConditions = [];
    /***
     * 忽视 过滤的字段
     * @var array|string[]
     */
    protected array $IgnoreFilterConditions = ["page", "pageSize", "currentPage"];
    /***
     * 信任的 过滤字段
     * @var array
     */
    protected array $TrustFilterConditions = [];
    protected string|ModelResource $modelResource = ModelResource::class;
    protected string|CollectionResource $modelCollectionResource = CollectionResource::class;

    /***
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $pageSize = $request->get("pageSize",15);
        $where = $this->getFilterConditions($request);
        $data = $this->getModel()::where($where)->paginate($pageSize);
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
        DB::beginTransaction();
        try {
            $data['guard_name'] = $data['guard_name'] ?? config('site-api.root_guard_name', "site-api");
            $this->getModel()::create($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
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
        $object = $this->getModel()::findOrFail($id);
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
        $row = $this->getModel()::find($id);
        DB::beginTransaction();
        try {
            $row->fill($data)->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        return $this->response(null, 200, '请求成功！');
    }

    /***
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->getModel()::where("id", $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
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
        return $this->errorResponse("检查请求的数据", 203);
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

    protected function errorResponse($message = null, $statusCode = 202, $code = 0)
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
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $statusCode, ['X-Site-Api' => true]);
    }

    protected function getModel()
    {
        return $this->model;
    }


    /***
     * 获取列表筛选条件
     * @param Request $request
     * @return array
     */
    public function getFilterConditions(Request $request)
    {
        $params = $this->input($request);
        $where = [];
        foreach ($params as $key => $param) {
            if(!$this->TrustFilterConditions || in_array($key,$this->TrustFilterConditions)){
                if (!in_array($key, $this->IgnoreFilterConditions)) { // ignore 不要的
                    if ($param) {
                        $conditions = $this->FilterConditions[$key] ?? "=";
                        if (is_array($conditions)) { // 当是like 类型时
                            $param = str_replace("{}", $param, $conditions[1] ?? "");
                            $conditions = $conditions[0] ?? "=";
                        }
                        $param = $param === "null" ? null : $param;
                        $where[] = [$key, $conditions, $param];
                    }
                }
            }
        }
        return $where;
    }
}
