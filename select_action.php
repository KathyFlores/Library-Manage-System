<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<!-- bootstrap -->
    <meta charset="utf-8">
    <style>
		body {
			margin-top: 60px;
			text-align:center;
			background-color: #F5F5F5;
		}
		p {
			font-size: 20px;
		}
		</style>
  </head>
  <body>
	<div class="container">
	    <div class="row clearfix">
	    	<div class="col-xs-12 column">
	    		<div class="jumbotron">
	    			<h1 class="text-center text-primary">图书管理系统</h1><br>
					<p>
<?php
/**
 * @Author: KathyF
 * @Date:   2017-04-06 12:19:16
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 19:58:31
 */
include dirname(__FILE__).'\select.php';

$bno=$_POST["bno"];
$category=$_POST["category"];
$title=$_POST["title"];
$press=$_POST["press"];
$year_l=$_POST["year_l"];
$year_r=$_POST["year_r"];
$price_l=$_POST["price_l"];
$price_r=$_POST["price_r"];
$author=$_POST["author"];
echo '</p><a href="select.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
select_book($bno,$category,$title,$press,$year_l,$year_r,$price_l,$price_r,$author);
?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<footer class="text-center">
		<hr>
		© Coded by <a href="https://github.com/KathyFlores" target="_blank">KathyFlores</a>
	</footer>
  </body>
</html>
