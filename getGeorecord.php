<?php

// db 정보 
$db_host = "localhost";
$db_id = "root";
$db_password = "root";
$db_dbname = "taxi_db";

// db 연결
$db_conn = mysql_connect($db_host, $db_id, $db_password) or die("Fail to connect database!");
mysql_select_db($db_dbname, $db_conn);

// 인기도로 내림차순하여 select 쿼리 날리기
$query = "select img,title,place,gpsX,gpsY,time,mon,tue,wed,thu,fri,sat,sun,popularity from play order by popularity desc";
$result = mysql_query($query);

if(!$result){
    $message = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query ;
    die($message);
}

// 데이터 베이스 결과 셋으로부터 json 만들기 
$resultArray = array();
while( $row = mysql_fetch_assoc($result)){
    $arrayMiddle = array (
        "img" => urlencode($row['img']), 
        "title" => urlencode($row['title']),
        "place" => urlencode($row['place']), 
        "gpsX" => (double) urlencode($row['gpsX']), 
        "gpsY" => (double) urlencode($row['gpsY']),
        "time" => urlencode($row['time']),
        "mon" => urlencode($row['mon']),
        "tue" => urlencode($row['tue']),
        "wed" => urlencode($row['wed']),
        "thu" => urlencode($row['thu']),
        "fri" => urlencode($row['fri']),
        "sat" => urlencode($row['sat']),
        "sun" => urlencode($row['sun']),
        "popularity" => (int) urlencode($row['popularity'])
    );
    array_push ($resultArray, $arrayMiddle);
}

// 결과 배열 출력
print_r (urldecode(json_encode(array("perform_data"=>$resultArray))));

// close db
mysql_close($db_conn);


?>