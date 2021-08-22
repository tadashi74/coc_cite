<?php
    header('Content-Type: text/html; charset=utf-8');
    function multi_url($urls,$tk){
        $rank = array('leader'=>'대표', 'coLeader'=>'공동대표', 'member'=>'클랜원', 'admin' => '장로');
        $mh = curl_multi_init(); //멀티 cUrl초기화
        $conn = array(); //conn이라는 배열 선언
        $headr = array("Accept: application/json", "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjQ4MjRkMzBmLWI4MTUtNDFkZC1iZDkyLTg4Y2Q3OWMwM2E3YiIsImlhdCI6MTU4MTQ5NjI1NCwic3ViIjoiZGV2ZWxvcGVyLzhlM2JhZDdjLTU1YmMtMWQ4OS0zZDQ1LTBkMjBkNWJjZmFiZiIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjExMi4xNzUuMTg0Ljc5Il0sInR5cGUiOiJjbGllbnQifV19.Tnk3SqUU6bNjR_VfFMiH3EZPPTW4hhJ3bXiNvlv7UJ1G5qavQGdV4TfbE7lH-4bpfPusliGP8gBZF9JZlkh3NQ");
            foreach( $urls as $i => $url) {  //$urls == Array 출력 //$i == Array순서 출력 //$url == $urls값 출력
                $url_dat="https://api.clashofclans.com/v1/players/".$url;
                $conn[$i] = curl_init($url_dat); //세션 초기화, 헨들값 리턴
                curl_setopt($conn[$i], CURLOPT_HTTPHEADER, $headr);
                curl_setopt( $conn[$i], CURLOPT_RETURNTRANSFER, true); //결과값을 받을것인지
                curl_setopt( $conn[$i], CURLOPT_SSL_VERIFYPEER, false ); //서버인증의 유효성 검사.false일경우 하지 않음
                curl_setopt( $conn[$i], CURLOPT_SSL_VERIFYHOST, false ); //피어 인증서의 일반 이름이 있는지 여부를 확인
                curl_multi_add_handle( $mh, $conn[$i] ); //핸들 추가
            }
        $health = null;
        do {
            usleep( 10000 );
            $mrc = curl_multi_exec( $mh, $health ); //멀티 curl실행
        } while( $health > 0 );
        $res = array(); //$res배열 선언
        foreach( $urls as $i => $url ) {
            $dataname = json_decode(curl_multi_getcontent( $conn[$i] ),true); //연결정보 가져오기
            $res['value'][$dataname["tag"]] = array('name'=>$dataname['name'], 'attack'=>$dataname["attackWins"], 'defense'=>$dataname['defenseWins'], 'role'=> $rank[$dataname['role']], 'donate'=>$dataname['donations'], 'donated'=>$dataname['donationsReceived'],'townhall'=>$dataname['townHallLevel'].'H', 'trophy'=>'🏆️ '.$dataname['trophies'], 'url'=>'https://www.clashofstats.com/players/'.substr($dataname['tag'],1).'/summary', 'tag'=>substr($dataname['tag'], 1));
            curl_multi_remove_handle( $mh, $conn[$i] ); //핸들 제거
        }
        //print_r(json_encode($res));

        foreach ((array) $res['value'] as $key => $value) {
            $sort[$key] = $value['attack'];
        }
        array_multisort($sort, SORT_DESC, $res['value']);
        
        curl_multi_close($mh);
        return $res; 
    } 


    function clanconnect($url) {
        $headr = array("Accept: application/json", "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjQ4MjRkMzBmLWI4MTUtNDFkZC1iZDkyLTg4Y2Q3OWMwM2E3YiIsImlhdCI6MTU4MTQ5NjI1NCwic3ViIjoiZGV2ZWxvcGVyLzhlM2JhZDdjLTU1YmMtMWQ4OS0zZDQ1LTBkMjBkNWJjZmFiZiIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjExMi4xNzUuMTg0Ljc5Il0sInR5cGUiOiJjbGllbnQifV19.Tnk3SqUU6bNjR_VfFMiH3EZPPTW4hhJ3bXiNvlv7UJ1G5qavQGdV4TfbE7lH-4bpfPusliGP8gBZF9JZlkh3NQ");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);;
    } 

    function info($data) {
        if(isset($data['tag'])) {
            if(count($data['memberList']) != 0) {
                $url_list = array();
                for($i=0;$i<count($data["memberList"]);$i++) {
                    array_push($url_list, $data["memberList"][$i]["tag"]);
                }
                $finaldata = multi_url($url_list,$token);   
                $clantype = array('inviteOnly'=>'초대 한정', 'closed'=>'비공개','open'=>'공개');
                $clandata = array('tag'=>$data['tag'], 'name'=>$data['name'], 'img'=>$data['badgeUrls']['large'], 'type'=>$clantype[$data['type']], 'descript'=>$data['description'], 'level'=>$data['clanLevel'], 'members'=>$data['members'], 'requiredTrophies'=>'🏆️ '.$data['requiredTrophies'], 'isWarLogPublic'=>$data['isWarLogPublic'], 'warWins'=>$data['warWins']);     
                return array('result'=>true, 'finaldata'=>$finaldata, 'clandata'=>$clandata);
            } else {
                echo '<script>alert("존재하지 않는 클랜입니다"); history.back();</script>';
                return array('result'=>false);
            }
        } else {
            echo '<script>alert("존재하지 않는 클랜입니다"); history.back();</script>';
            return array('result'=>false);
        }
    }

    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
    if($_GET['param']=='') {
        echo '<script>alert("클랜이름 또는 클랜태그를 입력해 주세요!"); history.back();</script>';
        return;
    }

    $result;

    if(startsWith($_GET['param'], '#')) { //태그로 검색할 때
        $url_clan = "https://api.clashofclans.com/v1/clans/".urlencode($_GET['param']);
        $data=infwo(clanconnect($url_clan));
        $result=array('finaldata'=>$data['finaldata'], 'clandata'=>$data['clandata']);
    } else { //이름으로 검색할 때
        $url_clan = "https://api.clashofclans.com/v1/clans/?name=%20%20%20".urlencode($_GET['param']);
        $predata=clanconnect($url_clan);
        if(isset($predata['items'])) { //해당 이름을 가진 클랜이 1개 이상 존재한다면
            if(count($predata['items'])<2) { //이름을 가진 클랜이 하나라면
                $data = clanconnect("https://api.clashofclans.com/v1/clans/".urlencode($predata['items'][0]['tag']));
                $afterdata=info($data);
                $result=array('finaldata'=>$afterdata['finaldata'], 'clandata'=>$afterdata['clandata']);
            } else { //이름을 가진 클랜이 여러개라면
                echo '<script>location.href="http://kaan.dothome.co.kr/searchbyname.php?name='.$_GET['param'].'"    </script>';
            }
        }else{
            echo '<script>alert("존재하지 않는 클랜입니다"); history.back();</script>';
        }
    
    }
?>

<html>
    <head>
        <title>Clash Of Clans</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image⁄x-icon" href="./supercell.png">
        <script  src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script  src="https://developers.kakao.com/sdk/js/kakao.js"></script>
        <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
        <style> 
            input:not(.disabled){
                font-size: 16px;
                width: 90%;
                padding: 10px;
                border: 1px solid blue;
                outline: none;
                float: left;
                margin-bottom: 8px;
            }

            button:not(.disabled){
                width: 10%;
                padding: 10px;
                border: 1px solid blue;
                background: #1b5ac2;
                outline: none;
                float: right;
                color: #ffffff;
                margin-bottom: 8px;
            }

            body { 
                color:#151515; 
           }

            table {
                width: 100%;
                border-top: 1px solid #444444;
                border-collapse: collapse;
                table-layout:fixed;word-break:break-all;
            }

            th, td {
                border: 1px solid #444444;
                padding: 10px;
                text-align: center;
            }
            thead tr {
                background-color: #A9E2F3;
                color: #151515;
            }
        </style>
    </head>

    <body>
        <script type="text/javascript">
        // 출처 : http://tonks.tistory.com/79 
            function sortingNumber( a , b ){  
                if ( typeof a == "number" && typeof b == "number" ) return a - b; 
                // 천단위 쉼표와 공백문자만 삭제하기.  
                var a = ( a + "" ).replace( /[,\s\xA0]+/g , "" ); 
                var b = ( b + "" ).replace( /[,\s\xA0]+/g , "" ); 
                var numA = parseFloat( a ) + ""; 
                var numB = parseFloat( b ) + ""; 
                if ( numA == "NaN" || numB == "NaN" || a != numA || b != numB ) return false; 
                return parseFloat( a ) - parseFloat( b ); 
            } 

            /* changeForSorting() : 문자열 바꾸기. */ 
            function changeForSorting( first , second ){  
                // 문자열의 복사본 만들기. 
                var a = first.toString().replace( /[\s\xA0]+/g , " " ); 
                var b = second.toString().replace( /[\s\xA0]+/g , " " ); 
                var change = { first : a, second : b }; 
                if ( a.search( /\d/ ) < 0 || b.search( /\d/ ) < 0 || a.length == 0 || b.length == 0 ) return change; 
                var regExp = /(\d),(\d)/g; // 천단위 쉼표를 찾기 위한 정규식. 
                a = a.replace( regExp , "$1" + "$2" ); 
                b = b.replace( regExp , "$1" + "$2" ); 
                var unit = 0; 
                var aNb = a + " " + b; 
                var numbers = aNb.match( /\d+/g ); // 문자열에 들어있는 숫자 찾기 
                for ( var x = 0; x < numbers.length; x++ ){ 
                        var length = numbers[ x ].length; 
                        if ( unit < length ) unit = length; 
                } 
                var addZero = function( string ){ // 숫자들의 단위 맞추기 
                        var match = string.match( /^0+/ ); 
                        if ( string.length == unit ) return ( match == null ) ? string : match + string; 
                        var zero = "0"; 
                        for ( var x = string.length; x < unit; x++ ) string = zero + string; 
                        return ( match == null ) ? string : match + string; 
                }; 
                change.first = a.replace( /\d+/g, addZero ); 
                change.second = b.replace( /\d+/g, addZero ); 
                return change; 
            } 

            /* byLocale() */ 
            function byLocale(){ 
                var compare = function( a , b ){ 
                        var sorting = sortingNumber( a , b ); 
                        if ( typeof sorting == "number" ) return sorting; 
                        var change = changeForSorting( a , b ); 
                        var a = change.first; 
                        var b = change.second; 
                        return a.localeCompare( b ); 
                }; 
                var ascendingOrder = function( a , b ){  return compare( a , b );  }; 
                var descendingOrder = function( a , b ){  return compare( b , a );  }; 
                return { ascending : ascendingOrder, descending : descendingOrder }; 
            } 

            /* replacement() */ 
            function replacement( parent ){  
                var tagName = parent.tagName.toLowerCase(); 
                if ( tagName == "table" ) parent = parent.tBodies[ 0 ]; 
                tagName = parent.tagName.toLowerCase(); 
                if ( tagName == "tbody" ) var children = parent.rows; 
                else var children = parent.getElementsByTagName( "li" ); 
                var replace = { 
                    order : byLocale(), 
                    index : false, 
                    array : function(){ 
                        var array = [ ]; 
                        for ( var x = 0; x < children.length; x++ ) array[ x ] = children[ x ]; 
                        return array; 
                    }(), 
                    checkIndex : function( index ){ 
                        if ( index ) this.index = parseInt( index, 10 ); 
                        var tagName = parent.tagName.toLowerCase(); 
                        if ( tagName == "tbody" && ! index ) this.index = 0; 
                    }, 
                    getText : function( child ){ 
                        if ( this.index ) child = child.cells[ this.index ]; 
                        return getTextByClone( child ); 
                    }, 
                    setChildren : function(){ 
                        var array = this.array; 
                        while ( parent.hasChildNodes() ) parent.removeChild( parent.firstChild ); 
                        for ( var x = 0; x < array.length; x++ ) parent.appendChild( array[ x ] ); 
                    }, 
                    ascending : function( index ){ // 오름차순 
                        this.checkIndex( index ); 
                        var _self = this; 
                        var order = this.order; 
                        var ascending = function( a, b ){ 
                            var a = _self.getText( a ); 
                            var b = _self.getText( b ); 
                            return order.ascending( a, b ); 
                                }; 
                            this.array.sort( ascending ); 
                            this.setChildren(); 
                    }, 
                    descending : function( index ){ // 내림차순
                        this.checkIndex( index ); 
                        var _self = this; 
                        var order = this.order; 
                        var descending = function( a, b ){ 
                            var a = _self.getText( a ); 
                            var b = _self.getText( b ); 
                            return order.descending( a, b ); 
                        }; 
                        this.array.sort( descending ); 
                        this.setChildren(); 
                    } 
                }; 
                return replace; 
            } 

            function getTextByClone( tag ){  
                var clone = tag.cloneNode( true ); // 태그의 복사본 만들기. 
                var br = clone.getElementsByTagName( "br" ); 
                while ( br[0] ){ 
                    var blank = document.createTextNode( " " ); 
                    clone.insertBefore( blank , br[0] ); 
                    clone.removeChild( br[0] ); 
                } 
                var isBlock = function( tag ){ 
                    var display = ""; 
                    if ( window.getComputedStyle ) display = window.getComputedStyle ( tag, "" )[ "display" ]; 
                    else display = tag.currentStyle[ "display" ]; 
                    return ( display == "block" ) ? true : false; 
                }; 
                var children = clone.getElementsByTagName( "*" ); 
                for ( var x = 0; x < children.length; x++){ 
                    var child = children[ x ]; 
                    if ( ! ("value" in child) && isBlock(child) ) child.innerHTML = child.innerHTML + " "; 
                } 
                var textContent = ( "textContent" in clone ) ? clone.textContent : clone.innerText; 
                return textContent; 
            } 
        </script>
   
        <script> 
            function change() {
                let selectOption = document.getElementById("select");
                let num = selectOption.options[selectOption.selectedIndex].value;
                let order = document.getElementsByName('order');
                if(order[0].checked) sortTD(num);
                else reverseTD(num);
            }
                var clandata = <? echo json_encode($result['clandata']); ?>;
                var list = <? echo json_encode($result['finaldata']); ?>;  
        </script>

        <form action="clan.php" method="get">
            <input type="text" name="param" placeholder="클랜이름 또는 클랜태그를 입력하세요. Made by 칸">
	        <button type="submit">검색</button>
        </form>
    
        <table class="claninfo">
            <thead>
                <tr><th colspan="2">클랜정보</th></tr>
            </thead>
        
            <tr width="50%" height="50%">
                <td rowspan="7" bgcolder="white">
                    <script>
                        document.write('<img src="'+clandata['img']+'" style="max-width: 50%; height: "50%"');
                        document.write('</td>');
                        document.write('<td bgcolor="#CEF6F5" width="50%">이름: '+clandata['name']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td width="50%" >태그: '+clandata['tag']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td bgcolor="#CEF6F5" width="50%" height="50%">레벨: '+clandata['level']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td width="50%" height="50%">유형: '+clandata['type']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td bgcolor="#CEF6F5" width="50%" height="50%">맴버수: '+clandata['members']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td width="50%" height="50%">전쟁승리횟수: '+clandata['warWins']+'</td>');
                        document.write('</tr>');
                        document.write('<tr>');
                        document.write('<td bgcolor="#CEF6F5" width="50%" height="50%">트로피 조건: '+clandata['requiredTrophies']+'</td>');
                        document.write('</tr>');
                    </script>
            <tr>
                <td bgcolor="#A9E2F3" colspan="2">
                    <a href="javascript:void(0);" id="btn" >카카오톡으로 공유하기</a>
                    <a href="javascript:copy()"; id="btn"> Url 복사하기 </a>
                    <script type="text/javascript" src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
                    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
                    <script>
                        function copy(){
                            toastr.success('Copied to clipboard 🎉!');
                            var t = document.createElement("textarea");
                            document.body.appendChild(t);
                            t.value = window.location.href;
                            t.select();
                            document.execCommand('copy');
                            document.body.removeChild(t);
                        }

                        Kakao.init('d3903111e6c57045275e1c3f291a7b1f');      
                        $("#btn").click(function(e) { //jquery를 사용한다 가정
                            e.preventDefault();   //이벤트 버블링 prevent
                            Kakao.Link.sendCustom({
                                templateId: 37738,
                                templateArgs: {
                                    'name': clandata['name'],
                                    'img':clandata['img'],
                                    'content1':'레벨: '+clandata['level'],
                                    'content2':'맴버: '+clandata['members']+'명',
                                    'content3':'클랜유형: '+clandata['type'],
                                    'content4':'전쟁 승리 횟수: '+clandata['warWins'],
                                    'bt1':'자세히보기',
                                    'url':window.location.href,
                                    'bt2':'인게임',
                                    'bt2_url':'https://link.clashofclans.com/kr?action=OpenClanProfile&tag='+clandata['tag']
                                    
                                }      
                            });
                        });
                    </script>
                </td>
            </tr>
        </table>
    
        <div style="margin-top:7px;"></div>

        <table class="unabled">
            <thead>
                <tr><th>정렬기준: 
                    <select id="select" name="select" onchange="change()">
                        <option value="4" selected="selected">공성</option>
                        <option value="5">방성</option>
                        <option value="6">지원한 유닛</option>
                        <option value="7">지원받은 유닛</option>
                        <option value="1">마을회관</option>
                        <option value="2">트로피</option>
                        <option value="0">이름</option>
                    </select>
                    <input type="radio" class="disabled" name="order" value="up" onclick="change()"> 오름차순
                    <input type="radio" class="disabled" name="order" value="down" onclick="change()" checked> 내림차순
                </th></tr>
            </thead>
        </table>
 
        <div style="margin-top:7px;"></div>

        <table class="unabled" id="coc">
            <thead>
                <tr>
                    <th>유저</th><th>마을회관</th><th>트로피</th><th>직위</th><th>공성</th><th>방성</th><th>지원한 유닛</th><th>지원받은 유닛</th>
                </tr>
            </thead>
            
            <tbody>
                <script>
                    for(i in list['value']){
                        document.write("<tr>");
                        document.write("<td bgcolor=\"#CEF6F5\" onclick=\"window.open('"+list['value'][i]['url']+"')\" style=\"cursor:pointer;\">"+list['value'][i]['name']+"</td><td>"+list['value'][i]['townhall']+"</td><td bgcolor=\"#CEF6F5\">"+list['value'][i]['trophy']+"</td><td>"+list['value'][i]['role']+"</td><td bgcolor=\"#CEF6F5\">"+list['value'][i]['attack']+"</td><td>"+list['value'][i]['defense']+"</td><td bgcolor=\"#CEF6F5\">"+list['value'][i]['donate']+"</td><td>"+list['value'][i]['donated']+"</td>");
                        document.write("</tr>"); 
                    }
                </script>
           </tbody>
       </table>

        <script type="text/javascript">
            var myTable = document.getElementById( "coc" ); 
            var replace = replacement( myTable ); 
            function sortTD( index ){    
                replace.ascending( index );    
            } 
            function reverseTD( index ){    
                replace.descending( index );
            } 
        </script>     
    </body>
</html>