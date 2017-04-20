<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<!-- bootstrap -->
	<title>图书管理系统-借书证管理</title>
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
 * @Date:   2017-04-06 12:46:23
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-20 09:27:24
 */
include dirname(__FILE__).'\dbms_connect.php';
$action=$_POST["submit"];
$cno=$_POST["cno"];
switch($action)//获取表单数据
{
case "添加":
	if(empty($_POST["type"])||empty($_POST["name"])||empty($_POST["department"]))
		echo "请输入完整的信息！<br>";
	else
		insert_into_card($cno,$_POST["name"],$_POST["department"],$_POST["type"]);
	break;
case "修改":
	$type=(empty($_POST["type"]))?null:$_POST["type"];
	$department=(empty($_POST["department"]))?null:$_POST["department"];
	$name=(empty($_POST["name"]))?null:$_POST["name"];
	alter_card($cno,$name,$department,$type);
	break;
case "删除":
	remove_card($cno);
	break;
}
echo '</p><a href="card_manage.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
function insert_into_card($cno,$name,$department,$type)
{
	global $dbh;
	$stmt="SELECT cno FROM card WHERE cno = '".$cno."'";
	$result = $dbh->query($stmt);
    if(empty($result->fetch()))//系统里没有这个卡号
	{
		$stmt = "INSERT INTO card (cno, name, department, type) VALUES ('". $cno."','".$name."','".$department."','".$type."')";
		//echo $dbh->query($stmt);
		if(!empty($dbh->exec($stmt)))
		{
			echo "成功为".$department."学院的".($type=='T'?"老师":"学生").$name."添加借书证，借书证号为：".$cno;
		}
		else
			echo "借书证添加失败！";
	}
	else
	{
		echo "系统中已有该借书证号!如要更改信息请点击修改";
	}

}
function alter_card($cno,$name,$department,$type)
{
	global $dbh;
	$flag=0;
	$stmt="SELECT * FROM card where cno = '".$cno."'";
	$result = $dbh->query($stmt);
	if(empty($result->fetch()))
		echo "未查询到该借书证号！请检查您的输入！";
	else
	{
		$result = $dbh->query($stmt);
		$result=$result->fetchAll(PDO::FETCH_ASSOC);
		$name_old=$result[0]['NAME'];
		$department_old=$result[0]['DEPARTMENT'];
		$type_old=$result[0]['TYPE'];
		if(!empty($name)&&$name!=$name_old)
		{
			$stmt = "UPDATE card set name = '".$name."' WHERE cno = '".$cno."'";
			if(!empty($dbh->exec($stmt)))
				$flag=1;
		}
		if(!empty($department)&&$department!=$department_old)
		{
			$stmt = "UPDATE card set department = '".$department."' WHERE cno = '".$cno."'";
			if(!empty($dbh->exec($stmt)))
				$flag=1;
		}
		if(!empty($type)&&$type!=$type_old)
		{
			$stmt = "UPDATE card set type = '".$type."' WHERE cno = '".$cno."'";
			if(!empty($dbh->exec($stmt)))
				$flag=1;

		}
		if($flag==1)
		{
			echo "借书证号为".$cno."的信息修改成功！<br>";
			echo "修改后：<br>";
			echo "姓名： ".$name;
			echo "<br>学院： ".$department;
			echo "<br>类型： ".($type=='T'?"教师卡":"学生卡");
		}
		else
			echo "没有输入任何修改信息!";

	}
}
function remove_card($cno)
{
	global $dbh;
	$stmt="SELECT * FROM card where cno = '".$cno."'";
	$result = $dbh->query($stmt);
	if(empty($result->fetch()))
		echo "未查询到该借书证号！请检查您的输入！";
	else
	{
		$stmt="SELECT * FROM borrow where cno = '".$cno."'and done = 0";
		$result = $dbh->query($stmt);
		if(!empty($result->fetch()))//删除时借书证有书未归还
		{

			echo "无法删除该借书证！<br>仍有以下书目尚未归还：<br>";
			$stmt="SELECT * FROM borrow where cno = '".$cno."'and done = 0";
			$result = $dbh->query($stmt);
			$result->setFetchMode(PDO::FETCH_ASSOC);
			foreach ($result as $row)
				echo "书号：".$row['BNO']."<br>";
		}
		else
		{
			$stmt="DELETE FROM card WHERE cno = '".$cno."'";
			$dbh->exec($stmt);
			echo "已删除借书证号为 ".$cno." 的借书证信息及其所有借书记录！";
		}

	}
}
?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<footer class="text-center">
		<hr>
		© Coded by <a href="https://github.com/KathyFlores" target="_blank">王淼</a>
	</footer>
  </body>
</html>

