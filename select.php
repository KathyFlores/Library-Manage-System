<?php

/**
 * @Author: KathyF
 * @Date:   2017-04-05 16:54:39
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 19:25:31
 */

include dirname(__FILE__).'\dbms_connect.php';

function select_book($bno,$category,$title,$press,$year_l,$year_r,$price_l,$price_r,$author)
{
	global $dbh;
	$stmt="SELECT * FROM book WHERE ";
	if($bno)
		$stmt.="bno = "."'".$bno."' and ";
	else
		$stmt.="bno LIKE '%' and ";
	//selcet category:
	if($category)
		$stmt.="category = "."'".$category."' and ";
	//select title:
	if($title)
		$stmt.="title = "."'".$title."' and ";
	//select press:
	if($press)
		$stmt.="press = "."'".$press."' and ";
	//select year:
	if($year_l)
		$stmt.="year >= ".$year_l." and ";
	if($year_r)
		$stmt.="year <= ".$year_r." and ";
	//select price:
	if($price_l)
		$stmt.="price >= ".$price_l." and ";
	if($price_r)
		$stmt.="price <= ".$price_r." and ";
	//select author:
	if($author)
		$stmt.="author = "."'".$author."'";
	else
		$stmt.="author LIKE '%'";
	$stmt.=' ORDER BY bno ASC';
	//echo $stmt;
	$result = $dbh->query($stmt);
	$result->setFetchMode(PDO::FETCH_ASSOC);
	foreach ($result as $row)
	{
		//print_r($row);
		//$stmt="SELECT * FROM book where bno='".$bno."'";

		echo '书号： '.$row['BNO'].'<br>';
		echo '类别： '.$row['CATEGORY'].'<br>';
		echo '书名： '.$row['TITLE'].'<br>';
		echo '出版社： '.$row['PRESS'].'<br>';
		echo '出版时间： '.$row['YEAR'].'<br>';
		echo '作者： '.$row['AUTHOR'].'<br>';
		echo '价格： '.$row['PRICE'].'<br>';
		echo '总藏书量： '.$row['TOTAL'].'<br>';
		echo '库存： '.$row['STOCK'].'<br><br>';
	}
}


?>
