<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends WxController
{
    /**
     * @throws BusinessException
     */
    public function register(Request $request)
    {
        // 获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        // 验证参数是否为空
        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        // 验证手机号码格式
        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return $this->fail(CodeResponse::AUTH_INVALID_MOBILE);

        }
        //检查用户是否存在
        $user = UserServices::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);

        }

        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_MOBILE_REGISTERED);
        }

        // 验证码是否正确
        UserServices::getInstance()->checkCaptcha($mobile, $code);

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
            return $this->fail(CodeResponse::AUTH_INVALID_MOBILE);

        }
        // 验证手机号是否注册
        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_MOBILE_REGISTERED);
        }

        //防刷验证十分钟一次   一天只能请求10次
        $lock = Cache::add('register_captcha_lock_'.$mobile, 1, 60);
        if (!$lock) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY);

        }
        $isPass = UserServices::getInstance()->checkMobileSendCaptchaCount($mobile);

        if (!$isPass) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY, "验证码当天发送最多10条");
        }

        $code = UserServices::getInstance()->setCaptcha($mobile);

        // 发送短信
        UserServices::getInstance()->sendCaptchaMsg($mobile, $code);

        return $this->success();
    }
}
