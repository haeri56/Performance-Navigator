
var output = document.getElementById("output");
//좌표 변환 
var pr_3857 = new Tmap.Projection("EPSG:3857");
var pr_4326 = new Tmap.Projection("EPSG:4326");

function getLonLat(coordX, coordY) {
    return new Tmap.LonLat(coordX, coordY).transform(pr_4326, pr_3857);
}

var map;

var MARKERS = [];
var MARKERLAYER;
var kmlLayer;

var latitude, longitude;

var zoom;
var cLonlat;
var size;
var offset;
var icon;
var label;
var cPos_marker;

function main() {

    window.oncontextmenu = function (e) {
        showMenu(e);
        return false;
    };
    window.onkeydown = doKeyDown;

    locate();
}

function locate() {
    zoom = 13;
    navigator.geolocation.getCurrentPosition(initialize, fail);
    navigator.geolocation.watchPosition(do_something, fail);
}
function do_something(position) {
    
    // 마커, 위치 좌표 설정
    MARKERLAYER.removeMarker(cPos_marker);
    latitude = position.coords.latitude;
    longitude = position.coords.longitude;
    cLonlat = getLonLat(longitude, latitude);   // 현재 위치의 좌표 
    cPos_marker.lonlat = cLonlat;
    MARKERLAYER.addMarker(cPos_marker);
    
    // 현재 위치와 보고있는 위치를 받아와서
    // 현재 위치가 아닌 곳을 보고 있다면 그곳을 센터로, 현재 위치 주변에 있다면 현재 위치를 센터로
    var curP = new Tmap.Pixel(cLonlat.lon, cLonlat.lat);
    var viewP = new Tmap.Pixel(map.getCenter().lon, map.getCenter().lat);
    var dist = curP.distanceTo(viewP);

    if (dist < 1000) {
        map.setCenter(cLonlat, map.getZoom());
    } else {
        map.setCenter(map.getCenter(), map.getZoom());
    }

}

function initialize(position) {
    map = new Tmap.Map({div: "output", width: '100%', height: '100%'});
    map.addControls([
        new Tmap.Control.KeyboardDefaults(), // 키보드의 키로 맵을 컨트롤 할 수 있게 하는 컨트롤러
        new Tmap.Control.MousePosition(), // 현재 위치가 가지는 좌표값을 맵 위에 표시해주는 컨트롤러 
        new Tmap.Control.OverviewMap()
    ]);

    addMarkerLayer();
    MARKERLAYER = new Tmap.Layer.Markers("MarkerLayer");
    map.addLayer(MARKERLAYER);
    size = new Tmap.Size(22, 22);
    offset = new Tmap.Pixel(-(size.w / 2), -(size.h));
    icon = new Tmap.Icon('http://www.robotwoods.com/dev/misc/bluecircle.png', size, offset);
    //label = new Tmap.Label("<div><img src='http://localhost:8888/Taxi/perform1.jpg'><br>공연장1</div>");
//     cLonlat = getLonLat(0, 0); 
    latitude = position.coords.latitude;
    longitude = position.coords.longitude;
    cLonlat = getLonLat(longitude, latitude);   // 현재 위치의 좌표 
    cPos_marker = new Tmap.Marker(cLonlat, icon);
    MARKERLAYER.addMarker(cPos_marker);

    // MARKERLAYER.removeMarker(cPos_marker);

    // zoom=getzoom();
    map.setCenter(cLonlat, zoom);
    zoom = map.getZoom();
    // cPos_marker.lonlat=cLonlat;

    // MARKERLAYER.addMarker(cPos_marker);

    setLayers();
    loadPerformData();



}

var CallbackEvent = function () {
    this.callback_method;
};
var EventRegistration = function (callbackEvent) {
    this.callbackEvent = callbackEvent;
    this.do_work = function () {
        alert('두워크');
        loadPerformData();
        this.callbackEvent.callback_method();
    };
};
var EventApplication = function () {
    this.main = function () {
        var callbackEvent = new CallbackEvent();
        callbackEvent.callback_method = function () {
            //print('call callback method from caller');
            onClickTitle(pos_x, pos_y);
            alert('콜백메소드');
            // 
        };

        var eventRegistration = new EventRegistration(callbackEvent);
        eventRegistration.do_work();
    };
};

function fail() {
    //output.innerHTML = "<h1>do not found</h1>";
    output.innerHTML = "<h1>nono</h1>";
    //alert('navigator.geolocation failed, may not be supported123123');
    alert('why22');
}

function loadPerformData(callback) {
    //alert('로드데이터');
    //오늘 요일 받아오기

    var current_day = new Date();
    var theDay = current_day.getDay();
    var label;
    //alert(theDay);

    // 오늘 날짜에 해당하는 인기도만 가져오기!
    // 인기도 정렬하기!
    // 갯수 세서 3으로 나누기

    jQuery.getJSON("getGeorecord.php", function (data) {
        //alert('로');

        if (data.perform_data) {
            //   alert('겟제이슨2');
            var totalCount = data.perform_data.length;
            var starCount;
            for (var i = 0; i < totalCount; i++) {

                //    alert('겟제이슨3');
                label = "공연제목: " + data.perform_data[i].title + "<br/>"
                        + "<div><img src='" + data.perform_data[i].img + "' width='200' height='200' onerror=\"this.src='img/no_img.png'\" >" + "<br/>"
                        + "공연장: " + data.perform_data[i].place + "<br/>";

                var finishTime = "";
                if (theDay === 1) {
                    finishTime = data.perform_data[i].mon;
                } else if (theDay === 2) {
                    finishTime = data.perform_data[i].tue;
                } else if (theDay === 3) {
                    finishTime = data.perform_data[i].wed;
                } else if (theDay === 4) {
                    finishTime = data.perform_data[i].thu;
                } else if (theDay === 5) {
                    finishTime = data.perform_data[i].fri;
                } else if (theDay === 6) {
                    finishTime = data.perform_data[i].sat;
                } else if (theDay === 0) {
                    finishTime = data.perform_data[i].sun;
                }
                label += "공연종료시간: " + finishTime + "<br/>";
                if (finishTime == '공연없음') {
                    continue;
                }

                label += "인기도: ";
                if (i >= 0 && i < totalCount / 3) {
                    starCount = 3;
                    label += "★★★ (태그수 ";
                } else if (i >= totalCount / 3 && i < (totalCount / 3) * 2) {
                    starCount = 2;
                    label += "★★☆ (태그수 ";
                } else {
                    starCount = 1;
                    label += "★☆☆ (태그수 ";
                }
                label += data.perform_data[i].popularity + ")<br>";

                map_coord(data.perform_data[i].gpsX, data.perform_data[i].gpsY, finishTime, starCount, label);

            }
        }
    }).fail(function (data, textStatus, error) {

        alert("getJSON failed, status: " + textStatus + ", error: " + error + data.responseText)
    });
//    if( typeof callback === "function" ) {
//        callback();
//    }

}



function map_coord(pos_x, pos_y, finishTime, starCount, label) {

    if (pos_x !== 0 && pos_y !== 0) {
        var position = new Tmap.LonLat(pos_x, pos_y).transform(pr_4326, pr_3857);  // 다음 좌표계에 좌표 입력 

        //loadGetAddressFromLonLat(position, label);
        addMarker(position, finishTime, starCount, new Tmap.Label(label));
    }

}

function drawKML() {
    var xmlObj = new Tmap.Format.KML({extractStyles: true, extractAttributes: true});                 //line 1
    var stBBOX = new Tmap.Strategy.BBOX({ratio: 1});                                                 //line 2
    var prtcl = new Tmap.Protocol.HTTP({url: "./test.kml", format: xmlObj});                           //line 3
    var kmlLayer = new Tmap.Layer.Vector("strategyTest", {strategies: [stBBOX], protocol: prtcl});   //line 4
    map.addLayers([kmlLayer]);                                                                      //line 5
}

function addMarkerLayer() {
    // Marker Layer 생성
    MARKERLAYER = new Tmap.Layer.Markers("MarkerLayer");
    map.addLayer(MARKERLAYER);
}

// Marker 설정 
function addMarker(lonlat, finishTime, starCount, label) {

    // 종료시간이 임박해 왔으면 (종료시간: finishTime, 현재시간: currentTime);
    
    var today = new Date();
    //h = today.getHours();
    //m = today.getMinutes();
    h = 10;
    m = 10;
    currentTime = 60 * h + 1 * m;

    // 종료 시, 분 가져오기
    finH = finishTime.substring(0, finishTime.indexOf("시"));
    finM = finishTime.substring(finishTime.indexOf("시") + 1, finishTime.indexOf("분"));
    finishTime = 60 * finH + 1 * finM;

    console.log(finM);

    var size = new Tmap.Size(22, 22);

    // 인기도에 따른 마커 사이즈 변경
    if (starCount === 1) {
        size = new Tmap.Size(19, 19);
    } else if (starCount === 2) {
        size = new Tmap.Size(25, 25);
    } else if (starCount === 3) {
        size = new Tmap.Size(35, 35);
    }

    var offset = new Tmap.Pixel(-(size.w / 2), -(size.h / 2));
    var icon = new Tmap.Icon('img/ico_spot2.png', size, offset);
    
    //종료시간이 임박해 왔으면 아이콘 변경! (종료시간: finishTime, 현재시간: currentTime);
    if (finishTime - 30 <= currentTime && finishTime + 10 >= currentTime) {
        var temp = Math.abs(currentTime - finishTime);
        offset = new Tmap.Pixel(-(size.w / 2), -(size.h / 2));
        icon = new Tmap.Icon('img/ico_spot_b_over.png', size, offset);
    }
    // 아니면 그대로 

    var theMarker = new Tmap.Markers(lonlat, icon, label);  // lonlat label 붙임 
    MARKERLAYER.addMarker(theMarker);

    // 마커에 추가
    MARKERS.push(theMarker);

    theMarker.events.register("click", theMarker, onClickMarker);
    theMarker.events.register("mouseover", theMarker, onMouseMarker);
    theMarker.events.register("mouseout", theMarker, onMouseMarker);

}

// 해당 marker의 label을 표시 / 숨김
function onMouseMarker(evt) {
    if (evt.type === "mouseover") {
        this.popup.show();
    } else {
        this.popup.hide();
    }
}

// 마커 클릭 시 길 찾기 
function onClickMarker(evt) {

    if (kmlLayer !== null) {
        kmlLayer.destroyFeatures();
    }

    if (marker3 !== null) {
        MARKERLAYER.removeMarker(mks[2]);
    }
    if (marker1 !== null) {
        MARKERLAYER.removeMarker(mks[0]);
    }
    if (marker2 !== null) {
        MARKERLAYER.removeMarker(mks[1]);
    }

    var curLonLat = getLonLat(longitude, latitude);
    var selLonLat = this.lonlat;

    jQuery('#startX').val(curLonLat.lon);
    jQuery('#startY').val(curLonLat.lat);
    startX = jQuery('#startX').val();
    startY = jQuery('#startY').val();

    jQuery('#endX').val(selLonLat.lon);
    jQuery('#endY').val(selLonLat.lat);
    endX = jQuery('#endX').val();
    endY = jQuery('#endY').val();

    // 현재 위치와 선택 위치의 거리 비교
    var ln = (Math.abs(curLonLat.lon - this.lonlat.lon)) / 2.0;
    var lt = (Math.abs(selLonLat.lat - this.lonlat.lat)) / 2.0;
    if (curLonLat.lon > this.lonlat.lon)
        ln += this.lonlat.lon;
    else
        ln += curLonLat.lon;

    if (selLonLat.lat > this.lonlat.lat)
        lt += this.lonlat.lat;
    else
        lt += selLonLat.lat;

    var cll = new Tmap.LonLat(ln, lt);

    // 거리 계산
    var dcll = Math.sqrt(Math.pow((Math.abs(curLonLat.lon - this.lonlat.lon)), 2) + Math.pow((Math.abs(selLonLat.lat - this.lonlat.lat)), 2));

    // 거리에 따른 지도 확대 축소
    if (dcll < 5000) {
        map.setCenter(cll, 15);
    } else if (dcll < 10000) {
        map.setCenter(cll, 14);
    } else if (dcll < 20000) {
        map.setCenter(cll, 13);
    } else if (dcll < 30000) {
        map.setCenter(cll, 12);
    } else if (dcll < 40000) {
        map.setCenter(cll, 11);
    } else if (dcll < 50000) {
        map.setCenter(cll, 10);
    } else
        map.setCenter(cll, 10);
    
    // 길 찾기 함수 호출
    getRouteData();
}

// 타이틀 클릭 시 길 찾기
function onClickTitle(pos_x, pos_y, callback) {

    alert("경로를 나타냅니다.");

    if (marker3 !== null) {
        MARKERLAYER.removeMarker(mks[2]);
    }
    if (marker1 !== null) {
        MARKERLAYER.removeMarker(mks[0]);
    }
    if (marker2 !== null) {
        MARKERLAYER.removeMarker(mks[1]);
    }

    var curLonLat = getLonLat(longitude, latitude);
    var selLonLat = new Tmap.LonLat(pos_x, pos_y).transform(pr_4326, pr_3857);

    jQuery('#startX').val(curLonLat.lon);
    jQuery('#startY').val(curLonLat.lat);
    startX = jQuery('#startX').val();
    startY = jQuery('#startY').val();

    jQuery('#endX').val(selLonLat.lon);
    jQuery('#endY').val(selLonLat.lat);
    endX = jQuery('#endX').val();
    endY = jQuery('#endY').val();

    var cll = new Tmap.LonLat((longitude + pos_x) / 2.0, (latitude + pos_y) / 2.0).transform(pr_4326, pr_3857);

    map.setCenter(cll, 12);

    getRouteData();

    pos_x = undefined;
    pos_y = undefined;

}

// map 클릭 시에 좌표 값 변환 
function onClickMap(e) {

    var lonlat = map.getLonLatFromViewPortPx(e.xy);
    loadGetAddressFromLonLat(lonlat);

}

function onClickMap1(e) {
    // 현재 중심 위치 받아와서 경도, 위도를 주소로 변환
    var c_ll = map.getCenter();
    loadGetAddressFromLonLat(c_ll);
}

// 위도,경도 -> 주소

// 주소 
function onCompleteLoadGetAddressFromLonLat() {
    var fullAddress = jQuery(this.responseXML).find("fullAddress").text();

    // 지정 위치에 마커 추가 
    addMarker(this.ll, new Tmap.Label(this.labell + "<br/> 주소: " + fullAddress));

}

// MARKERS 전체 삭제 
function delTotalMarker() {

    for (var i = 0; i < MARKERS.length; i++) {
        // Marker중 개별적으로 삭제된 것을 제외하고 삭제
        if (MARKERS[i].lonlat !== undefined) {

            MARKERLAYER.removeMarker(MARKERS[i]);
            MARKERS[i].initialize();
            MARKERS[i].destroy();

            map.removePopup(MARKERS[i].popup);
            MARKERS[i].popup.initialize();
            
        }
    }
    // MARKERS[] 전체 삭제
    MARKERS.splice(0, MARKERS.length);
}

// 해당 marker 삭제
function delOneMarker() {

    var markerNum = MARKERS.length - 1;

    // MARKERLAYER에서 MARKERLAYER는 삭제되지만 MARKERS[]에서는 존재~(초기화는 됨)
    MARKERLAYER.removeMarker(MARKERS[markerNum]);
    MARKERS[markerNum].initialize();
    MARKERS[markerNum].destroy();

    // MARKERS[]에 해당 marker를 삭제
    MARKERS.splice(markerNum, 1);
}

function doKeyDown(e) {
    switch (e.keyCode) {
        // d
        case 68:
            getRouteData();
            break;
    }
}

// 길찾기 레이어 등록
function setLayers() {

    if (!map) {
        var msg = "map객체가 초기화되기 전에 레이어가 등록되었습니다.";
        alert(msg);
        return;
    }

    var context = {
        getColor: function (feature) {
            var color = '#aaaaaa';
            if (feature.attributes.clazz && feature.attributes.clazz === 4) {
                color = '#ee0000';
            } else if (feature.cluster) {
                var onlyFour = true;
                for (var i = 0; i < feature.cluster.length; i++) {
                    if (onlyFour && feature.cluster[i].attributes.clazz !== 4) {
                        onlyFour = false;
                    }
                }
                if (onlyFour === true) {
                    color = '#ee0000';
                }
            }
            return color;
        }
    };

    var style = new Tmap.Style({
        pointRadius: 5,
        fillColor: "${getColor}",
        fillOpacity: 1,
        strokeColor: "#330066",
        strokeWidth: 2,
        strokeOpacity: 1,
        graphicZIndex: 1
    }, {
        context: context
    });

    var v_option = {renderers: ['Canvas', 'SVG', 'VML'], styleMap: style};
    kmlLayer = new Tmap.Layer.Vector("kml", v_option);
    MARKERLAYER = new Tmap.Layer.Markers("MarkerLayer");
    map.addLayer(kmlLayer);
    map.addLayer(MARKERLAYER);
}

// 메뉴 보이기 
function showMenu(evt) {
    var menu1 = document.getElementById("menu1");
    menu1.style.left = evt.clientX + "px";
    menu1.style.top = evt.clientY + "px";
    menu1.style.display = 'block';
    pixelX = evt.clientX;
    pixelY = evt.clientY;
}

// 메뉴 닫기 
function closeMenu(evt) {
    var menu1 = document.getElementById("menu1");
    menu1.style.display = 'none';
}

var marker1 = null, marker2 = null, marker3 = null;
var mks = new Array(3);
// 메뉴 선택
function selectMenu(selectVal) {
    
    if (selectVal === '0') {

        if (marker1 !== null) {
            MARKERLAYER.removeMarker(mks[0]);
        }
        if (kmlLayer !== null) {
            kmlLayer.destroyFeatures();
        }

        var lonLat = map.getLonLatFromPixel(new Tmap.Pixel(pixelX - 310, pixelY));

        jQuery('#startX').val(lonLat.lon);
        jQuery('#startY').val(lonLat.lat);
        startX = jQuery('#startX').val();
        startY = jQuery('#startY').val();
        var size = new Tmap.Size(31, 35);
        var offset = new Tmap.Pixel(-(size.w / 2), -size.h);
        var icon = new Tmap.IconHtml("<img src=img/ico_spot_b.png />", size, offset);
        marker1 = new Tmap.Markers(lonLat, icon);
        MARKERLAYER.addMarker(marker1);
        mks[0] = marker1;
    } else if (selectVal === '1') {
        if (marker2 !== null) {
            MARKERLAYER.removeMarker(mks[1]);
        }
        if (kmlLayer !== null) {
            kmlLayer.destroyFeatures();
        }

        var lonLat = map.getLonLatFromPixel(new Tmap.Pixel(pixelX - 310, pixelY));
        
        jQuery('#endX').val(lonLat.lon);
        jQuery('#endY').val(lonLat.lat);
        endX = jQuery('#endX').val();
        endY = jQuery('#endY').val();
        var size = new Tmap.Size(31, 35);
        var offset = new Tmap.Pixel(-(size.w / 2), -size.h);
        var icon = new Tmap.IconHtml("<img src=img/ico_spot_b.png />", size, offset);
        marker2 = new Tmap.Markers(lonLat, icon);
        MARKERLAYER.addMarker(marker2);
        
        mks[1] = marker2;
    } else if (selectVal === '2') {
        if (marker3 !== null) {
            MARKERLAYER.removeMarker(mks[2]);
        }
        if (marker1 !== null) {
            MARKERLAYER.removeMarker(mks[0]);
        }
        if (marker2 !== null) {
            MARKERLAYER.removeMarker(mks[1]);
        }

        if (kmlLayer !== null) {
            kmlLayer.destroyFeatures();
        }

        var curLonLat = getLonLat(longitude, latitude);
        var selLonLat = map.getLonLatFromPixel(new Tmap.Pixel(pixelX - 310, pixelY));


        jQuery('#startX').val(curLonLat.lon);
        jQuery('#startY').val(curLonLat.lat);
        startX = jQuery('#startX').val();
        startY = jQuery('#startY').val();

        jQuery('#endX').val(selLonLat.lon);
        jQuery('#endY').val(selLonLat.lat);
        endX = jQuery('#endX').val() - 100;
        endY = jQuery('#endY').val();

        var size = new Tmap.Size(31, 35);
        var offset = new Tmap.Pixel(-(size.w / 2), -size.h);
        var icon = new Tmap.IconHtml("<img src=img/ico_spot_b.png />", size, offset);
        marker3 = new Tmap.Markers(selLonLat, icon);
        MARKERLAYER.addMarker(marker3);
        mks[2] = marker3;
    }
}

// 길 찾기 
function getRouteData() {

    var startLon = jQuery('#startX').val();
    var startLat = jQuery('#startY').val();
    var endLon = jQuery('#endX').val();
    var endLat = jQuery('#endY').val();

    if (startLon === 0 || startLat === 0) {
        alert('출발 위치를 선택하세요.');
    } else if (endLon === 0 || endLat === 0) {
        alert('도착 위치를 선택하세요');
    } else {

        var startLonLat = new Tmap.LonLat(startLon, startLat);
        var endLonLat = new Tmap.LonLat(endLon, endLat);

        tData = new Tmap.TData();

        var option = {
            version: "1",
            format: 'xml',
            detailPosFlag: 1
        };

        tData.getRoutePlan(startLonLat, endLonLat, option);

        tData.events.register("onComplete", tData, onLoadSuccess);
    }

}

// 길 찾기 성공 시 kml 화면에 뿌리기 
function onLoadSuccess() {

    var kmlForm = new Tmap.Format.KML().read(this.responseXML);
    for (var i = 0; i <= kmlForm.length - 1; i++) {
        kmlLayer.addFeatures([kmlForm[i]]);
    }
}


function onMoveMap() {

    var si = document.getElementById("sido").options[document.getElementById("sido").selectedIndex].value;
    var gu = document.getElementById("gugun").options[document.getElementById("gugun").selectedIndex].value;
    var lt, ln;

    if (si === "서울") {
        if (gu === "송파구") {
            lt = 4514426.730550;
            ln = 14150401.809852;
        } else if (gu === "광진구") {
            lt = 4517948.708381;
            ln = 14147460.810749;
        } else if (gu === "종로구") {
            lt = 4521555.771576;
            ln = 14134160.296145;
        } else if (gu === "강남구") {
            lt = 4512690.359740;
            ln = 14139823.056797;
        } else if (gu === "강동구") {
            lt = 4518482.764001;
            ln = 14156884.314865;
        }
        var zoom = 14;
        var cLonlat = new Tmap.LonLat(ln, lt);   // 현재 위치의 좌표 
        map.setCenter(cLonlat, zoom);
    } else if (si === "경기") {
        if (gu === "영통구") {
            lt = 4476918.390690;
            ln = 14142061.815446;
            var zoom = 14;
            var cLonlat = new Tmap.LonLat(ln, lt);   // 현재 위치의 좌표 
            map.setCenter(cLonlat, zoom);
        }
    }

}
function go_current() {
    map.setCenter(cLonlat, zoom);
}
