<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\FilmModel;
use App\model\SeatModel;
class MovieController extends Controller
{

    // 电影购票
    public function film(Request $request){
//       echo strtotime(date('Y-m-d H:i:s', strtotime('+300second')));die;
        $film_id = $request->film_id;
        $filInfo = FilmModel::where('film_id',$film_id)->first();
        if(is_object($filInfo)){
            $filInfo = $filInfo->toArray();
        }
//         结束时间秒数
        $time = $filInfo['film_time']-time();
//        dd($time);
//        dd($filInfo);
        // 剩余库存
        $film_count = $filInfo['film_count'];
        $str = [];
        for($i = 1;$i<=$film_count;++$i){
            $str[] = [
                'seat_num'  =>$i,
            ];
        }
        //        dd($str);
        // 根据电影 id 查询当前电影已经购买当前座位号
        $seatInfo = SeatModel::where('film_id',$film_id)->get();
        if(is_object($seatInfo)){
            $seatInfo = $seatInfo->toArray();
        }
        $seat_num = [];
        foreach ($seatInfo as $k=>$v){
            $seat_num[] = $v['seat_num'];
        }

        return view('movie.movie',['film_count'=>$str,'seat_num'=>$seat_num,'time'=>$time]);
    }
    // 开始购票
    public function filmadd(Request $request){
        $user_id = session('user_id');
        if(empty($user_id)){
            echo "<script>alert('请先登录');window.location.href='/index/login'</script>";
        }
        $data = $request->except('_token');
//        dd($data);
        $film_id = $data['film_id'];
        if(empty($data['film_count'])){
            echo "<script>alert('请选择电影座');window.location.href='/film?film_id='+$film_id</script>";
        }
        $film_count = $data['film_count'];
//        dd($film_count);
        // 根据电影 id 查询当前电影已经购买当前座位号
        $seatInfo = SeatModel::where('film_id',$film_id)->get();
        if(is_object($seatInfo)){
            $seatInfo = $seatInfo->toArray();
        }
        $seat_num = [];
        foreach ($seatInfo as $k=>$v){
            $seat_num[] = $v['seat_num'];
        }
        // 入库
        $data = [];
        foreach ($film_count as $k=>$v){
            if(in_array($v,$seat_num)){
                echo "<script>alert($v+'此座位号已被购买请重新选择');window.location.href='/film?film_id='+$film_id</script>";
            }else{
                $data[] = [
                    'film_id'  =>$film_id,
                    'seat_num' =>$v,
                    'add_time' =>time(),
                    'user_id'  =>$user_id,
                ];
            }
        }
//        dd($data);
        $res = SeatModel::insert($data);
        if($res){
            echo "<script>alert('购票成功,前台查询后，付款拿票');window.location.href='/film?film_id='+$film_id</script>";
        }else{
            echo "<script>alert('购票失败');window.location.href='/test/film?film_id='+$film_id</script>";
        }
    }

}
