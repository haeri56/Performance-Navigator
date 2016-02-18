<script language="javascript" src="https://apis.skplanetx.com/tmap/js?version=1&format=javascript&appKey=fa5b993d-af82-30e3-b033-a7588d716d95"></script>
<script src="jquery-1.11.3.js"></script>
<script src="tMapApi.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<?php
$taxi_db = mysql_connect("localhost", "root", "root");
mysql_select_db("taxi_db", $taxi_db);

function delete_db() {
    $query = "delete from play";
    $result = mysql_query($query);
    return $result;
}

function select_domain() {
    $query = "select title,time from play";
    $result = mysql_query($query);
    return $result;
}

function select_title($sido) {
    $query = "select time from play where sido = '" . $sido . "'";
    $result = mysql_query($query);
    return $result;
}

function show_db() {
    $sql = "select * from play";
    $result = mysql_query($sql);
    $num1 = 0;
    while ($row = mysql_fetch_array($result)) {
        $num1++;
        echo $num1 . $row['title'] . $row['gpsX'] . $row['gpsY'] . $row['time'] . "<br>";
    }
}

function update_db($title, $result2, $play_runtime) {
    $sql = "update play set time ='" . $play_runtime . "', mon ='" . $result2['월'] . "'"
            . ", tue ='" . $result2['화'] . "', wed ='" . $result2['수'] . "', thu ='" . $result2['목'] . "'"
            . ", fri ='" . $result2['금'] . "', sat ='" . $result2['토'] . "', sun ='" . $result2['일'] . "'"
            . "where title='" . $title . "'";
    $result = mysql_query($sql);

    if ($result) {
        echo ": 성공<br>";
    } else {
        echo ": 실패<br>";
    }
}

function insert($title, $img, $place_name, $result2, $play_runtime, $longitude, $latitude, $popularity) {
    $sql = "insert into play(title,img,place,mon,tue,wed,thu,fri,sat,sun,time,gpsX,gpsY,popularity)";
    $sql.="value ('" . $title . "','" . $img . "','" . $place_name . "','" . $result2['월'] . "','" . $result2['화'] . "','"
            . $result2['수'] . "','" . $result2['목'] . "','" . $result2['금'] . "','" . $result2['토'] . "','" . $result2['일'] . "','"
            . $play_runtime . "','" . $longitude . "','" . $latitude . "','" . $popularity . "')";
    $result = mysql_query($sql);

    if ($result) {
        echo ": 성공<br>";
    } else {
        echo ": 실패<br>";
    }
}

function is_passwd_correct($id, $passwd) {
    $query = "select groups from member where id='" . $id . "' and passwd='" . $passwd . "'";
    $rows = mysql_query($query);

    if (mysql_num_rows($rows)) {
        //select 는 검색결과가 없다고 fail, false를 반환하지 않음. 단지 반환하는 리소스값이 0일뿐임.
        // 따라서 if문에서 쿼리가 true다, false다 라고 활용할 수 없음.
        //그래서 mysql_num_rows를 이용해서 갯수가 있는지 없는지 보는 것.
        $row = mysql_fetch_row($rows);
        if ($row[0] == 'admin') {
            header("Location:testtest2.php");
        } else {
            header("Location:index.php");
        }
    } else {
        header("Location:login.html");
    }
}

function search($title) {
    $query = "select title from play where title='" . $title . "'";
    $result = mysql_query($query);
    if (mysql_num_rows($result))
        return 1;
    else
        return 0;
}

function ensure_logged_in() {
    if (!$_SESSION["id"]) {
        header("Location:login.html");
    }
}
?>