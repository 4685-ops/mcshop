<?php

namespace App\Services;

use App\Models\User;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Support\Facades\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use App\Notifications\VerificationCode;
class UserServices
{
    public function getByUsername(string $name)
    {
        return User::query()->where('username', $name)->where('deleted', 0)->first();
    }


    public function getByMobile(string $mobile)
    {
        return User::query()->where('mobile', $mobile)->where('deleted', 0)->first();
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
}
