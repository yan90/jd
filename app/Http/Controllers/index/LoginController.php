<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
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
        echo $active_url;exit;
        //注册成跳转登录
            if($res){
            return redirect('index/login');
            }else{
                return redirect('index/register');
            }
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
      if(empty($res)){
        return redirect('/index/login')->with(['msg'=>'用户不存在']);
      }
        if($res['password']==md5($user_password)){
            session(['user_id'=>$res['user_id'],'user_name'=>$res['user_name'],'tel'=>$res['tel'],'email'=>$res['email']]);
            $last_login=$res['last_login']=time();  
            $Womodel=new UserModel();
            $logininfo = ['last_login'=>$last_login,'last_ip'=>$ip,'visit_count'=>$res['visit_count']+1];
            $Womodel->where('user_id',$res['user_id'])->update($logininfo);
        
            return redirect('/index/index');
        }else{
            return redirect('/index/login')->with(['msg'=>'账号或者密码错误']);
        }
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
