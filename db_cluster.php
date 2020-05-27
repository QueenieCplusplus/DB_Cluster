Login	 	Register	 
PHP Classes  
File: example.php

 	Search  送出	 	All class groups All class groups	 	Latest entries Latest entries	 	Top 10 charts Top 10 charts	 	Blog Blog	 	Forums Forums	 	Help FAQ Help FAQ	 
Recommend this page to a friend!	
	
 	Classes of Mihails Atamanskis	 > 	PHP MySQL Cluster Connection	 > 	example.php	 > 	Download	 
File:	example.php
Role:	Example script
Content type:	text/plain
Description:	Simple example
Class:	PHP MySQL Cluster Connection
Access MySQL clusters using separate connections
Author:	By Mihails Atamanskis
Last change:	small fix
Date:	4 years ago
Size:	1,784 bytes
 

Advertisement
VDO.AI
 


 
Contents

Class file image Download
<?php 

$databases = array(); 

$i=0; 
// Primary database 

$databases[$i]['number'] = $i; //server in cluster unique number 
$databases[$i]['role'] = 'read'; //server role: read/write 
$databases[$i]['db_host'] = '10.0.0.1'; //hostname (ip or domain) 
$databases[$i]['db_name'] = 'database'; //database name 
$databases[$i]['db_user'] = 'user'; //database user 
$databases[$i]['db_pass'] = 'password'; //database password 
$databases[$i]['error_email'] = 'my@email.com'; //database error report email 

//Second database 
$i++; 
$databases[$i]['number'] = $i; //server in cluster unique number 
$databases[$i]['role'] = 'write'; //server role: read/write . If only one server in cluster then it use for read & write. if 
$databases[$i]['db_host'] = '10.0.0.2'; //hostname (ip or domain) 
$databases[$i]['db_name'] = 'database'; //database name 
$databases[$i]['db_user'] = 'user'; //database user 
$databases[$i]['db_pass'] = 'password'; //database password 
$databases[$i]['error_email'] = 'my@email.com'; //database error report email 

require_once 'mysql_cluster.php'; 

$db = new db_cluster($databases); 

# Uncoment if need test shutdown one of server 
//echo "sleep start\n"; 
//sleep(70); #mysql timeout set to 60 seconds, we must get error and select other mysql server 
//echo "sleep stop\n"; 

//Example 1 
$sql_results = $db->query("Select * from users LIMIT 10"); 
var_dump($db->get_row($sql_results)); 

//Multy rows example 
$sql_results = $db->query("Select * from users LIMIT 10"); 
if ($db->num_rows($sql_results) > 0) { 
    while ($row = $db->get_row($sql_results)){ 
        //Some your code 
    } 
} 


 
 	Advertise on this site Advertise On This Site	 	Site map Site Map	 	Newsletter Newsletter	 	Statistics Statistics	 	Site tips Site Tips	 	Privacy policy Privacy Policy	 	Contact Contact	 
Icontem
Copyright (c) Icontem 1999-2020
For more information send a message to info at phpclasses dot org.
