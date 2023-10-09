<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Http\Controllers\Controller;
use App\VerifyRequestInput;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WxController extends Controller
{
    use VerifyRequestInput;

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

    public function userId()
    {
        return $this->user()->id;
    }

    public function isLogin(): bool
    {
        return (bool)$this->userId();
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
    )
    {
        if ($isSuccess) {
            return $this->success($data);
        }
        return $this->fail($codeResponse, $info);
    }

    /**
     * @param $page
     * @param null $list
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successPaginate($page, $list = null): \Illuminate\Http\JsonResponse
    {
        return $this->success($this->paginate($page, $list));
    }

    /**
     * @param LengthAwarePaginator|array $page
     * @param null|array $list
     * @return array
     */
    protected function paginate($page, $list = null)
    {
        if ($page instanceof LengthAwarePaginator) {
            $total = $page->total();
            return [
                'total' => $page->total(),
                'page' => $total == 0 ? 0 : $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $total == 0 ? 0 : $page->lastPage(),
                'list' => $list ?? $page->items()
            ];
        }

        if ($page instanceof Collection) {
            $page = $page->toArray();
        }
        if (!is_array($page)) {
            return $page;
        }

        $total = count($page);
        return [
            'total' => $total,
            'page' => $total == 0 ? 0 : 1,
            'limit' => $total,
            'pages' => $total == 0 ? 0 : 1,
            'list' => $page
        ];
    }

    /**
     * 401
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badArgument(): \Illuminate\Http\JsonResponse
    {
        return $this->fail(CodeResponse::PARAM_ILLEGAL);
    }

    /**
     * 402
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badArgumentValue(): \Illuminate\Http\JsonResponse
    {
        return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
    }


}
