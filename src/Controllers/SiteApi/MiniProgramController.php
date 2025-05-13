<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Lsshu\Site\Api\PlugIn\EasyWeChat;

class MiniProgramController extends Controller
{
    /***
     * 微信小程序登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function user_login(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $this->input($request);
        $app = EasyWeChat::miniProgram();
        return $this->response($app->auth->session($params["code"] ?? ""));
    }

    /***
     * 获取微信小程序手机号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_user_phone_number(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $this->input($request);
        $app = EasyWeChat::miniProgram();
        $response = $app->httpPostJson('/wxa/business/getuserphonenumber', [
            "code" => $params['code'] ?? ""
        ]);
        return $this->response($response);
    }

    /***
     * 解密微信手机号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function user_phone_number(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $this->input($request);
        $app = EasyWeChat::miniProgram();
        return $this->response($app->encryptor->decryptData(
            $params["session_key"] ?? "",
            $params["iv"] ?? "",
            $params["encryptedData"] ?? ""
        ));
    }

    /***
     * 解密用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function user_info(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = $this->input($request);
        $app = EasyWeChat::miniProgram();
        return $this->response($app->encryptor->decryptData(
            $params["session_key"] ?? "",
            $params["iv"] ?? "",
            $params["encryptedData"] ?? ""
        ));
    }
}
