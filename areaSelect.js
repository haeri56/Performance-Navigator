/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var sido;
var gugun;
var si = new Array('강원', '서울', '인천', '경기', '충북', '대전', '충남', '전북', '전남', '대구', '부산', '울산', '광주', '경남', '경북', '제주');
var gu1 = new Array('양양', '고성', '영월', '평창', '횡성');
var gu2 = new Array('송파구', '광진구', '종로구', '강남구', '강동구');
var gu3 = new Array('중구', '동구', '남구', '서구', '계양');
var gu4 = new Array('고양', '권선구', '영통구', '장안구', '만안구');
var gu5 = new Array('서원구', '흥덕구', '청원구', '옥천');
var gu6 = new Array('대덕구', '유성구', '서구', '중구', '동구');
var gu7 = new Array('부여', '쳥양', '서천', '흥성', '');
var gu8 = new Array('덕진구', '완산구', '군산', '부안', '임실');
var gu9 = new Array('여수', '담양', '곡성', '구례', '고');
var gu10 = new Array('중구', '동구', '수서구', '달서구', '달성');
var gu11 = new Array('동래구', '해운대구', '금정구', '수영구', '기장');
var gu13 = new Array('동구', '서구', '남구', '북구', '광산구');
var gu14 = new Array('김해', '거제', '통영', '밀양', '사천');
var gu15 = new Array('영덕', '칠곡', '울진', '울릉', '붕화');
var gu16 = new Array('제주');
var gu12 = new Array('남구', '중구', '동구', '북구');
//var gu1=new Array('양양','고성','영월','평창','횡성');
var data;
function change(s) {
    for (var i = 0; i < 16; i++) {
        if (si[i] == s.value)
        {
            gugun = i + 1;

            break;
        }
    }

    //var select2 = document.myform.gugun;
    select2 = document.getElementById("gugun");
    switch (gugun) {
        case 1:
            data = gu1;
            break;
        case 2:
            data = gu2;
            break;
        case 3:
            data = gu3;
            break;
        case 4:
            data = gu4;
            break;
        case 5:
            data = gu5;
            break;
        case 6:
            data = gu6;
            break;
        case 7:
            data = gu7;
            break;
        case 8:
            data = gu8;
            break;
        case 9:
            data = gu9;
            break;
        case 10:
            data = gu10;
            break;
        case 11:
            data = gu11;
            break;
        case 12:
            data = gu12;
            break;
        case 13:
            data = gu13;
            break;
        case 14:
            data = gu14;
            break;
        case 15:
            data = gu15;
            break;
        case 16:
            data = gu16;
            break;
        default :
            break;
    }
    select2.innerHTML = "";

    for (i = 0; i < data.length; i++)
    {
        var optionEI = document.createElement("option");

        optionEI.value = data[i];
        optionEI.appendChild(document.createTextNode(data[i]));
        select2.appendChild(optionEI);
    }
    // select2.style.display = "block";
}

