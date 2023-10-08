<?php

namespace App\Services;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Support\Facades\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use App\Notifications\VerificationCode;
use function Symfony\Component\Translation\t;

class UserServices extends BaseServices
{
    public function getByUsername(string $name)
    {
        return User::query()->where('username', $name)->where('deleted', 0)->first();
    }


    public function getByMobile(string $mobile)
    {
        return User::query()->where('mobile', $mobile)->where('deleted', 0)->first();
    }

    public function checkMobileSendCaptchaCount(string $mobile): bool
    {
        $countKey = 'register_captcha_count_'.$mobile;
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

    public function sendCaptchaMsg($mobile, $code)
    {
        if (app()->environment('testing')) {
            return;
        }
        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile, 86)
        )->notify(new VerificationCode($code));
    }

    /**
     * @throws BusinessException
     */
    public function checkCaptcha(string $mobile, string $code): bool
    {
        if (!app()->environment('production')) {
            return true;
        }

        $key = "register_captcha_".$mobile;
        $isPass = $code === Cache::get($key);
        if ($isPass) {
            Cache::forget($key);
            return true;
        } else {
            throw new BusinessException(CodeResponse::AUTH_CAPTCHA_UNMATCH);
        }
    }

    /**
     * @throws \Exception
     */
    public function setCaptcha(string $mobile): string
    {
        $code = random_int(10000, 99999);
        $code = strval($code);
        // 保存手机号码与验证码关系
        Cache::put('register_captcha_'.$mobile, $code, 600);

        return $code;
    }

}
