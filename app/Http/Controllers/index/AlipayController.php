<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
class AlipayController extends Controller
{
    protected $config = [
        'app_id' =>'2021000116699144',//你创建应用的APPID
        'notify_url' => 'http://jd.2004.com/alipay/AliPayReturn',//异步回调地址
        'return_url' => 'http://jd.2004.com/alipay/AliPayNotify',//同步回调地址
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApDxtbMPfn8idqOOQbg0SgKmvoeUSXgZOt/hIssNrjfL7bSTUfW9Dt4emRgANR/Bhi7YG0yZJQ0pqvEvW1fyt5DPl5SdRuN5mOwB8l1z5XGHuLkAk3UkbYcyfWNQW8wOqDo1yy2MxtbaZwKZlYyF0GO1Y3nB+ui92tutNA1EeW82MCmGoV45dL1W++R2V2959Zvfl6jf9KNRm2YVXxwn93q0K03l04PB9ziQSs5+EcgbIh4BEpxRhdhGdHSlluQHzripg3s3sVuyIlHvlHA9vJxhoKasuIxISWF8L+O56SzNKbX4p4IX2clGSqR3GNSDHz4HFxKzhOA0CHq7yDtwhDQIDAQAB',//是支付宝公钥，不是应用公钥,  公钥要写成一行,不要换行
        // 加密方式： **RSA2**
        'private_key' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCvfyyOgpbAy2/c1GxDJTLkaq3sd2RCZyaZ/f62Le2iHs2zEdrB7bjsrgAkQ4/RX/wH0I+11AX0iDV06UumjXZ3gOnsKWIXo2PSiNvyoMmI9GqJRZ7DonRHymGwQ0K+AcscbnOX27uh2kNNV1kodFC4Hh7wi+Gky4AQEbt6Opjs+DB+ChNJr2ybzG7Ym2eXtFZEJng4sUYF/fo5fVRAcPHmYVQoGosniSF02kB58XUu9mhyDi6uWCWYAIb6fRTN9LGY+HW5dv1ehkDpgxNofgygpBa5UEFzYPfLJov8j8cicCEyG3EleOxe4+A3EzWgHqmX9lLGVmActe0NRjPVsLBBAgMBAAECggEAcicxz1zoPG3XzHesGBzpNqShjw+1+m9oL4CEnvHPAcYxnMn/VmeQAvvHgc8kjFd384lZATfxy7aRtwNNPwADUAZdokzkzmVsN/TnxLGLhfceGT5c3/oa0tu0oVeO4VL/T/YUYHIAYW48muE3UFYgbzLAg00pr+zi1xEBtqbI9FIENRfovvCtFBn/q8CVWc8QOz6Y4gO1lwIo9dBBKMEzCUgm7bWgsW/MO0/aJN1XbwLqn8U/Pugimw5nISUNplLyuWAZa18mwt2cxQHQkP/tPP8Z5nBxaav/H0owO9srrqJDVgZ8y7DLjQ7EUXin+LkfkX+/N9EHtlZLpVMFJX/IwQKBgQDaGLPpSkc7wLPiyythLX9MTNpFJdKoFTBxVKeukripG5N4au3ouJoTA2QrtxggfjkD76wOdYweg3qYuPlMHL5sXC0uqSlYPNDAt/yWocUvoG2zFrLpNOjOja/nXBfnDrE5NgyvrGqarQ74mYPcGfx81Vnhvj5ScfijASuIFJgSmQKBgQDN/yx21h+/r/D6wXaHNtdi0Y+12PT4l6TG5haf7OrhWRYsRVjkIw9Utei/5Vo9JDg7tg9IWrf5Dn/ywzprHYgzU+XDH9dyqbuatYrZjaOGZ6PFN9abLCOEgcckGFf6WHJbVrVvGNp8ZKrp1gQVh5ebVQMwHGV6ABlFxhcjEIK76QKBgDHFaWlX1iHAvEyJaQDoTSCweS9GjmhlTYTPeOTR/uo4rNLSNDDjz+V+5KFFS3A+3ewUCgPSt4NPJe8sZ1gDR0GbV4RKfnDwkAMq+a22hTV1OxOfnnyx4l8g3n/B+IJ2S+Nufj6o7jsWO1BoWDsmgwRJ/BUUQUy3TbKFVdXcPUghAoGAfCG8ZY/6acRX7oMOLcS6Xe3yL56hX/vha4nTMGPP1iOc3Oic/Dy0TFOiAaDvk9Bzome/Jdak7gvyxhMm7M1K+cMgvUg+x/XH+x5SoW5cj+18HqfbRn5+mKarnfCdc1pA7xF9G4laf4MaCvbQVzjx/sRnu2IhNDdDA2yvHI4ieaECgYAMWtAN2A0RAlWwAitJ3FIWkze+d8QNGCGUPLoWUtQzmRdDL+9drqhRcs/qvDEdWjy6ERwJ+TTKe/U0T3yzLbqEetzeLj9pwH7uTdNw3nTzai+rTBZGQtbPmbkkQMUMPGeAYuGAPh+QPZ75MwuQDu0qNxulLep2b9VVQCTs98CCzA==',//密钥,密钥要写成一行,不要换行
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];
    public function Alipay($order)
    {
//        dd($order);
        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay;// laravel 框架中请直接 `return $alipay`
    }

    public function AliPayReturn()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
        return Pay::alipay($this->config)->success();// laravel 框架中请直接 `return $alipay->success()`
    }

    public function AliPayNotify(Request $request)
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
        $money = $request->total_amount;
        return view('order.paysuccess',['money'=>$money]);
    }
}
