<!--instagram API 이용하여 코딩-->

<?php

    function processURL($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2
        ));
 
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function getInstaTag($title){
        
    // 공연 이름 받아와서 url 검색! (insta api 사용)
    $tag = urlencode($title);
    $client_id = "996ffe9e18ab4712b0a135fd1e5608e3";
    $url = 'https://api.instagram.com/v1/tags/search?q='.$tag.'&client_id='.$client_id; // instagram API 이용

    $all_result  = processURL($url);
    $arrtag = json_decode($all_result);
    print_r($arrtag);
    
    // 데이터에서 태그 갯수 뽑아오기
    if ($arrtag->data[0]){
        $tagCount = $arrtag->data[0]->media_count;
    } else {
        $tagCount = 0;
    }
    
    // 태그 갯수 반환하기
    return $tagCount;
    
    }
?>
