<?php

include 'getInstaRecord.php';

function findTime2($url) {
    $day = array('월', '화', '수', '목', '금', '토', '일', '평일', '주말');

    $inter_data = file_get_contents_curl($url);
    $inter_data = mb_convert_encoding($inter_data, 'utf-8', 'euc-kr');

    // 이름 구하기
    $title = $inter_data;
    $index = strpos($title, '<h1>');
    $title = substr($title, $index);
    $index = strpos($title, '</h1>');
    $title = substr($title, 0, $index);
    $title = strip_tags($title);
    $title = str_replace("“", "", $title);
    $title = str_replace("”", "", $title);
    $title = str_replace("‘", "", $title);
    $title = str_replace("’", "", $title);
    $title = str_replace(",", "", $title);
    $title = str_replace("\n", "", $title);
    $title = str_replace("\r", "", $title);
    $title = str_replace("\t", "", $title);
    $title = $title . trim();
    echo $title . "<br>";

    $title2 = $inter_data;
    $index = strpos($title2, '「');
    $title2 = substr($title2, $index);
    $index = strpos($title2, '」더 궁금하다면? ');
    $title2 = substr($title2, 0, $index);
    $title2 = str_replace("「", "", $title2);
    $title2 = str_replace("-", "", $title2);
    echo $title2 . "<br>";
    //이름 완료
    
    // 인스타api 이용해 인기도 구하기
    $popularity = getInstaTag($title2);
    echo "<br>" . $popularity . "<br>";
    // 인기도 완료
    
    // 이미지 구하기
    $img = $inter_data;
    $index = strpos($img, 'TabA_Poster');
    $img = substr($img, $index);
    $index = strpos($img, 'divMediaInfo');
    $img = substr($img, 0, $index);
    $index = strpos($img, 'http://');
    $img = substr($img, $index);
    $index = strpos($img, '" alt');
    $img = substr($img, 0, $index);
    echo $img . "<br>";
    //이미지 완료
    
    //장소이름 구하기
    $place_name = $inter_data;
    $index = strpos($place_name, '장소');
    $place_name = substr($place_name, $index);
    $index = strpos($place_name, '기간');
    $place_name = substr($place_name, 0, $index);
    $index = strpos($place_name, '</h4>');
    $place_name = substr($place_name, $index);
    $place_name = strip_tags($place_name);
    $place_name = str_replace("\n", "", $place_name);
    $place_name = str_replace("\r", "", $place_name);
    $place_name = str_replace("\t", "", $place_name);
    echo $place_name . "<br>";
    //장소이름 완료
    
    //날짜 구하기
    $play_date = $inter_data;
    $index = strpos($play_date, '기간');
    $play_date = substr($play_date, $index);
    $index = strpos($play_date, '201');
    $play_date = substr($play_date, $index);
    $index = strpos($play_date, 'btn_moretime');
    $play_date = substr($play_date, 0, $index);
    $index = strpos($play_date, '~');
    if (!($index == 0)) {
        $play_date = substr($play_date, 0, $index);
    } else {

        $index = strpos($play_date, 'java');
        $play_date = substr($play_date, 0, $index);
    }
    $play_date = preg_replace("/[^0-9]/", "", $play_date);
    $play_date = trim($play_date);
    echo "play_date : " . $play_date . "<br>";
    $today = date("Ymd");

    echo $today . " ";
    echo $play_date . "<br>";

    

    if ((int) $play_date - (int) $today > 0) {
        echo "걸러라<br>";
        return 0;
    }
    //날짜 거르기
    
    //좌석수 구하기 
    $seat = $inter_data;
    $index = strpos($seat, 'var vPC');
    $seat = substr($seat, $index);

    $index = strpos($seat, 'var vBSYN');
    $seat = substr($seat, 0, $index);
    $seat = preg_replace("/[^0-9]/", "", $seat);

    $url2 = "http://ticket.interpark.com/Ticket/Goods/ifrGoodsPlace.asp?PlaceCode=" . $seat;
    $place = file_get_contents_curl($url2);

    $place = mb_convert_encoding($place, 'utf-8', 'euc-kr');
    //좌석수 완료

    // 위도, 경도 구하기
    $index = strpos($place, '&Longitude');
    if (!$index)
        return 0;
    $xy = substr($place, $index);
    $index = strpos($xy, '&Width');
    $xy = substr($xy, 0, $index);
    $xy_array = explode('&', $xy);

    $xy_array[1] = preg_replace("/([a-zA-Z]{1,10})/", "", $xy_array[1]);
    $xy_array[1] = str_replace("=", "", $xy_array[1]);
    $longitude = (double) $xy_array[1];
    $xy_array[2] = preg_replace("/([a-zA-Z]{1,10})/", "", $xy_array[2]);
    $xy_array[2] = str_replace("=", "", $xy_array[2]);
    $latitude = (double) $xy_array[2];
    echo $longitude . "<br>";
    echo $latitude . "<br>";
    //long , lat 완료
    
    //공연 관람 시간 구하기
    $index = strpos($inter_data, '<dd class="etc">');
    $inter_data = substr($inter_data, $index);
    $inter_array = explode('<div class="dt_tSocial">', $inter_data);

    $runtime_array = explode('|', $inter_array[0]);

    $play_runtime = $runtime_array[1];
    $play_runtime = trim($play_runtime);

    if (strlen($play_runtime) >= 10000)
        $play_runtime = "";

    if (strpos($play_runtime, "분") === FALSE) {
        $play_runtime = 60;
    } else {
        $runtime_array = explode('분', $play_runtime);
        $play_runtime = $runtime_array[0];
    }
    echo $play_runtime . "<br>";

    // 공연 시간 구하기
    $index = strpos($inter_array[1], '<p class="m_T5">');
    $inter_data = substr($inter_array[1], $index);
    $index = strpos($inter_data, "</p>");
    $inter_data = substr($inter_data, 0, $index);
    $inter_data = strip_tags($inter_data);

    if ($index = strpos($inter_data, "http")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, '*')) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "#")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "▶")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "♥")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "♡")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "http")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "★")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "☞")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "싸니")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "*")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "＇")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "※")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "습니다")) {
        $inter_data = substr($inter_data, 0, $index);
    } if ($index = strpos($inter_data, "4회")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "［9월")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "추석연휴")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "휴무")) {
        $inter_data = substr($inter_data, 0, $index);
    }if ($index = strpos($inter_data, "공연없음")) {
        $inter_data = substr($inter_data, 0, $index);
    }
    if ($index = strpos($inter_data, "단,")) {
        $inter_data = substr($inter_data, 0, $index);
    }if ($index = strpos($inter_data, "5/4")) {
        $inter_data = substr($inter_data, 0, $index);
    }if ($index = strpos($inter_data, "Open")) {
        $inter_data = substr($inter_data, 0, $index);
    }if ($index = strpos($inter_data, "［월요일")) {
        $inter_data = substr($inter_data, 0, $index);
    }

    $inter_data = str_replace("'", "", $inter_data);
    $inter_data = trim($inter_data);
    if (strlen($inter_data) == 0)
        $inter_data = "평일 6시";

    $inter_data = str_replace("-", "", $inter_data);
    $inter_data = str_replace("&", "", $inter_data);
    $inter_data = str_replace(";", "", $inter_data);
    $inter_data = preg_replace("/(공휴일)/", "", $inter_data);
    $inter_data = preg_replace("/(오후)/", "", $inter_data);
    $inter_data = preg_replace("/([0-9]{1,2})+[.]+([0-9]{1,2})/", "", $inter_data);
    $inter_data = str_replace(",", "", $inter_data);
    $inter_data = str_replace("00", "", $inter_data);

    # code…
    $time2 = explode('(', $inter_data);
    $inter_data = implode(' /', $time2);
    $time2 = explode(')', $inter_data);
    $inter_data = implode(' /', $time2);
    $time2 = explode('~', $inter_data);
    $inter_data = implode(' /', $time2);
    $time2 = explode(' ', $inter_data);
    $inter_data = implode('/', $time2);
    $time2 = explode('년', $inter_data);
    $inter_data = $time2[sizeof($time2) - 1];
    $time2 = explode('/', $inter_data);
    $inter_data = implode('/', $time2);
    $time2 = explode('시', $inter_data);
    $inter_data = implode(' /', $time2);
    $time2 = explode('/', $inter_data);
    $time3 = array();
    $i = 0;

    foreach ($time2 as $value2) {
        # code…
        $value2 = preg_replace("/([0-9]{1,2})([가-힣]{1,3})/", "", $value2);
        $value2 = preg_replace("/([a-zA-Z]{1,10})/", "", $value2);

        $len = mb_strlen($value2);

        if ($len > 0) {
            $time3[$i] = $value2;

            $i++;
        }
    }

    foreach ($day as $date) {
        $kk = 0;
        $flag = true;
        foreach ($time3 as $date2) {
            # code…
            if ($flag == false)
                break;
            if (!(strpos($date2, $date) === false)) {
                $temp = $kk;
                while ($temp < sizeof($time3)) {
                    if (preg_match("/[0-9]{1,2}/", $time3[$temp], $a)) {
                        $time3[$temp] = preg_replace("/[^0-9]/", "", $time3[$temp]);
                        if (strlen($time3[$temp]) >= 3) {
                            $var = substr($time3[$temp], 0, -2) . "시" . substr($time3[$temp], -2) . "분";
                            $time3[$temp] = $var;
                        } else {
                            $time3[$temp].="시";
                        }
                        if ($date == '평일') {
                            $result2['월'] = $time3[$temp];
                            $result2['화'] = $time3[$temp];
                            $result2['수'] = $time3[$temp];
                            $result2['목'] = $time3[$temp];
                            $result2['금'] = $time3[$temp];
                        } else if ($date == '주말') {
                            $result2['토'] = $time3[$temp];
                            $result2['일'] = $time3[$temp];
                        } else {
                            $result2[$date] = $time3[$temp];
                        }
                        $flag = false;
                        break;
                    }
                    $temp++;
                }
            } else {
                $result2[$date] = "공연없음";
            }
            $kk++;
        }
    }
    
    $play_runtime = preg_replace("/[^0-9]/", "", $play_runtime);

    foreach ($result2 as $key => $result3) {

        if ($result3 == "공연없음")
            continue;

        $hArr = explode('시', $result3); // hArr[0] : 시
        if (isset($hArr[1]))
            $mArr = explode('분', $hArr[1]);  // mArr[0] : 분
        else
            $mArr[0] = 0;
        $tempTime = (int) ($mArr[0]) + (int) ($play_runtime);
        $min = $tempTime % 60;
        $hour = (int) ($hArr[0] + $tempTime / 60);
        
        $result2[$key] = $hour . "시" . $min . "분";

    }

    print_r($result2);
    
    insert($title2, $img, $place_name, $result2, $play_runtime, $longitude, $latitude, $popularity);
    echo "<br><br>";
}

?>