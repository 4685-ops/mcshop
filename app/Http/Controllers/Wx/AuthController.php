<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        // 验证参数是否为空
        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return ['errno' => 401, 'errmsg' => '参数不对'];
        }
        // 验证手机号码格式
        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return ['errno' => 707, 'errmsg' => '手机号码格式不正确'];
        }
        //检查用户是否存在
        $user = (new UserServices())->getByUsername($username);
        if (!is_null($user)) {
            return ['errno' => 704, 'errmsg' => '用户名已注册'];
        }
        $user = (new UserServices())->getByMobile($mobile);
        if (!is_null($user)) {
            return ['errno' => 705, 'errmsg' => '手机号已注册'];
        }
        // 验证码是否正确


        // 写入用户表
        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->mobile = $mobile;
        $user->avatar = "https://img.xyzs.com/g/202104/151114285qrm.png";
        $user->nickname = $username;
        $user->last_login_time = Carbon::now()->toDateTimeString();
        $user->last_login_ip = $request->getClientIp();
        $user->save();
        // 用户发券
        // 返回信息和token
        return [
            'errno' => 0, 'errmsg' => '成功', 'data' => [
                'token' => '',
                'userInfo' => [
                    'nikeName' => $username,
                    'avatarUrl' => $user->avatar
                ]
            ]
        ];
    }

    /**
     * @throws \Exception
     */
    public function regCaptcha(Request $request)
    {
        // 获取参数
        $mobile = $request->input('mobile');
        // 验证参数是否为空
        if (empty($mobile)) {
            return ['errno' => 401, 'errmsg' => '参数不对'];
        }
        // 验证手机号码格式
        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return ['errno' => 707, 'errmsg' => '手机号码格式不正确'];
        }
        // 验证手机号是否注册
        $user = (new UserServices())->getByMobile($mobile);
        if (!is_null($user)) {
            return ['errno' => 705, 'errmsg' => '手机号已注册'];
        }

        $code = random_int(10000, 99999);


        //防刷验证 一分钟一次   一天只能请求10次

        $lock = Cache::add('register_captcha_lock_'.$mobile, 1, 600);
        if (!$lock) {
            return ['errno' => 702, 'errmsg' => '验证码未超过1分钟，不能发送'];
        }

        $countKey = 'register_captcha_count_'.$mobile;
        if (Cache::has($countKey)) {
            $count = Cache::increment($countKey);

            if ($count > 10) {
                return ['errno' => 702, 'errmsg' => '也只能当天发送不能超过10次'];
            }
        } else {
            Cache::put($countKey, 1, Carbon::tomorrow()->diffInSeconds(now()));
        }


        // 保存手机号码与验证码关系
        Cache::put('register_captcha_'.$mobile, $code, 600);


        // 发送短信
    }
}
