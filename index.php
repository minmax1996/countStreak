<?php
    require_once("database.php");    
    require_once("models/sum_model.php");
    $apiKey="609f18c4-27d9-4dee-a37b-513e25d476c6";
    $link=db_connect();
    //$articles = articles_all($link);
    if($_POST['summonerName']!="")
    {
        $server=$_POST['server'];
        $id=getSummonerID($_POST['summonerName'],$server,$apiKey);
        $matchArray=getMatches($id,$server,$apiKey);
        $count=calculateStreak($summonerId,$server,$apiKey,$matchArray);
        include("views/result_view.php");
    }
    else
        include("views/search_view.php");
?>