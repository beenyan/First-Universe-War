<?php
	$db=mysqli_connect("localhost","root","","shootgame");
	mysqli_query($db,"SET NAMES UTF8");
	$i0=$_POST["name"];
	$i1=$_POST["con"];
	$i2=$_POST["time"];
	mysqli_query($db,"INSERT INTO `01`(`name`, `con`,`time`) VALUES ('$i0','$i1','$i2')");
?>