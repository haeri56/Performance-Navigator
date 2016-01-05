<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href='http://fonts.googleapis.com/earlyaccess/hanna.css' rel='stylesheet' type='text/css'>
        <script language="javascript" src="https://apis.skplanetx.com/tmap/js?version=1&format=javascript&appKey=fa5b993d-af82-30e3-b033-a7588d716d95"></script>
        <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <style>
            body{background-color: #00ECC8;}
            #map_canvas{width:78%;height:97%;float:right;margin-right:10px;margin-top:10px;}
            #map_canvas{position:relative; z-index:1;}
            #search{
                text-align: center; width: 20%;   margin: 30px 0;
                float:left;}
            #header{text-align:center;width:20%;margin:30px 0;float:left;}
            #icon{margin-bottom: 0;width:50px;height:50px;text-align: left;}
            #spot{width:30px;height:30px;}
            h1{color:white; font-family: Hanna, serif;margin-bottom: 100px;}
            #route:hover{border:3px solid yellow;}
            #sub:hover{border:3px solid yellow;}
            #sub{border:none;}
            #route{border:none;}
            #admin{border:none;}
            #admin:hover{border:3px solid gray;}
            #header2{display:none;}
            a:link{text-decoration: none;}

            select{
                width:70px;color: #e07767;
            }
            #menu1{display: inline-block;width:20%;height:50%;
                   float:left;margin-top:100px;text-align:center;color:white; font-family: Hanna;
            }
            @media screen and (max-width:360px){
                #map_canvas{width:100%;height:100%;margin:0px;}

                #header{text-align:center;width:100%;margin:5px 0px;float:left;display: inline-block;float:left;}
                h1{color:white;font-family: Hanna, serif;display:inline-block;width:70%;margin-bottom: 100px;font-size: 20px;margin:0px;}
                #icon{display: block;margin:0px;width:30px;height:30px;margin:0px auto;text-align:center;}
                #search{width:100%;}
                #menu1{display: block;width:100%;margin:10px;float:none;}
            }

            input[type="text"]{width: 500px; height: 40px; color: black; font-size: 27px;}
            input[type="text"]:focus{background: black; color: white; font-size: 30px;}
            input[type="submit"]{width: 70px; height: 100px; font-size: 100px; cursor: pointer; }


        </style>
    </head>

    <body onload="main()">
        <script src="areaSelect.js"></script>

        <div id="header" >
            <a href="javascript:go_current()">
                <img src="img/taxi.ico" id="icon"/>
                <h1> 공연네비게이터 </h1></a>
        </div>

        <div id="map_canvas">
            <div id="output"></div>

        </div>  

        <div id="search">
            <select name="sido" id="sido" onchange="change(this)">
                <option value="">::시/도::</option>
                <option value="서울">서울</option>
                <option value="강원">강원</option>
                <option value="인천">인천</option>
                <option value="경기">경기</option>
                <option value="충북">충북</option>
                <option value="대전">대전</option>
                <option value="충남">충남</option>
                <option value="전북">전북</option>
                <option value="전남">전남</option>
                <option value="대구">대구</option>
                <option value="부산">부산</option>
                <option value="울산">울산</option>
                <option value="광주">광주</option>
                <option value="경남">경남</option>
                <option value="경북">경북</option>
                <option value="제주">제주</option>
            </select> 

            <select name="gugun" id="gugun">
                <option value="">::구/::</option>
            </select>

            <input type="submit" id="sub" name="submit" value="검색" onclick="onMoveMap()" style="color:white;width:50px;height:30px;font-size:12px;background-color:#3DB7CC;">
        </div>



        <div id="menu1">
            <h2>원하는 구간 길찾기</h2><br>
            &nbsp;<a style="text-decoration:none;" href="javascript:selectMenu('0')"><font color="white">&nbsp;>>출발위치로 지정</font></a><br>
            &nbsp;<a style="text-decoration:none;" href="javascript:selectMenu('1')"><font color="white">&nbsp;>>도착위치로 지정</font></a><br><br>
            <br><h2>현재 위치로 길찾기</h2><br>
            &nbsp;<a style="text-decoration:none;" href="javascript:selectMenu('2')"><font color="white">&nbsp;>>도착위치 지정</font></a><br>
            <input type="hidden" id="pixelX"/>
            <input type="hidden" id="pixelY"/>
            <br/>

            <button type ="button"  id="route"  onclick="getRouteData()" style="color:white;width:150px;height:30px;background:#3DB7CC;"> 길 찾기 </button>
            <br><br><br><br><br>
            <a href="javascript:;" 
               onclick="window.open('login.html', 'mt100', 'width=500 height=600 top=0 left=0 scrollbars=yes')" 
               onFocus="this.blur()"/> 

            <button type ="button" id="admin" style="width:150px;height:30px;background:gainsboro;"> 관리자 </button>
        </a>
    </div>

    <input type="hidden" id="startX"/>
    <input type="hidden" id="startY"/>
    <input type="hidden" id="endX"/>
    <input type="hidden" id="endY"/>

    <script src="jquery-1.11.3.js"></script>
    <script src="tMapApi.js"></script>

</body>
</html>

