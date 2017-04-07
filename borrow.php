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
 * @Date:   2017-04-05 19:26:15
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 17:44:59
 */

include dirname(__FILE__).'\dbms_connect.php';


$action=$_POST["submit"];
switch($action)
{
case "借书":
	$cno=$_POST["cno"];
	$bno=$_POST["bno"];
	borrow_book($cno,$bno);
	echo '</p><a href="borrow.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
	break;
case "还书":
	$cno=$_POST["cno"];
	$bno=$_POST["bno"];
	return_book($cno,$bno);
	$bno=$_POST["bno"];
	echo '</p><a href="borrow.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
	break;
case "查询借书记录":
	$cno=$_POST["cno"];
	record_query($cno);
	echo '</p><a href="record_query.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
	break;
}

function record_query($cno)
{
	global $dbh,$cno;


	$stmt="SELECT bno,category,title,press,year,author,price,total,stock,borrow_date,return_date,due_date,done FROM book NATURAL JOIN borrow WHERE cno = '".$cno."'";
	//echo $stmt;
	$result = $dbh->query($stmt);
	if(empty($result->fetch()))//未查询到记录
	{
		echo "抱歉！没有查询到借书证号为".$cno."的借书记录呢，请检查借书证号是否正确~";
	}
	else
	{
		$stmt="SELECT bno,category,title,press,year,author,price,total,stock,borrow_date,return_date,due_date,done FROM book NATURAL JOIN borrow WHERE cno = '".$cno."'";
		//echo $stmt;
		$result = $dbh->query($stmt);
		echo "查询到借书证号为".$cno."的借书记录如下：<br><br>";
		foreach ($result as $row)
		{

			echo '书号： '.$row['BNO'].'<br>';
			echo '类别： '.$row['CATEGORY'].'<br>';
			echo '书名： '.$row['TITLE'].'<br>';
			echo '出版社： '.$row['PRESS'].'<br>';
			echo '出版时间： '.$row['YEAR'].'<br>';
			echo '作者： '.$row['AUTHOR'].'<br>';
			echo '价格： '.$row['PRICE'].'<br>';
			echo '总藏书量： '.$row['TOTAL'].'<br>';
			echo '库存： '.$row['STOCK'].'<br>';

			echo '是否已还？ '.($row['DONE']?'是':'否').'<br>';
			echo '应还时间： '.$row['DUE_DATE'].'<br>';
			echo '借出时间： '.$row['BORROW_DATE'].'<br>';
			echo '归还时间： '.$row['RETURN_DATE'].'<br><br>';
		}
	}

}
function borrow_book($cno,$bno)
{

	global $dbh;

	//查询该书库存：
	$stmt="SELECT stock,title FROM book WHERE bno = '".$bno."'";
	$result = $dbh->query($stmt);
	$result=$result->fetch(PDO::FETCH_ASSOC);
	$stmt="SELECT cno FROM card WHERE cno = '".$cno."'";
	$result1 = $dbh->query($stmt);
	$result1=$result1->fetch(PDO::FETCH_ASSOC);
	if(!empty($result)&&!empty($result1))//有借书证且有这本书
	{
		$stock = $result['STOCK'];
		$title = $result['TITLE'];
		if ($stock <= 0)//库存为空
		{
			echo "书目：".$title." 库存为空，借出失败！<br>";

			$stmt="SELECT max(due_date) as latest_due_date FROM borrow where bno = '".$bno."'";
			$result = $dbh->query($stmt);
			$result=$result->fetchAll(PDO::FETCH_ASSOC);

			echo "它将在: ".$result[0]['LATEST_DUE_DATE']." 之前被归还。<br>";
		}
		else//库存大于0
		{
			//查询该卡是否已经借了这本书并且还没还
			$stmt="SELECT borrow_date FROM borrow WHERE bno = '".$bno."' and cno = '".$cno."' and done = 0";
			$result = $dbh->query($stmt);
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$borrow_d = $result->fetch()['BORROW_DATE'];
			//$return_d = $fetch[0]['RETURN_DATE'];
			if($borrow_d)//还没还
				echo "已经借了这本书并且尚未归还！<br>";
			else
			{
				try {
					date_default_timezone_set('PRC');
		  			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		  			//开始一个事务：插入借书记录，并将库存减一
		  			$dbh->beginTransaction();
		  			$stock--;
					$stmt="INSERT INTO borrow (cno, bno, borrow_date, return_date) VALUES ('".$cno."','".$bno."','".date("Y-m-d H:i:s")."',null)";
		  			$dbh->exec($stmt);
		  			$stmt="UPDATE borrow SET due_date = DATE_ADD(borrow_date,INTERVAL 45 DAY)";//借书期限45天
		  			$result = $dbh->exec($stmt);
		  			$stmt="UPDATE book SET stock = ".$stock." WHERE bno = '".$bno."'";
		  			$result = $dbh->exec($stmt);
		  			echo '借书成功！<br>';
		  			//提交事务
		  			$dbh->commit();
				} catch (Exception $e) {
		  			$dbh->rollBack();//回滚
		  			echo 'Failed: ' . $e->getMessage().'<br>';
				}
			}
			//打印借书后库存，调试用
			$stmt="SELECT title, stock FROM book WHERE bno = '".$bno."'";
			$result = $dbh->query($stmt);
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			echo $bno.":".$result[0]['TITLE']." 的现有库存:".$result[0]['STOCK'].'<br>';
		}
	}
	else
	{
		echo "请核对借书证号与书号！";
	}

}
//borrow_book('3104250',null);

//borrow_book('3104250','AZ01982401');
function return_book($cno,$bno)
{

	global $dbh;

	//查询该书库存：
	$stmt="SELECT stock FROM book WHERE bno = '".$bno."'";
	$result = $dbh->query($stmt);
	$result=$result->fetch(PDO::FETCH_ASSOC);
	$stock = $result['STOCK'];
	$stmt="SELECT cno FROM card WHERE cno = '".$cno."'";
	$result1 = $dbh->query($stmt);
	$result1=$result1->fetch(PDO::FETCH_ASSOC);
	if(!empty($result)&&!empty($result1))//借书证号和书号正确
	{
		//找出对应的借书记录
		$stmt="SELECT borrow_date FROM borrow WHERE bno = '".$bno."' and cno = '".$cno."' and done = 0";
		$result = $dbh->query($stmt);
		$result->setFetchMode(PDO::FETCH_ASSOC);

		$borrow_d = $result->fetch()['BORROW_DATE'];

		if ($borrow_d)//找到了对应的借书记录
		{
			try {
				date_default_timezone_set('PRC');
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				//开始事务：更新还书时间，将done置1，库存加一
				$dbh->beginTransaction();
				$stock++;
				$stmt="UPDATE borrow SET return_date = "."'".date("Y-m-d H:i:s")."', done = 1"." WHERE cno = '".$cno."' and bno = '".$bno."' and borrow_date = '".$borrow_d."'";
				$dbh->exec($stmt);
				$stmt="UPDATE book SET stock = ".$stock." WHERE bno = "."'".$bno."'";
				$result = $dbh->exec($stmt);
				echo '还书成功！<br>';
				//提交事务
				$dbh->commit();
			} catch (Exception $e) {
				$dbh->rollBack();
				echo "Failed: " . $e->getMessage().'<br>';
			}

		}
		else//没找到借书记录
		{
			echo "抱歉！没找到符合条件的记录呢~请检查借书证号与书号是否正确！<br>";
		}
		//打印还书以后的库存，调试用
		$stmt="SELECT title, stock FROM book WHERE bno = '".$bno."'";
		$result = $dbh->query($stmt);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);

		echo $bno.":".$result[0]['TITLE']." 的现有库存:".$result[0]['STOCK'].'<br>';
	}
	else
	{
		echo "请核对借书证号与书号！";
	}

}
//return_book('3104250','AZ01982401');
?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<footer class="text-center">
		<hr>
		<p>© Coded by <a href="https://github.com/KathyFlores" target="_blank">王淼</a></p>
	</footer>
  </body>
</html>
