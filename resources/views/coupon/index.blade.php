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
<button id="btn-coupon-type1">满100-20</button>
<button id="btn-coupon-type2">满200-40</button>
</body>
</html>
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    $("#btn-coupon-type1").on('click',function () {
    $.ajax({
        url:"/coupon/get?type=1",
        type:"get",
        dataType:"json",
        success:function (d) {
            if(d.errno==400003){
                window.location.href='/login'
            }else
                if(d.errno==0){
                alert(d.msg)
            }
        }
    })
    })
</script>
