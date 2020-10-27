<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>抽奖</h1>
<button id="btn-prize">开始抽奖</button>
</body>
</html>

<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    $(document).on("click","#btn-prize",function () {
        $.ajax({
            url:"/prize/add",
            type:"get",
            success:function(d){
                if(d.errno==400){
                    alert(d.msg)
                    window.location.href='/index/login';
                }else if(d.errno=30008){
                    alert(d.msg)
                }
                if(d.errno){
                        alert(d.data.prize)

                }
            }
        })
    })
</script>


