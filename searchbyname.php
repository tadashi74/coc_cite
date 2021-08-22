<?php
    header('Content-Type: text/html; charset=utf-8');
    function clanconnect($url) {
        $headr = array("Accept: application/json", "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjQ4MjRkMzBmLWI4MTUtNDFkZC1iZDkyLTg4Y2Q3OWMwM2E3YiIsImlhdCI6MTU4MTQ5NjI1NCwic3ViIjoiZGV2ZWxvcGVyLzhlM2JhZDdjLTU1YmMtMWQ4OS0zZDQ1LTBkMjBkNWJjZmFiZiIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjExMi4xNzUuMTg0Ljc5Il0sInR5cGUiOiJjbGllbnQifV19.Tnk3SqUU6bNjR_VfFMiH3EZPPTW4hhJ3bXiNvlv7UJ1G5qavQGdV4TfbE7lH-4bpfPusliGP8gBZF9JZlkh3NQ");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    } 
    $url = "https://api.clashofclans.com/v1/clans/?name=%20%20%20".urlencode($_GET['name']);
    $predata=clanconnect($url);
    $choice=array();
    $clantype = array('inviteOnly'=>'초대 한정', 'closed'=>'비공개','open'=>'공개');
    $num;
    $choice['value']=array();
    (count($predata['items']) > 50)?$num=50:$num=count($predata['items']); 
    for($i=0;$i<$num;$i++) {
        $choice['value'][$predata['items'][$i]['tag']] = array("url"=>"http://kaan.dothome.co.kr/clan.php?param=".urlencode($predata['items'][$i]['tag']), "name"=>$predata['items'][$i]['name'],"tag"=>$predata['items'][$i]['tag'],"type"=>$clantype[$predata['items'][$i]['type']],"level"=>$predata['items'][$i]['clanLevel'], 'members'=>$predata['items'][$i]['members']);
    }
    $result=array('clanlist'=>$choice);
?>

<html>
    <head>
        <title>Clash Of Clans</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image⁄x-icon" href="./supercell.png">
        <script  src="http://code.jquery.com/jquery-latest.min.js"></script>
        <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
        <style> 
            .color: {
              color: #151515;
            }
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

            body { color:#151515; }

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
            var clanlist = <? echo json_encode($result['clanlist']); ?>;
            console.log(clanlist);    
        </script>

        <form action="clan.php" method="get">
            <input type="text" name="param" placeholder="클랜이름 또는 클랜태그를 입력하세요. Made by 칸">
	        <button type="submit">검색</button>
        </form>
        <div style="margin-top:7px;"></div>

        <table class="unabled">
            <thead>
                <tr><th>정렬기준: 
                    <select id="select" name="select" onchange="change()">
                        <option value="0" selected="selected">이름</option>
                        <option value="2">클랜레벨</option>
                        <option value="3">맴버수</option>
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
                    <th>이름</th><th>태그</th><th>클랜레벨</th><th>맴버수</th><th>유형</th>
                </tr>
            </thead>
            
            <tbody>
                <script>
                    for(i in clanlist['value']){
                        document.write("<tr>");
                        document.write("<td bgcolor=\"#CEF6F5\" onclick=\"window.open('"+clanlist['value'][i]['url']+"')\" style=\"cursor:pointer;\"> <U> <font color=\"#2E2EFE\">"+clanlist['value'][i]['name']+"</U> </font></td><td>"+clanlist['value'][i]['tag']+"</td><td bgcolor=\"#CEF6F5\">"+clanlist['value'][i]['level']+"</td><td>"+clanlist['value'][i]['members']+"</td><td bgcolor=\"#CEF6F5\">"+clanlist['value'][i]['type']+"</td>");
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