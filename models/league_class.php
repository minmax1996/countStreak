<?php
class ResponseException extends Exception{}; 
define('MYSQL_SERVER','localhost');
define('MYSQL_USER','mysql');
define('MYSQL_PASSWORD','admin123');
define('MYSQL_DB','summoners');

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


class Summoner
{
    public $summonerName;
    public $summonerId;
    public $server;
    private $apiKey;
    private $db_link;
	// Rate limit for 10 minutes
	const LONG_LIMIT_INTERVAL = 600;
	const RATE_LIMIT_LONG = 500;
	// Rate limit for 10 seconds'
	const SHORT_LIMIT_INTERVAL = 10;
	const RATE_LIMIT_SHORT = 10;
    
    private function db_execute($query)
    {
        if($this->db_link)
        {
            $res=mysqli_query($this->db_link,$query);
        }
        return $res;
    }
    
    private function execute($query)
    {   
        $this->updateLimitQueue($this->longLimitQueue, self::LONG_LIMIT_INTERVAL, self::RATE_LIMIT_LONG);
        $this->updateLimitQueue($this->shortLimitQueue, self::SHORT_LIMIT_INTERVAL, self::RATE_LIMIT_SHORT);
        
        $fgc=@file_get_contents($query);
        
        $resp= parseHeaders($http_response_header);
        
        if ($resp['reponse_code'] != '200'){
            throw new ResponseException($resp[0]);
        }
        return json_decode($fgc, true);
    }
    
    private function updateLimitQueue($queue, $interval, $call_limit){
		
		while(!$queue->isEmpty()){
			
			/* Three possibilities here.
			1: There are timestamps outside the window of the interval,
			which means that the requests associated with them were long
			enough ago that they can be removed from the queue.
			2: There have been more calls within the previous interval
			of time than are allowed by the rate limit, in which case
			the program blocks to ensure the rate limit isn't broken.
			3: There are openings in window, more requests are allowed,
			and the program continues.*/
			$timeSinceOldest = time() - $queue->bottom();
			// I recently learned that the "bottom" of the
			// queue is the beginning of the queue. Go figure.
			// Remove timestamps from the queue if they're older than
			// the length of the interval
			if($timeSinceOldest > $interval){
					$queue->dequeue();
			}
			
			// Check to see whether the rate limit would be broken; if so,
			// block for the appropriate amount of time
			elseif($queue->count() >= $call_limit){
				if($timeSinceOldest < $interval){ //order of ops matters
					echo("sleeping for".($interval - $timeSinceOldest + 1)." seconds\n");
					sleep($interval - $timeSinceOldest);
				}
			}
			// Otherwise, pass through and let the program continue.
			else {
				break;
			}
		}
		// Add current timestamp to back of queue; this represents
		// the current request.
		$queue->enqueue(time());
	}

    
    public function __construct($sName, $sServer, $sApi)
    {
        $this->summonerName = $sName;
        $this->server = $sServer;
        $this->apiKey = $sApi;
        $this->shortLimitQueue = new SplQueue();
		$this->longLimitQueue = new SplQueue();
        $this->db_link = mysqli_connect(MYSQL_SERVER,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB);
        if (!$this->db_link){
            echo "db_connect";
        }
             
        $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v1.4/summoner/by-name/%s?api_key=%s",
            $this->server, 
            $this->server, 
            $this->summonerName, 
            $this->apiKey);
            
        $result = $this->execute($query);
        
        
        $this->summonerId = $result[$this->summonerName]['id'];    
        
    }

   

    public function getMatchList()
    {
        $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/matchlist/by-summoner/%d?rankedQueues=TEAM_BUILDER_DRAFT_RANKED_5x5&seasons=SEASON2016&api_key=%s",
            $this->server,
            $this->server,
            $this->summonerId,
            $this->apiKey);
        $result= $this->execute($query);
        print("getMatchList(): ok! <br>");
        
        
        return $result;
        
        //["startIndex"] int
        //["endIndex"] int
        //["totalGames"] int
        //["matches"]:
        //       [
        //          {
        //             "timestamp"
        //             "champion"
        //             "region"
        //             "queue"
        //             "season"
        //             "matchId"
        //             "role"
        //             "platformId"
        //             "lane"
        //          }{}{}{}...
        //      ]
    }
    public function getMatchInfo($matchId)
    {
        $query = sprintf("https://%s.api.pvp.net/api/lol/%s/v2.2/match/%d?api_key=%s",
            $this->server, 
            $this->server, 
            $matchId, 
            $this->apiKey);
        
        
        
        $result = $this->execute($query);
        
        for ($i=$result["startIndex"]; $i<$result["endIndex"]; $i++){
            $this->db_execute(sprintf("INSERT INTO `summoners`.`match_list` (`match_id`,`match_data`) VALUES ('%s','%s')",$matchId,$result));
        }
        print("getMatchInfo(): ok! <br>");
        return $result;
    }
    
    public function getWinOrLose($match)
    {
        //$match = $this->getMatchInfo($matchId);
        $participantIdentities = $match["participantIdentities"];
        for ($i = 0; $i < 10; $i++) {
            if ($match["participantIdentities"][$i]["player"]["summonerId"] == $this->summonerId) 
            {
                $playerNumber = $participantIdentities[$i]["participantId"];
                //break;
                
            }
        }
        
        if ($playerNumber >= 1 && $playerNumber <= 5)
            $playerTeam = 0;
        else
            $playerTeam = 1;
            
        if ($match["teams"][$playerTeam]["winner"] == "true")
            return 1;
        else
            return 0;
    }
    
    
    function calculateStreak()
        {

            $matchList = $this->getMatchList();
    //echo $matches[0]['matchId'];$matchArray['endIndex']
            for ($i = 0; $i < $amount; $i++) {
        //echo "[" . time() . "] winorlose game " . $i . "start;<br>";
                $b[$i] = $this->getWinOrLose($matchList['matches'][$i]);
        //sleep(1.4);
        //echo "[" . time() . "] winorlose game " . $i . "end;<br>";
        //echo $b[$i] . "<br>";
    }
    //echo winorlose($summonerId,$server,$matches[11]['matchId'],$apiKey);
    //echo winorlose($summonerId,$server,$matches[0]['matchId'],$apiKey);
    return maxStreak($b);
        }

}
;


?>