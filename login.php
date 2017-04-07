<?php
/**
 * @Author: KathyF
 * @Date:   2017-04-07 17:03:04
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 17:54:52
 */


//session_start();
$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='library';    //使用的数据库

$user=$_POST['username'];      //数据库连接用户名
$pass=$_POST['password'];          //对应的密码
session_start();
$_SESSION['username']=$user;      //数据库连接用户名
$_SESSION['password']=$pass;
$dsn="$dbms:host=$host;dbname=$dbName";
try
{
	$dbh = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true));
	$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER);

	echo '<script language="javascript">';
	echo 'document.location="library.html"';
	echo '</script>';
} catch (PDOException $e) {
    die ('<html lang="en">
         	<head>
	         	<link rel="stylesheet"href="https://cdn.bootcss.com/bootstrap/3.3.1/css/bootstrap.min.css">
	    		<meta charset="utf-8">
	    		<style>
					@import url("page.css")
				</style>
 			</head>
			<body>
			<div class="container">
		    	<div class="row clearfix">
		    		<div class="col-xs-12 column">
		    			<div class="jumbotron">
		    				<div id="background">
		    				<h1 class="text-center text-primary">图书管理系统</h1><br><h2>用户名或密码错误<br></h2>
							<a href="login.html"><button type="button" class="btn btn-danger">返回</button></a>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
			</div>
			<footer class="text-center">
				<hr>
				<p>© Coded by <a href="https://github.com/KathyFlores" target="_blank">王淼</p>
			</footer>
			</body>
		</html>');
}



?>
