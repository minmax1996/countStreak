
<div class="container">
    <form method="get" action="index.php">
        <label>SummonerName<input type="text" name="summonerName" value="" class="form-item" autofocus required> </label> 
        
        <label><select size="1" name="server">
        <option value="eune">eune</option>
        <option value="euw">euw</option>
        <option value="na">na</option>
        <option value="ru">ru</option>
        </select>
        </label> 
        
        <label>amount<input type="text" name="amount" value="20" class="form-item" autofocus required> </label>   
        
        <input type="submit" name="result_submit"        value="search" class="btn" />
        <input type="submit" name="matchhistory_submit"  value="matchhistory" class="btn" />
    </form>

</div>
