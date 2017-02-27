<?php
define('MYSQL_SERVER','localhost');
define('MYSQL_USER','mysql');
define('MYSQL_PASSWORD','admin123');
define('MYSQL_DB','summoners');

class DataBase{
    private $link;
    
    
    public function db_connect()
    {
        $l = mysqli_connect(MYSQL_SERVER,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB)
            or die("ERROR: ".mysql_error($l));
        if (!mysqli_set_charset($l,"utf8"))
        {
            printf("error: ".mysql_error($l));
        }
        $link = $l;
        return $this;
    }
    public function db_create()
    {
        //$query="CREATE TABLE data(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,summoner_id INT NOT NULL,summoner_name VARCHAR(255) , data BLOB)";
        //$l=mysqli_query($this->link,$query);
        //return $this;
        if (mysqli_query($link, "CREATE TABLE data(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,summoner_id INT NOT NULL,summoner_name VARCHAR(255) , data BLOB)") === TRUE) {
             printf("Таблица myCity успешно создана.\n");
        }
    }
    
    public function db_alter($sumId,$sumName,$d)
    {
        $query="ALTER INTO data(summoner_id,summoner_nam, data) VALUES"+$sumId+$sumName+$d;
        if(!($l=mysqli_query($link,$query))){
            echo "alter table error";
        }
        return $this;
    }

}

?>