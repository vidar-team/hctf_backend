<?php

namespace App\Services;

/**
 * Class APIReturnService
 * 格式化API返回
 * @author Eridanus Sora <sora@sound.moe>
 * @package App\Services
 */
class APIReturnService
{
    /**
     * @param $status
     * @param $data
     * @param $httpCode
     * @param null $redirect
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    protected function APIReturn($status, $data, $httpCode, $redirect = null)
    {
        $body = [
            "status" => $status,
            "data" => $data
        ];
        if ($redirect) {
            $body["redirect"] = $redirect;
        }
        return response()->json($body, $httpCode);
    }

    /**
     * @param array $data 返回数据
     * @param null $redirect 重定向
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $redirect = null)
    {
        return $this->APIReturn('success', $data, 200, $redirect);
    }

    /**
     * @param string $code 错误代码
     * @param mixed $message 错误信息
     * @param int $httpCode
     * @param null $redirect 重定向
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($code, $message, $httpCode = 500, $redirect = null)
    {
        return $this->APIReturn('fail', [
            'error' => [
                "code" => $code,
                "message" => $message
            ]
        ], $httpCode, $redirect);
    }
}