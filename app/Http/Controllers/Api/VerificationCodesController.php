<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodesRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{

    public function store(VerificationCodesRequest $request, EasySms $easySms)
    {

        $captchaData = \Cache::get($request->captcha_key);

        if( !$captchaData )
        {
            return $this->response->error('图形验证码已失效',422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaData['phone'];
        //生成随机码
        if( !app()->environment('production')){

            $code = '1234';
        }else{
            $code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);

            try {
                $result = $easySms->send($phone, [
                    'content'  =>  "【Lbbs社区】您的验证码是{$code}。如非本人操作，请忽略本短信"
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                return $this->response->errorInternal($message ?? '短信发送异常');
            }
        }

        $key = 'verificationCode_'.str_random(15);
        $exporedAt = now()->addMinutes(10);

        \Cache::put($key , ['phone'=>$phone,'code'=> $code], $exporedAt);

        return $this->response->array(
            [
                'key'=>$key,
                'expired_at'=>$exporedAt->toDateTimeString()
            ]
        )->setStatusCode(201);

    }
}
