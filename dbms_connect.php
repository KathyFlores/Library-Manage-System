<?php

/**
 * @Author: KathyF
 * @Date:   2017-04-05 11:46:23
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 17:52:59
 */

$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='library';    //使用的数据库

session_start();
$user=$_SESSION['username'];      //数据库连接用户名
$pass=$_SESSION['password'];          //对应的密码

$dsn="$dbms:host=$host;dbname=$dbName";
try
{
	$dbh = new PDO($dsn, $user, $pass);//, array(PDO::ATTR_PERSISTENT => true));
	$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER);
} catch (PDOException $e) {
    print '错误: '.$e->getMessage().'<br>';
    print '行号: '.$e->getLine().'<br>';
    die();
}

?>
