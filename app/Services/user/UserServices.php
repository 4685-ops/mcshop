<?php

namespace App\Services\user;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Services\BaseService;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserServices extends BaseService
{
    public function getByMobile(string $mobile)
    {
        return User::query()->where('deleted', 0)->where('mobile', $mobile)->first();
    }

    public function getByUsername(string $name)
    {
        return User::query()->where('deleted', 0)->where('username', $name)->first();
    }

    /**
     * @throws \Exception
     */
    public function checkMobileSendCaptchaCount(string $mobile): bool
    {
        $countKey = 'register_captcha_count_' . $mobile;
        if (Cache::has($countKey)) {
            $count = Cache::increment($countKey);
            if ($count > 10) {
                return false;
            }
        } else {
            Cache::put($countKey, 1, Carbon::tomorrow()->diffInSeconds(now()));
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function setCaptcha(string $mobile): string
    {
        // 随机生成6位验证码
        $code = random_int(100000, 999999);

        // 非生产环境固定短信验证码
        if (!app()->environment('production')) {
            $code = 111111;
        }

        $code = strval($code);
        Cache::put('register_captcha_' . $mobile, $code, 600);
        return $code;
    }

    public function sendCaptchaMsg(string $mobile, int $code)
    {
        if (app()->environment('testing')) {
            return;
        }
        // 发送短信
        //Notification::route(
        //    EasySmsChannel::class,
        //    new PhoneNumber($mobile, 86)
        //)->notify(new VerificationCode($code));

        return true;
    }

    public function checkCaptcha(string $mobile, int $code): bool
    {
        $key = 'register_captcha_' . $mobile;

        $isPass = $code == Cache::get($key);

        if ($isPass) {
            Cache::forget($key);
            return true;
        } else {
            throw new BusinessException(CodeResponse::AUTH_CAPTCHA_UNMATCH);
        }
    }

    public function getUsers($userIds){
        return User::query()->where('deleted', 0)
            ->whereIn('id', $userIds)
            ->get();
    }
}
