<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Http\Controllers\Controller;
use Auth;

class WxController extends Controller
{
    protected $only;
    protected $except;
    public function __construct()
    {
        $option = [];
        if (!is_null($this->only)) {
            $option['only'] = $this->only;
        }

        if (!is_null($this->except)) {
            $option['except'] = $this->except;
        }

        $this->middleware('auth:wx', $option);
    }
    public function user()
    {
        return Auth::guard('wx')->user();
    }

    protected function success($data = null): \Illuminate\Http\JsonResponse
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data);
    }

    protected function fail(array $codeResponse = CodeResponse::FAIL, $info = ''): \Illuminate\Http\JsonResponse
    {
        return $this->codeReturn($codeResponse, null, $info);
    }

    protected function codeReturn(array $codeResponse, $data = null, $info = ''): \Illuminate\Http\JsonResponse
    {
        list($errno, $errmsg) = $codeResponse;
        $ret = ['errno' => $errno, 'errmsg' => $info ?: $errmsg];
        if (!is_null($data)) {
            if (is_array($data)) {
                $data = array_filter($data, function ($item) {
                    return $item !== null;
                });
            }
            $ret['data'] = $data;
        }
        return response()->json($ret);
    }

    protected function failOrSuccess(
        $isSuccess,
        array $codeResponse = CodeResponse::FAIL,
        $data = null,
        $info = ''
    ) {
        if ($isSuccess) {
            return $this->success($data);
        }
        return $this->fail($codeResponse, $info);
    }
}
