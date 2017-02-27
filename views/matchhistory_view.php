<table border="1">

    <tr><td>id</td></tr>
<tr>
    <td></td><td>1team</td>
    <td></td><td>2team</td>
</tr>
<?php
for($i=0;$i<$amount;$i++)
{
    $matchId =  $matchArray['matches'][$i]['matchId'];
    $match = $summoner->getMatchInfo($matchId);
    $wl= $summoner->getWinOrLose($match);
    $b[$i] = $wl;
     
    if ($wl==0)
        $color="red";
    else
        $color="green";
    
    echo "<tr><td>".$match['matchId']."</td><td bgcolor=".$color."></td></tr>";
    for ($j=0; $j<5; $j++)
    {       
        echo "<tr>";
            echo "<td></td><td>".$match["participantIdentities"][$j]["player"]["summonerName"]."</td>";
            echo "<td></td><td>".$match["participantIdentities"][$j+5]["player"]["summonerName"]."</td>";
        echo "</tr>";
    }
    
}
    echo "maxstreak=".maxStreak($b);



?>

</table>