<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use GuzzleHttp \Client;
use App\model\GitModel;
use Mail;
class LoginController extends Controller
{
    //注册视图
    public function register(){
        // echo 'a11';exit;
        return view('index/register');
    }
    //注册执行
    public function home(Request $request){
        // $data=$request->except("_token");
        // dd($data);
        $user_name=$request->input('user_name');
        $email=$request->input('email');

        $password=$request->input('password');
        $passwords=$request->input('passwords');
        //判断用户名不能为空
        if(empty($user_name)){
            return redirect('index/register')->with(['msg'=>'用户名不能为空']);
        }
        //判断两次密码一致
        if($password!=$passwords){
            return redirect('index/register')->with(['msg'=>'两次密码不一样']);
        }
        $tel=$request->input('tel');
        $data=
        [
            'user_name'=>$user_name,
            'password'=>md5($password),
            'tel'=>$tel,
            'email'=>$email,
        ];
        $UserModel=new UserModel();
        $data['reg_time'] = time();
        $res=UserModel::insertGetId($data);
        // $res=$UserModel->inserto($data);
        //生成激活码
        $active_code=Str::random(64);
        //保存激活码与用户的对应关系 使用有序集合
        $redis_active_key='ss:user:active';
        Redis::zAdd($redis_active_key,$res,$active_code);
        $active_url=env('APP_URL').'/user/active?code='.$active_code;
        $res=$this->email($data['email']);
        //注册成跳转登录
            if($res){
            return view('index/enroll');
            }else{
                return redirect('index/register');
            }
    }
    //邮箱
    public function email($email){
        $title = '邮箱激活';
        $key='';

        $content = "亲爱的用户：
        您好
        您于".date('Y-m-d H:i')."注册品优购,点击以下链接，即可激活该帐号：
        ".env('APP_URL')."/index/enroll".$key."
        (如果您无法点击此链接，请将它复制到浏览器地址栏后访问)
        1、为了保障您帐号的安全性，请在 半小时内完成激活，此链接将在您激活过一次后失效！
        2、请尽快完成激活，否则过期，即 ".date('Y-m-d H:i',time()+60*30)." 后品优购将有权收回该帐号。
        品优购";//内容
        $tag = Mail::raw($content, function ($message)use($title,$email){
            $message->subject($title);
            $message->to($email);
    });
    return redirect('index/enroll');
    }
    /**
     * 激活用户
     *
     *
     */
    public function active(Request $request){
        $active_code=$request->get('code');
        echo '激活码:'.$active_code; echo '</br>';
        $redis_active_key='ss:user:active';
        $uid=Redis::zScore($redis_active_key,$active_code);
        if($uid){
            echo "uid:".$uid;echo '</br>';
        //激活用户
        UserModel::where(['user_id'=>$uid])->update(['is_validated'=>1]);
        echo '激活成功';
        //删除集合中的激活码
        Redis::zRem($redis_active_key,$active_code);
        }else{
            echo '已失效';
        }

    }
    //注册成功后跳转的注册成功视图
    public  function enroll(){
        return view('index/enroll');
    }
    //登录视图
    public function login(){
        return view('index/login');
    }
    //执行登录
    public function logindo(Request $request){
        $user_name=$request->input('user_name');
        $user_password=$request->input('password');
        if(empty($user_name)){
            return redirect('index/login')->with(['msg'=>'用户不能为空']);
        }
        //最后登录的ip
        $ip=$_SERVER['REMOTE_ADDR'];
        $res=UserModel::where(['user_name'=>$user_name])
        ->orwhere(['tel'=>$user_name])
        ->orwhere(['email'=>$user_name])
        ->first();
//        dd($res);
      if(empty($res)){
        return redirect('/index/login')->with(['msg'=>'用户不存在']);
      }
        if($res['password']==md5($user_password)){
            session(['user_id'=>$res['user_id'],'user_name'=>$res['user_name'],'tel'=>$res['tel'],'email'=>$res['email']]);
            $last_login=$res['last_login']=time();
            $Womodel=new UserModel();
            $logininfo = ['last_login'=>$last_login,'last_ip'=>$ip,'visit_count'=>$res['visit_count']+1];
            $Womodel->where('user_id',$res['user_id'])->update($logininfo);

            return redirect('/');
        }else{
            return redirect('/index/login')->with(['msg'=>'账号或者密码错误']);
        }
    }
    //  GITHUB登录
    public function callback(Request $request){
      // 接收code
      $code = $_GET['code'];
      //换取access_token
      $token = $this->getAccessToken($code);
      //获取用户信息
      $git_user = $this->getGithubUserInfo($token);
      //判断用户是否已存在，不存在则入库新用户
      $u = GitModel::where(['guid'=>$git_user['id']])->first();
      if($u)          //存在
      {
          // TODO 登录逻辑
          $this->webLogin($u->uid);
      }else{          //不存在
          //在 用户主表中创建新用户  获取 uid
          $new_user = [
              'user_name' => Str::random(10)              //生成随机用户名，用户有一次修改机会
          ];
          $uid = UserModel::insertGetId($new_user);
          // 在 github 用户表中记录新用户
          $info = [
              'uid'                   => $uid,       //作为本站新用户
              'guid'                  => $git_user['id'],         //github用户id
              'avatar'                =>  $git_user['avatar_url'],
              'github_url'            =>  $git_user['html_url'],
              'github_username'       =>  Str::random(10),
              'github_email'          =>  $git_user['email'],
              'add_time'              =>  time()
          ];
          $guid = GitModel::insertGetId($info);        //插入新纪录
          // TODO 登录逻辑
          $this->webLogin($guid);
      }
      //将 token 返回给客户端
      return redirect('/index/index');       //登录成功 返回首页
    }
     /**
     * 根据code 换取 token
     */
    protected function getAccessToken($code)
    {
        $url = 'https://github.com/login/oauth/access_token';
        //post 接口  Guzzle or  curl
        $client = new Client();
        $response = $client->request('POST',$url,[
            'verify'    => false,
            'form_params'   => [
                'client_id'         => '53dc6bc017e4551194a8',
                'client_secret'     => 'f26819eac8dde6fe2fbde86261b49273d2debb0f',
                'code'              => $code
            ]
        ]);
        parse_str($response->getBody(),$str); // 返回字符串 access_token=59a8a45407f1c01126f98b5db256f078e54f6d18&scope=&token_type=bearer
        return $str['access_token'];
    }
     /**
     * 获取github个人信息
     * @param $token
     */
    protected function getGithubUserInfo($token)
    {
        $url = 'https://api.github.com/user';
        //GET 请求接口
        $client = new Client();
        $response = $client->request('GET',$url,[
            'verify'    => false,
            'headers'   => [
                'Authorization' => "token $token"
            ]
        ]);
        return json_decode($response->getBody(),true);
    }
    protected function webLogin($uid)
    {
        $res = GitModel::where('uid',$uid)->first();
        //将登录信息保存至session uid 与 token写入 seesion
        // print_r($res->toArray());exit;
        session(['uid'=>$res['uid'],'user_name'=>$res['github_username'],'user_tel'=>null]);
        /* echo session('uid');
        echo session('user_name');
        die; */
        return true;
    }
    //退出
    public function logout(Request $request){
        session(['user_id'=>null,'user_name'=>null,'tel'=>null]);
        $user_id=$request->session()->get('uid');
        if(empty($user_id)){
            return redirect('index/index');
        }
    }
}
