<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<?php
include 'find_time2.php';
include 'db.php';

//특정 url 내용 얻어오는 함수 
function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);

    $contents = curl_exec($ch);
    curl_close($ch);

    return $contents;
}

//db내용지우기
delete_db();

$url_array = array("http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42001&RegionName=%BC%AD%BF%EF", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42010&RegionName=%B0%E6%B1%E2", 
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42011&RegionName=%C0%CE%C3%B5", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42080&RegionName=%B0%AD%BF%F8", 
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42070&RegionName=%C3%E6%BA%CF",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42060&RegionName=%C3%E6%B3%B2", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42061&RegionName=%B4%EB%C0%FC",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42030&RegionName=%B0%E6%BA%CF", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42020&RegionName=%B0%E6%B3%B2",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42031&RegionName=%B4%EB%B1%B8", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42091&RegionName=%BF%EF%BB%EA",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42021&RegionName=%BA%CE%BB%EA", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42050&RegionName=%C0%FC%BA%CF",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42040&RegionName=%C0%FC%B3%B2", "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42041&RegionName=%B1%A4%C1%D6",
    "http://ticket.interpark.com/TiKi/Special/TPRegionReserve.asp?Region=42090&RegionName=%C1%A6%C1%D6");

foreach ($url_array as $url) {
    
    // url 받아오기
    $play_list = file_get_contents_curl($url);
    
    $play_list = mb_convert_encoding($play_list, 'utf-8', 'euc-kr');
    $index = strpos($play_list, 'top_line');
    $play_list = substr($play_list, $index);
    $index = strpos($play_list, 'btn_genre_exhibit');
    $play_list = substr($play_list, 0, $index);

    $index = strpos($play_list, 'http://ticket.interpark.com/TIKI/Main/TikiGoodsInfo.asp?GoodsCode=');
    $play_list = substr($play_list, $index);

    $play_array = explode("Line", $play_list);
    $index1 = 0;
    
    foreach ($play_array as $play) {

        $index1++;
        $index = strpos($play, 'http://ticket.interpark.com/TIKI/Main/TikiGoodsInfo.asp?GoodsCode=');

        $play = substr($play, $index);
        $index = strpos($play, 'img');
        $play = substr($play, 0, $index);
        $play = preg_replace("/[^0-9]/", "", $play);

        $url = "http://ticket.interpark.com/Ticket/Goods/GoodsInfo.asp?GoodsCode=" . $play;

        findTime2($url);
    }
}
?>
