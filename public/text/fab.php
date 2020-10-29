<?php
$n=$_GET['n'];
//
function fab($n){
    if($n==1|| $n==2)
    {
        return 1;
    }
    return fab($n-2)+fab($n-1);
}
for ($i=1;$i<=40;$i++){
    echo fab($i);echo '<br>';
}
