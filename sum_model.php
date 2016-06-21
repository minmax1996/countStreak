<?php
    
function getSummonerID($name,$server,$apiKey)
{
    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v1.4/summoner/by-name/%s?api_key=%s",$server,$server,$name,$apiKey);
    $result = json_decode(file_get_contents($query),true);
    $result = $result[$name];
    return $result['id'] ;
}


function getMatches($summonerId,$server,$apiKey)
{
    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/matchlist/by-summoner/%d?rankedQueues=TEAM_BUILDER_DRAFT_RANKED_5x5&seasons=SEASON2016&api_key=%s",$server,$server,$summonerId,$apiKey);
    $result = json_decode(file_get_contents($query),true);
    return $result;
}

function winorlose($summonerId,$server,$matchId,$apiKey) //доделать
{
    $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/match/%d?api_key=%s",$server,$server,$matchId,$apiKey);
    $result = json_decode(file_get_contents($query),true);
    $players=$result["participantIdentities"];
    for($i=1; $i<=10;$i++)
    {
        echo $players['player']['summonerId'];
        if($players['player']['summonerId'] == $summonerId)
            {
                $playerNumber=$i;
                
                break;
            }
    }
    if($playerNumber>=1 && $playerNumber<=5)
            $playerTeam=0;
        else
            $playerTeam=1;
   
    return $result["teams"][$playerTeam]["winner"];                         
}

function calculateStreak($summonerId,$server,$apiKey,$matchArray)
{
    
    $matches = $matchArray['matches'];
    //for($i=$matchArray['startIndex']; $i<=$matchArray['endIndex']; $i++)
    //{
    //    $b[i]=winorlose($summonerId,$server,$matches[$i]['matchId'],$apiKey);
    //    echo $b[i];
   // }
    echo winorlose($summonerId,$server,$matches[0]['matchId'],$apiKey);
    return $name;
}

?>