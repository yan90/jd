<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Log;
use GuzzleHttp\Client;
use App\Model\WeachModel;
use App\Model\MediaModel;
class TextController extends Controller
{
        //接入微信
    public function checkSignature(Request $request)
    {
        $echostr=$request->echostr;
//        echo $echostr;exit;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = env('WX_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            //调用关注回复
            $this->sub();
            echo "";
        }else{
            echo '';
        }
    }
    //关注回复   用户入库
    public function sub(){
        //获取微信post数据 xml(格式)
        $postStr = file_get_contents("php://input");
//        Log::info("====".$postStr);
        $postArray=simplexml_load_string($postStr);
//        Log::info('=============='.$postArray);
        $toUser= $postArray->FromUserName;//openid
        //evnet  判断是不是推送事件
        if($postArray->MsgType=="event") {
            if ($postArray->Event == "subscribe") {
                $WeachModelInfo = WeachModel::where('openid', $toUser)->first();
                if (is_object($WeachModelInfo)) {
                    $WeachModelInfo = $WeachModelInfo->toArray();
                }
                if (!empty($WeachModelInfo)) {
                    $content = "欢迎回来";
                } else {
                    $content = "你好，欢迎关注";
                    $token = $this->token();
                    $data = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $token . "&openid=" . $toUser . "&lang=zh_CN";
                    file_put_contents('user_wetch', $data, FILE_APPEND);//存文件
                    $wetch = file_get_contents($data);
                    $json = json_decode($wetch, true);
//        file_put_contents('user_wetch',$data,'FILE_APPEND');//存文件
                    $data = [
                        'openid' => $toUser,
                        'nickname' => $json['nickname'],
                        'sex' => $json['sex'],
                        'city' => $json['city'],
                        'country' => $json['country'],
                        'province' => $json['province'],
                        'language' => $json['language'],
                        'subscribe_time' => $json['subscribe_time'],
                    ];
                    $weachInfo = WeachModel::insert($data);
                }
//                Log::info('111=============='.$postArray);
                //调用自定义菜单
                echo  $this->custom();
                $this->text($postArray, $content);
            }
            //点击二级 获取天气
            if ($postArray->Event == 'CLICK') {
                if ($postArray->EventKey == 'weather') {
                    //调用天气
                    $content = $this->getweather();
                    $this->text($postArray, $content);
                }
            }
            if ($postArray->Event == 'CLICK') {
                if ($postArray->EventKey == 'checkin') {
                    $key = 'USER_SIGN_' . date('Y_m_d', time());
                    $content = '签到成功';
                    $user_sign_info = Redis::zrange($key, 0, -1);
                   if(in_array((string)$toUser,$user_sign_info)){
                       $content='已经签到，不可重复签到';
                   }else{
                       Redis::zadd($key,time(),(string)$toUser);
                   }
                   $result= $this->text($postArray, $content);
                   return $result;
                    }
            }
            //每日推荐
//            if($postArray->Event=='CLICK'){
//                if($postArray->EventKey=='daily'){
//
//                }
//            }

        }elseif ($postArray->MsgType=="text"){
            $msg=$postArray->Content;
            switch ($msg){
                case '你好':
                    $content='亲   你好';
                    $this->text($postArray,$content);
                    break;
                case '天气':
                    $content=$this->getweather();
                    $this->text($postArray,$content);
                    break;
               case '时间';
                    $content=date  ('Y-m-d H:i:s',time());
                    $this->text($postArray,$content);
                    break;
                default;
                    $content='啊啊啊啊 亲  你在说什么呢 ';
                    $this->text($postArray,$content);
                break;
            }
//            echo __LINE__;exit;
//
        }
        //判断入库
        if(!empty($postArray)){
            $toUser=$postArray->FromUserName;
            $fromUser=$postArray->ToUserName;
            //将聊天入库
            $msg_type=$postArray->MsgType;//推送事件的消息类型
            switch ($msg_type){
                //视频入库
                case 'video':
                    $this->videohandler($postArray);
                    break;
                    //音频
                    case 'voice';
                    $this->voicehandler($postArray);
                    break;
                    //文本
                case 'text';
                    $this->texthandler($postArray);
                    break;
                    //图片
                case 'image';
                    $this->picture($postArray);
                    break;
            }
        }
        //微信材料库
    }
    //关注回复
    public function text($postArray,$content){
//        Log::info('222=============='.$postArray);
//        Log::info('222=============='.$content);
        $toUser= $postArray->FromUserName;//openid
//
                Log::info('222=============='.$toUser);
        $fromUser = $postArray->ToUserName;
        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
        $info = sprintf($template, $toUser, $fromUser, time(), 'text', $content);
        echo  $info;
    }
    //获取天气预报
    public function getweather(){
        $url='http://api.k780.com:88/?app=weather.future&weaid=beijing&&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json';
        $weather=file_get_contents($url);
        $weather=json_decode($weather,true);
//        dd($weather);
        if($weather['success']){
            $content = '';
            foreach($weather['result'] as $v){
                $content .= '日期：'.$v['days'].$v['week'].' 当日温度：'.$v['temperature'].' 天气：'.$v['weather'].' 风向：'.$v['wind'];
            }
        }
        Log::info('===='.$content);
        return $content;

    }
    //获取token
    public  function token(){
        $key='wx:access_token';
        //检查是否有token
        $token=Redis::get($key);
        if($token){
//            echo "有缓存";'</br>';
//            echo $token;
        }else{
//            echo"无缓存";'</br>';
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSEC')."";
//
            $response=file_get_contents($url);

            $data=json_decode($response,true);

            //使用guzzle发起get请求
            $client=new Client();
            //['verify'=>false]   不加这个会报ssl错误  因为默认是true
            $response=$client->request ('GET',$url,['verify'=>false]);  //发起请求并接受响应
            $json_str=$response->getBody();    //服务器的响应数据
//            echo $json_str;
            $data=json_decode($json_str,true);

            $token=$data['access_token'];
            //缓存到redis中  时间为3600
            Redis::set($key,$token);
            Redis::expire($key,3600);
        }

        return $token;
    }
    //GET测试
    public function guzzle(){
//        echo __METHOD__;
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSEC')."";
        //使用guzzle发起get请求
        $client=new Client();
        //['verify'=>false]   不加这个会报ssl错误  因为默认是true
        $response=$client->request ('GET',$url,['verify'=>false]);  //发起请求并接受响应
        $json_str=$response->getBody();    //服务器的响应数据
    echo $json_str;
    }
    //POST 上传素材
    public function guzzle2(){
        $access_token=$this->token();
        $type='image';
        $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
//
        $client=new Client();
        //['verify'=>false]   不加这个会报ssl错误  因为默认是true
        $response=$client->request ('POST',$url,[
            'verify'=>false,
            'multipart'=>[
                [
                    'name'=>'media',
                    'contents'=>fopen('timg.jpg','r'),
                ], //上传的文件路径
            ]
        ]);  //发起请求并接受响应
        $data=$response->getBody();
        return $data;

    }
    //自定义菜单lpoloplop
    public function custom(){
        $access_token=$this->token();
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
//        echo $url;
        //
        $array=[


            'button'=>[
                [
              'type'=>'view',
              'name'=>'商城',
              'url'=>'http://2004yjl.comcto.com/'.'/web_auth',

            ],

            [
                'name'=>'菜单',
                "sub_button"=>[
                    [
                        'type'  => 'click',
                        'name'  => '传图',
                        'key'   => 'uploadimg'
                    ],
                    [
                        'type'  => 'click',
                        'name'  => '天气',
                        'key'   => 'weather'
                    ],
                    [
                        'type'  => 'click',
                        'name'  => '签到',
                        'key'   => 'checkin'
                    ],
                    [
                    'type'=>'click',
                    'name'=>'每日推荐',
                    'key'=>'daily',
                ],

                ]
            ]

                ]
        ];
//        $array->toArray();
//

        $client=new Client();
        $response=$client->request('POST',$url,[
            'verify'=>false,
            'body'=>json_encode($array,JSON_UNESCAPED_UNICODE)
        ]);
        $data=$response->getBody();
        echo $data;
    }
    //视频入库
    protected function videohandler($postArray){
        $data=[
            'add_time'=>$postArray->CreateTime,
            'media_type'=>$postArray->MsgType,
            'media_id'=>$postArray->MediaId,
            'msg_id'=>$postArray->MsgId,
        ];
        MediaModel::insert($data);
    }
    //音频
    protected function voicehandler($postArray){
        $data=[
            'add_time'=>$postArray->CreateTime,
            'media_type'=>$postArray->MsgType,
            'media_id'=>$postArray->MediaId,
            'msg_id'=>$postArray->MsgId,
        ];
        MediaModel::insert($data);
    }
    //文本
    protected function texthandler($postArray){
        $data=[
            'add_time'=>$postArray->CreateTime,
            'media_type'=>$postArray->MsgType,
            'openid'=>$postArray->FromUserName,
            'msg_id'=>$postArray->MsgId,
        ];
        MediaModel::insert($data);
    }
    //图片
    protected function picture ($postArray){
        $data=[
            'media_url'=>$postArray->PicUrl,//图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
            'media_type'=>'image',//类型为图片
            'add_time'=>time(),
            'openid'=>$postArray->FromUserName,
        ];
        MediaModel::insert($data);
    }
    //微信网页授权
    public function wxWebAuth(){
        $redirect='http://2004yjl.comcto.com/'.'/web_redirect';
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APPID')."&redirect_uri=".$redirect."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
//        dd($url);
        return redirect($url);
    }
    //微信授权页面重定向
    public function wxWebRedirect(){
    $code=$_GET['code'];
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APPID')."&secret=".env('WX_APPSEC')."&code=".$code."&grant_type=authorization_code";
//        echo $url;
        $xml=file_get_contents($url);
        $xml_code=json_decode($xml,true);
        if(isset($xml_code['errcode'])){
            if($xml_code['errcode']==40163){
                return"验证码已经失效";
            }
        }
        $access_token=$xml_code['access_token'];
        $openid=$xml_code['openid'];
        //拉取用户的信息
        $api="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $user=file_get_contents($api);
        $user_info=json_decode($user,true);
//        dd($user_info);
        if($user_info){
            return redirect('/');
        }
    }
    //创建标签
    public function set_label(Request $request){
    $label_name=$request->label_name;
    //获取token
        $access_token=$this->token();
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$access_token;
//        dd($url);
        $arr_xml=[
          'tag'=>[
              'name'=>$label_name,
          ],
        ];
        $xml_arr=json_encode($arr_xml,256);
//       dd($xml_arr);
        $client=new Client();
        //post请求(微信创建标签)
        $response=$client->request('POST',$url,[
            'verify'=>false,
            'body'=>$xml_arr
            ]);
//        print_r($response) ;exit;
        $callback=json_decode($response->getBody()->getContents());
//        print_r($callback) ;exit;
        if(isset($callback->tag)){
            if(is_object($callback->tag)) {
                return "添加菜单成功";
            }
            }else{
                if($callback->errcode == 45157){
                    return "标签名非法或和其他标签重名";
                }else if($callback->errcode == 45158){
                    return "标签名长度超过30个字节";
                }else{
                    return "创建的标签数过多，请注意不能超过100个";
                }
            }

    }
    //图文
    public function uploads($toUser,$fromUser,$title,$description,$content,$url){
        $template = "<xml>
                              <ToUserName><![CDATA[%s]]></ToUserName>
                              <FromUserName><![CDATA[%s]]></FromUserName>
                              <CreateTime>%s</CreateTime>
                              <MsgType><![CDATA[%s]]></MsgType>
                              <ArticleCount><![CDATA[%s]]></ArticleCount>
                              <Articles>
                                <item>
                                  <Title><![CDATA[%s]]></Title>
                                  <Description><![CDATA[%s]]></Description>
                                  <PicUrl><![CDATA[%s]]></PicUrl>
                                  <Url><![CDATA[%s]]></Url>
                                </item>
                              </Articles>
                            </xml>";
        $info = sprintf($template);
//        return $info;
        echo $info;
    }
}

