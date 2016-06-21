<?php
define('MYSQL_SERVER','localhost');
define('MYSQL_USER','mysql');
define('MYSQL_PASSWORD','admin123');
define('MYSQL_DB','summoners');

function db_connect()
{
    $link = mysqli_connect(MYSQL_SERVER,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB)
        or die("ERROR: ".mysql_error($link));
    if (!mysqli_set_charset($link,"utf8"))
    {
        printf("error: ".mysql_error($link));
    }
    
    return $link;
}

?>