<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.1/css/bootstrap.min.css">

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
 * @Date:   2017-04-05 11:48:20
 * @Last Modified by:   KathyF
 * @Last Modified time: 2017-04-07 23:15:55
 */
header("content-Type: text/html; charset=Utf-8");

include dirname(__FILE__).'\select.php';
$action=$_POST["submit"];
switch($action)
{
case "单本入库":
    $bno=$_POST["bno"];
    $category=$_POST["category"];
    $title=$_POST["title"];
    $press=$_POST["press"];
    $year=$_POST["year"];
    $author=$_POST["author"];
    $price=$_POST["price"];
    $num=$_POST["num"];
    insert_into_book($bno,$category,$title,$press,$year,$author,$price,$num);
    echo '</p><a href="insert_one.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
    break;
case "批量入库":
    if($_FILES['file']['error']>0)
    {
        echo 'Error:'.$_FILES['file']['error'].'<br>';
    }
    else
    {
        // echo 'Upload: ' . $_FILES['file']['name'] . '<br>';
        // echo 'Type: ' . $_FILES['file']['type'] . '<br>';
        // echo 'Size: '. ($_FILES['file']['size'] / 1024).' Kb<br>';
        // echo 'Stored in: '. $_FILES['file']['tmp_name'].'<br>';
        $filename ="upload/" . $_FILES["file"]["name"];
        if (file_exists($filename))
            unlink($filename);
        if(is_uploaded_file($_FILES['file']['tmp_name']))
        {
            $stored_path = dirname(__FILE__).'/'.'upload/'.$_FILES['file']['name'];
        }
        move_uploaded_file($_FILES['file']['tmp_name'],$stored_path);
        //echo $stored_path.'<br>';
    }
    echo '</p><a href="insert_several.html"><button type="button" class="btn btn-primary">返回上页</button></a><br><br><p>';
    insertions_book($stored_path);
    break;
}
function insert_into_book($bno,$category,$title,$press,$year,$author,$price,$num)
{
	global $dbh;
    $stmt="SELECT total,stock FROM book WHERE bno = '".$bno."'";
    $result = $dbh->query($stmt);

    if(empty($result->fetch()))//系统里没有这本书
    {
        $stmt = "INSERT INTO book (bno, category, title, press, year, author, price, total, stock) VALUES ('".$bno."','".$category."','".$title."','".$press."',".$year.",'".$author."',".$price.",".$num.",".$num.")";
        //echo $stmt;
        if(!empty($dbh->exec($stmt)))
            echo '入库成功，请核对信息：<br>';
        else
            echo '入库失败，请核对信息：<br>';
        echo '书号： '.$bno.'<br>';
        echo '类别： '.$category.'<br>';
        echo '书名： '.$title.'<br>';
        echo '出版社： '.$press.'<br>';
        echo '出版时间： '.$year.'<br>';
        echo '作者： '.$author.'<br>';
        echo '价格： '.$price.'<br>';
        echo '入库数量： '.$num.'<br><br>';

    }

    else//已经有了这本书，修改
    {
        echo "系统中已有书号为".$bno."的书目，相关信息已更新，请核对!<br>";
        echo '</p><div class="row"><div class="col-xs-6"><p>更新前：<br>';
        select_book($bno,null,null,null,null,null,null,null,null);
        $stmt="UPDATE book SET category = '".$category."',title='".$title."',press='".$press."',year=".$year.",author='".$author."',price=".$price.",total=total+".$num.", stock =stock+".$num." WHERE bno = '".$bno."'";
        //echo $stmt;
        $dbh->exec($stmt);
        echo '</p></div><div class="col-xs-6"><p>入库 '.$num.'本后：<br>';
        select_book($bno,null,null,null,null,null,null,null,null);
        echo '</p></div></div><p>';
    }
}

function insertions_book($filename)
{
    global $dbh;
    $contents= file_get_contents($filename,0,NULL,1);
    $countline=count(file($filename));
    echo '入库'.$countline.'条记录<br><br>';
    $data=trim($contents,chr(0xEF).chr(0xBB).chr(0xBF));
    //echo chr(0xEF).chr(0xBB).chr(0xBF);
    //echo $data;
    $data=strtr($data,"\n"," ");
    $info=explode(" ",$data);
    //print_r($info);
    for($i=0;$i<$countline;$i++)
    {
        $bno=$info[0+$i*8];
        $category=$info[1+$i*8];
        $title=$info[2+$i*8];
        $press=$info[3+$i*8];
        $year=$info[4+$i*8];
        $author=$info[5+$i*8];
        $price=$info[6+$i*8];
        $num=$info[7+$i*8];
        insert_into_book($bno,$category,$title,$press,$year,$author,$price,$num);
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

