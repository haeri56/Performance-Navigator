<?php

include 'getInstaRecord.php';

function findTime2($url) {
    $day = array('월', '화', '수', '목', '금', '토', '일', '평일', '주말'); //날짜별로 시간정보를 가져오기위한 배열

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
    
    //기간안에 오늘날짜가 포함된 공연만 DB에 저
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

    
    //시작날짜가 오늘보다 뒤에있으면 DB에 저장하지 않
    if ((int) $play_date - (int) $today > 0) {
        echo "걸러라<br>";
        return 0;
    }
    //날짜 거르기
    
    //좌석수 구하기, 앞으로 추가될 기능
    $seat = $inter_data;
    $index = strpos($seat, 'var vPC');
    $seat = substr($seat, $index);

    $index = strpos($seat, 'var vBSYN');
    $seat = substr($seat, 0, $index);
    $seat = preg_replace("/[^0-9]/", "", $seat);
    //공연장정보 페이지로 이동하여 공연장의 좌석수 가져오기 
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

    $xy_array[1] = preg_replace("/([a-zA-Z]{1,10})/", "", $xy_array[1]);//영어지우기
    $xy_array[1] = str_replace("=", "", $xy_array[1]);
    $longitude = (double) $xy_array[1];
    $xy_array[2] = preg_replace("/([a-zA-Z]{1,10})/", "", $xy_array[2]);//영어지우기
    $xy_array[2] = str_replace("=", "", $xy_array[2]);
    $latitude = (double) $xy_array[2];
    echo $longitude . "<br>";
    echo $latitude . "<br>";
    //long , lat 완료
    
    //공연 소요시간 구하기 -> 보여질때 시작시간 + 소요시간으로 제공될것
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

    // 공연 시간 구하기 -> 시작시간
    $index = strpos($inter_array[1], '<p class="m_T5">');
    $inter_data = substr($inter_array[1], $index);
    $index = strpos($inter_data, "</p>");
    $inter_data = substr($inter_data, 0, $index);
    $inter_data = strip_tags($inter_data);

    //특수문자 & 필요없는 문자 지우
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
    $time2 = explode('년', $inter_data); //특정한날짜 지우기
    $inter_data = $time2[sizeof($time2) - 1];
    $time2 = explode('/', $inter_data);
    $inter_data = implode('/', $time2);
    $time2 = explode('시', $inter_data); //시로 나눠서 시간은 숫자만남게 만듬
    $inter_data = implode(' /', $time2);
    $time2 = explode('/', $inter_data);
    $time3 = array();
    $i = 0;

    foreach ($time2 as $value2) {
        # code…
        $value2 = preg_replace("/([0-9]{1,2})([가-힣]{1,3})/", "", $value2);
        $value2 = preg_replace("/([a-zA-Z]{1,10})/", "", $value2);
        //영어, 숫자와 한글이 합쳐진문자 지우기
        $len = mb_strlen($value2);

        if ($len > 0) {
            $time3[$i] = $value2;

            $i++;
        }
    }

    foreach ($day as $date) {
        //요일별로 시간찾기 
        $kk = 0;
        $flag = true;
        foreach ($time3 as $date2) { //시간정보가 배열에 나눠 저장된상태 ex) 월 830 화 330
            # code…
            if ($flag == false)
                break;
            if (!(strpos($date2, $date) === false)) {
                $temp = $kk;
                while ($temp < sizeof($time3)) {
                    if (preg_match("/[0-9]{1,2}/", $time3[$temp], $a)) { //숫자로만 되어있는 배열을 찾으면
                        $time3[$temp] = preg_replace("/[^0-9]/", "", $time3[$temp]); //해당요일의 시간에 넣음
                        if (strlen($time3[$temp]) >= 3) { //숫자가 3개이상이면 ex)630 ->6시30분
                            $var = substr($time3[$temp], 0, -2) . "시" . substr($time3[$temp], -2) . "분";
                            $time3[$temp] = $var;
                        } else {
                            $time3[$temp].="시";
                        }
                        if ($date == '평일') { //평일이란 문구를 찾으면 월화수목금 다 적용
                            $result2['월'] = $time3[$temp];
                            $result2['화'] = $time3[$temp];
                            $result2['수'] = $time3[$temp];
                            $result2['목'] = $time3[$temp];
                            $result2['금'] = $time3[$temp];
                        } else if ($date == '주말') {//주말이란 문구를 찾으면 토일만 적용
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
                //숫자 못찾았을경우 해당날짜 공연없음
                $result2[$date] = "공연없음";
            }
            $kk++;
        }
    }
    
    //소요시간을 숫자만 남게함 ex) 100분 -> 100
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
        //소요시간과 시작시간 더함 
        $result2[$key] = $hour . "시" . $min . "분";

    }

    print_r($result2);
    //db에 넣기 
    insert($title2, $img, $place_name, $result2, $play_runtime, $longitude, $latitude, $popularity);
    echo "<br><br>";
}

?>