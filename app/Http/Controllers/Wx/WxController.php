<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
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
}
