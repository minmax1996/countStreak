<?php

function getSummonerID($name, $server, $apiKey)
{
    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v1.4/summoner/by-name/%s?api_key=%s",
        $server, $server, $name, $apiKey);
    $result = json_decode(file_get_contents($query), true);
    //$result = $result[$name];
    //echo $result['id'];
    return $result[$name]['id'];
}


function getMatches($summonerId, $server, $apiKey)
{
    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/matchlist/by-summoner/%d?rankedQueues=TEAM_BUILDER_DRAFT_RANKED_5x5&seasons=SEASON2016&api_key=%s",
        $server, $server, $summonerId, $apiKey);
    $result = json_decode(file_get_contents($query), true);
    return $result;
}

function maxStreak(&$b)
{
    $s = 0; // счетчик нулей подряд
    $m = 0; // максимум количества нулей подряд
    $n = count($b);
    for ($i = 0; $i < $n; $i++) {
        if ($b[$i] == 0)
            $s++;
        if ($i + 1 == $n || $b[$i] != 0) {
            if ($s > $m)
                $m = $s;
            $s = 0;
        }
    }
    return $m;
}


function winorlose($summonerId, $server, $matchId, $apiKey)
{
    //echo "[".time()."] winorlose: get api start;<br>";

    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/match/%d?api_key=%s", $server,
        $server, $matchId, $apiKey);
    $result = json_decode(file_get_contents($query), true);

    //echo "[".time()."] winorlose: get api end;<br>";

    $participantIdentities = $result["participantIdentities"];
    for ($i = 0; $i < 10; $i++) {
        //echo $participantIdentities[$i]["player"]["summonerName"].'|';  //показ игроков в данной игре
        //echo $summonerId;
        //
        if ($participantIdentities[$i]["player"]["summonerId"] == $summonerId) {
            $playerNumber = $participantIdentities[$i]["participantId"];
            //echo $participantIdentities[$i]["player"]["summonerName"];
            // break;
        }
    }
    //echo "[".time()."] winorlose: get getplayernumber;<br>";
    if ($playerNumber >= 1 && $playerNumber <= 5)
        $playerTeam = 0;
    else
        $playerTeam = 1;
    if ($result["teams"][$playerTeam]["winner"] == "true")
        return 1;
    else
        return 0;
}

function calculateStreak($summonerId, $server, $apiKey, $matchArray, $amount)
{

    $matches = $matchArray['matches'];
    //echo $matches[0]['matchId'];$matchArray['endIndex']
    for ($i = 0; $i < $amount; $i++) {
        echo "[" . time() . "] winorlose game " . $i . "start;<br>";

        $b[$i] = winorlose($summonerId, $server, $matches[$i]['matchId'], $apiKey);
        sleep(1.4);
        echo "[" . time() . "] winorlose game " . $i . "end;<br>";
        echo $b[$i] . "<br>";
    }
    //echo winorlose($summonerId,$server,$matches[11]['matchId'],$apiKey);
    //echo winorlose($summonerId,$server,$matches[0]['matchId'],$apiKey);
    return maxStreak($b);
}


//Array
//(
//    [0] => HTTP/1.1 200 OK
//    [reponse_code] => 200
//    [Date] => Fri, 01 May 2015 12:56:09 GMT
//    [Server] => Apache
//    [X-Powered-By] => PHP/5.3.3-7+squeeze18
//    [Set-Cookie] => PHPSESSID=ng25jekmlipl1smfscq7copdl3; path=/
//    [Expires] => Thu, 19 Nov 1981 08:52:00 GMT
//    [Cache-Control] => no-store, no-cache, must-revalidate, post-check=0, pre-check=0
//    [Pragma] => no-cache
//    [Vary] => Accept-Encoding
//    [Content-Length] => 872
//    [Connection] => close
//    [Content-Type] => text/html
//)
function parseHeaders( $headers )
{
    $head = array();
    foreach( $headers as $k=>$v )
    {
        $t = explode( ':', $v, 2 );
        if( isset( $t[1] ) )
            $head[ trim($t[0]) ] = trim( $t[1] );
        else
        {
            $head[] = $v;
            if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                $head['reponse_code'] = intval($out[1]);
        }
    }
    return $head;
}
?>