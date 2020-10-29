<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>电影购票系统</h1>
<form action="/film/add" method="post" id="form-sub">
    <table border="1">
        <input type="hidden" name="film_id" value="{{$_GET['film_id']}}">
        @csrf
        @foreach($film_count as $k=>$v)
            @if($k % 5 == 0)
                <tr></tr>
            @endif
            <td>
                {{$v['seat_num']}}
                @if(!in_array($v['seat_num'],$seat_num))
                    <input type="checkbox" name="film_count[]" value="{{$v['seat_num']}}">
                @else
                    <button type="button" style="background-color: red;height: 15px" value="{{$v['seat_num']}}">
                @endif
            </td>
        @endforeach
    </table>
    <input type="reset" value="重置">
    <input type="submit" id="btn-film" value="一键购票">
    <p>注:</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;红色为当前座位号已经被购买</p>
    @if($time > 0)
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;距离电影开场还有：<font id="time"></font></p>
    @else
    @endif
    <p id="over"></p>
</form>
</body>
</html>
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    $(document).ready(function () {
        $(document).on("submit","#form-sub",function () {
            var _true = window.confirm("确认购票吗")
            if(_true == true){
                if($("#over").html() == ''){
                    return true;
                }else{
                    alert('购票时间已结束');
                    return false;
                }
            }else{
                return false;
            }
        })
        const countDown = (second) => {
            const s = second % 60;
            const m = Math.floor(second / 60);
            return `${`00${m}`.slice(-2)} : ${`00${s}`.slice(-2)}`;
        };
        // let time = 6 * 60;
        let time = {{$time}};
        const timer = setInterval(() => {
            const show = countDown(time--);
            $("#time").html(show)
            // console.log(show);
            if(time < 0) {
                // console.log('倒计时结束！');
                $("#over").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;电影已开场或结束');
                clearInterval(timer);
            }
        }, 1000);
    })
</script>
