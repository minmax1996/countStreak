<?php
    //require_once("database.php");    
    //require_once("models/sum_model.php");
    require_once("models/league_class.php");
    
    $apiKey="RGAPI-257A7B39-3619-4A06-A7C5-BA7FC7DD194E";
    $server=$_GET['server'];
    $summonerName=$_GET['summonerName'];
    $amount=$_GET['amount'];
    $content_view="search_view.php";
    
    try{

        if($summonerName != "" && $server != "" )
        {   
            $summoner = new Summoner($summonerName,$server,$apiKey);
            
            $matchArray = $summoner->getMatchList();
            if(isset($_GET['result_submit']))
                {
                    echo "result_submit";
                    $content_view = "result_view.php";
                }
            elseif(isset($_GET['matchhistory_submit']))
                {
                    echo "matchhistory";
                    $content_view = "matchhistory_view.php";
                } 
        }   
    
    include("views/template_view.php");
    
    
    }catch( ResponseException $e)
        {
            print("response exception:".$e->getMessage()." in ".$e->getFile()." line: ".$e->getLine());
        }
    catch(Exception $e)
        {
            echo 'exception: '.$e->getMessage().'<br>';
        }
    
  
?>