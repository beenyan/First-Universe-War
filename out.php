<?php
	$db=mysqli_connect("localhost","root","","shootgame");
	mysqli_query($db,"SET NAMES UTF8");
	$b=mysqli_fetch_array(mysqli_query($db,"SELECT * FROM `01` ORDER BY `01`.`id` DESC"));
	$all=mysqli_query($db,"SELECT * FROM `01` ORDER BY `01`.`con` DESC, `01`.`time` DESC, `01`.`id` ASC");
	$i=1;
	$best=$b[0];
	$nice="false";
	$re="true";
	while ($row=mysqli_fetch_array($all)){
		if ($i<=3){
			echo "$i,$row[1],$row[2],$row[3]:";
			if ($row[0]==$best){
				$nice="$i";
				$re="false";
			}
		}
		if ($re=="true"&&$row[0]==$best){
			echo "$i,$row[1],$row[2],$row[3]:";
			$re="false";
		}
		$i++;
	}
	echo $nice;
?>