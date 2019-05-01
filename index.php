<?php
	$db=mysqli_connect("localhost","root","","shootgame");
	mysqli_query($db,"SET NAMES UTF8");
	$all=mysqli_query($db,"SELECT * FROM `01` ORDER BY `01`.`con` DESC, `01`.`time` DESC, `01`.`id` ASC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>外太空射擊遊戲</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	$(function(){
		var st;
		var tmp;
		var start="false";
		var timeup;
		var timedown;
		var timeright;
		var timeleft
		var $find;
		var $findgun;
		var $overfind;
		var enemygunspeed=3;//敵人子彈速度
		var gunspped=8; 	//子彈速度
		var sumbackp=300; 	//生成隕石機率sumbackp分之1
		var score=10; 		//擊破隕石得分數
		var FPS=60;
		var gashave=15;		//初始燃料
		var backattack=15;	//撞到隕石損失的燃料
		var sump=400;		//燃料生成機率sump/1s
		var gasspeed=1;		//汽油下降速度
		var stop="true";	//檢查遊戲是否暫停
		var enemyspeed=2; 	//敵人戰艦移動速度
		var sumenemyp=500;	//生成敵人機率sumenemyp/(1000/FPS)s
		var shootgunp=500;	//敵人射擊機率
		var addtime=0;		//每5秒增強敵人
		var mycon;			//分數
		var flyalltime=0;	//飛行時間
		//成績
		var line0;
		var line1;
		var line2;
		var line3;
		var fin;
		//循環時間變數;
		var gunflytime;
		var backtime;
		var movebacktime="";
		var gastime;
		var sumgastime;
		var gasdowntime;
		var summonenemytime;
		var moveenemytime;
		var enemygun;
		var movegun;
		var addenemytime;
		var flytime;
		//移動速度
		var upspeed=4;
		var downspeed=4;
		var leftspeed=6;
		var rightspeed=6;
		//隕石速度
		var back1speed=1.5;
		var back2speed=2.5;
		var back3speed=1.0;
		var back4speed=0.5;
		var back5speed=2;
		$player=$("#player");	//玩家
		$stage=$(".stage");		//背景
		$mygun=$(".mygun");		//子彈
		$icon=$(".icon");		//記分板
		$gas=$(".gas");			//燃料
		//開啟計時器
		function starttime(){
			stop="false";
			start="true";
			gastime=setInterval(gas,1000)				//1.消耗燃料
			st=setInterval(yn,1000/FPS)						//2.啟用判斷
			sumgastime=setInterval(sumgas,1000/FPS)			//3.生成燃料
			gasdowntime=setInterval(gasdown,1000/FPS)		//4.燃料下降
			backtime=setInterval(summonback,1000/FPS)		//5.生成隕石
			movebacktime=setInterval(moveback,1000/FPS)		//6.隕石移動
			gunflytime=setInterval(gunflying,1000/FPS)		//7.子彈移動
			summonenemytime=setInterval(sumenemy,1000/FPS)	//8.生成敵人
			moveenemytime=setInterval(moveenemy,1000/FPS)	//9.敵人移動
			enemygun=setInterval(enemyshoot,1000/FPS)		//10.敵人射擊
			movegun=setInterval(gunmove,1000/FPS)			//11.敵人子彈移動
			addenemytime=setInterval(addpwoer,1000/FPS)		//12.強化敵人
			$(".bg").css("animation-duration","40s")		//13.背景移動
			flytime=setInterval(fly,1000)					//計算飛行時間
		}
		function text(){
			stop="false";
			start="true";
			st=setInterval(yn,1000/FPS)
			gunflytime=setInterval(gunflying,1000/FPS)
			enemygun=setInterval(enemyshoot,1000/FPS)
			moveenemytime=setInterval(moveenemy,1000/FPS)
			movegun=setInterval(gunmove,1000/FPS)
		}
		//關閉計時器
		function offtime(){
			stop="true";
			start="false";
			clearInterval(gastime);			
			clearInterval(sumgastime);		
			clearInterval(gasdowntime);
			clearInterval(backtime);
			clearInterval(movebacktime);
			clearInterval(gunflytime);
			clearInterval(st);				
			clearInterval(summonenemytime);
			clearInterval(moveenemytime);
			clearInterval(enemygun);
			clearInterval(movegun);	
			clearInterval(addenemytime);
			$(".bg").css("animation-duration","8000000s")
			clearInterval(flytime);
		}
		//起始設定
		$player.css({
			top:(parseFloat($stage.css("height"))-parseFloat($player.css("height")))/2
		})
		$icon.css({
			left:(parseFloat($stage.css("width"))-parseFloat($icon.css("width")))/2
		})
		$gas.html(gashave)
		$gas.css({
			left:(parseFloat($stage.css("width"))-parseFloat($gas.css("width"))),
			top:10,
			'background-image':"url(gas/gas_"+gashave+".png)",
		})
		//移動隕石
		function moveback(){
			$stage.find(".back1").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-back1speed)
				//刪除隕石
				if ((parseInt($(this).css("left"))<-320)){
					$(this).remove();
				}
			})
			$stage.find(".back2").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-back2speed)
				//刪除隕石
				if ((parseInt($(this).css("left"))<-320)){
					$(this).remove();
				}
			})
			$stage.find(".back3").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-back3speed)
				//刪除隕石
				if ((parseInt($(this).css("left"))<-320)){
					$(this).remove();
				}
			})
			$stage.find(".back4").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-back4speed)
				//刪除隕石
				if ((parseInt($(this).css("left"))<-320)){
					$(this).remove();
				}
			})
			$stage.find(".back5").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-back5speed)
				//刪除隕石
				if ((parseInt($(this).css("left"))<-320)){
					$(this).remove();
				}
			})
		}
		//隨機生成隕石
		function summonback(){
			if (rand(0,sumbackp)==0){
				var list=["<div class='back1 back'>2</div>","<div class='back2  back'>2</div>","<div class='back3  back'>2</div>","<div class='back4  back'>2</div>","<div class='back5  back'>2</div>"];
				var temp=rand(0,4)
				$stage.append(list[temp])
				if (temp==0){
					$find=$stage.find(".back1:last")
					$find.css({
						left:parseInt($stage.css("width")),
						top:rand(-300,600),
					})
				}
				else if (temp==1){
					$find=$stage.find(".back2:last")
					$find.css({
						left:parseInt($stage.css("width")),
						top:rand(-300,600),
					})
				}
				else if (temp==2){
					$find=$stage.find(".back3:last")
					$find.css({
						left:parseInt($stage.css("width")),
						top:rand(-300,600),
					})
				}
				else if (temp==3){
					$find=$stage.find(".back4:last")
					$find.css({
						left:parseInt($stage.css("width")),
						top:rand(-300,600),
					})
				}
				else if (temp==4){
					$find=$stage.find(".back5:last")
					$find.css({
						left:parseInt($stage.css("width")),
						top:rand(-300,600),
					})
				}
			}
		}
		//隨機生成燃料
		function sumgas(){
			if (rand(0,sump)==0){
				$stage.append("<div class='gascon'></div>")
				$find=$stage.find(".gascon:last")
				$find.css({
					top:0-parseInt($find.css("height")),
					left:rand(0,600),
				})
			}
		}
		//燃料下降(刪除)
		function gasdown(){
			$stage.find(".gascon").each(function(){
				$(this).css({
					top:parseFloat($(this).css("top"))+gasspeed,
				})
				if (parseFloat($(this).css("top"))>600){
					$(this).remove();	
				}
			})
		}
		//敵人發射子彈
		function enemyshoot(){
			$stage.find(".allenemy").each(function(){
				if (rand(0,shootgunp)==0){
					$stage.append("<div class='enemygun'></div>")
					$find=$stage.find(".enemygun:last")
					$find.css({
						top:parseFloat($(this).css("top"))+parseFloat($(this).css("height"))/2,
						left:parseFloat($(this).css("left")),
					})
				}
				
			})
		}
		//敵人子彈移動
		function gunmove(){
			$stage.find(".enemygun").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-enemygunspeed)
					//刪除子彈(超出螢幕)
					if (parseFloat($(this).css("left"))<0){
						$(this).remove();
					}
			})
		}
		//強化敵人
		function addpwoer(){
			addtime=addtime+1;
			if (addtime>=FPS*5){
				addtime=addtime-FPS*5;
				if (sumbackp>100){
					sumbackp=sumbackp-rand(0,3);		//生成隕石
				}
				if (sumenemyp>100){
					sumenemyp=sumenemyp-rand(0,3);		//生成敵人
				}
				if (shootgunp>100){
					shootgunp=shootgunp-rand(0,3);		//敵人發射子彈機率
				}
				if (sump>100){
					sump=sump-rand(0,3);				//燃料生成機率
				}
				//console.log("隕石"+sumbackp,"敵人"+sumenemyp,"子彈"+shootgunp,"燃料"+sump)
			}
		}
		//上下左右移動(螢幕)
		//向上
		$(".up").mousedown(function(){
			clearInterval(timeup)
			timeup=setInterval(function(){
				if (stop=="false"){
					tmp=(parseFloat($player.css("top")))-upspeed
					if (tmp+parseFloat($player.css("height"))/2>parseFloat($stage.css("top"))){
						$player.css("top",tmp)
					}
				}
			},1000/FPS)
		})
		$(".up").mouseup(function(){
			clearInterval(timeup)
		})
		$(".up").mouseout(function(){
			clearInterval(timeup)
		})
		//向下
		$(".down").mousedown(function(){
			clearInterval(timedown)
			timedown=setInterval(function(){
				if (stop=="false"){
					tmp=(parseFloat($player.css("top")))+downspeed
					if (tmp+parseFloat($player.css("height"))/2<parseFloat($stage.css("height"))){
						$player.css("top",tmp)
					}
				}
			},1000/FPS)
		})
		$(".down").mouseup(function(){
			clearInterval(timedown)
		})
		$(".down").mouseout(function(){
			clearInterval(timedown)
		})
		//向右
		$(".right").mousedown(function(){
			clearInterval(timeright)
			timeright=setInterval(function(){
				if (stop=="false"){
					tmp=(parseFloat($player.css("left")))+rightspeed
					if (tmp+parseFloat($player.css("width"))<parseFloat($stage.css("width"))){
						$player.css("left",tmp)
					}
				}
			},1000/FPS)
		})
		$(".right").mouseup(function(){
			clearInterval(timeright)
		})
		$(".right").mouseout(function(){
			clearInterval(timeright)
		})
		//向左
		$(".left").mousedown(function(){
			clearInterval(timeleft)
			timeleft=setInterval(function(){
				if (stop=="false"){
					tmp=(parseFloat($player.css("left")))-leftspeed
					if (tmp>0){
						$player.css("left",tmp)
					}
				}
			},1000/FPS)
		})
		$(".left").mouseup(function(){
			clearInterval(timeleft)
		})
		$(".left").mouseout(function(){
			clearInterval(timeleft)
		})
		//禁止右鍵
		$("body").contextmenu(function(x){
			x.preventDefault();
		})
		//上下左右移動(鍵盤)
		$("body").keydown(function(x,y){
			if (start=="true"&&stop=="false"){
				if (x.key=="a"||x.key=="A"){
					tmp=(parseFloat($player.css("left")))-leftspeed*2
					if (tmp>0){
						$player.css("left",tmp)
					}
				}
				else if(x.key=="s"||x.key=="S"){
					tmp=(parseFloat($player.css("top")))+downspeed*2
					if (tmp+parseFloat($player.css("height"))/2<parseFloat($stage.css("height"))){
						$player.css("top",tmp)
					}
				}
				else if(x.key=="d"||x.key=="D"){
					tmp=(parseFloat($player.css("left")))+rightspeed*2
					if (tmp+parseFloat($player.css("width"))<parseFloat($stage.css("width"))){
						$player.css("left",tmp)
					}
				}
				else if(x.key=="w"||x.key=="W"){
					tmp=(parseFloat($player.css("top")))-upspeed*2
					if (tmp+parseFloat($player.css("height"))/2>parseFloat($stage.css("top"))){
						$player.css("top",tmp)
					}
				}

			}
		})
		//空白建發射子彈
		$("body").keyup(function(x,y){
			if (stop=="false"){
				if (x.key==" "){
					$stage.append("<div class='mygun'></div>")
					$find=$stage.find(".mygun:last");
					$find.css({
						left:parseFloat($player.css("left"))+35,
						top:parseFloat($player.css("top"))+10,
					})
				}
			}
		})
		//遊戲暫停(開啟)
		$("body").keyup(function ss(x,y){
			if (start=="true"){
				if (x.key=="p"||x.key=="P"){
					if (stop=="true"){
						starttime();
						$(".stop").remove();
						$(".indiv").remove();
						$(".stopstrat").css({
							'background-image':"url('image/gamestop.png')",
						})
					}
					else if (stop=="false"){
						$(".stopstrat").css({
							'background-image':"url('image/gameplay.png')",
						})
						offtime();
						start="true";
						$stage.append("<div class='stop'>遊戲暫停，按P開始遊戲</div>");
						$stage.append("<div class='indiv' id='d1'>隕石生成率："+"1/"+parseInt((sumbackp/FPS)*100)/100+"</div>");
						$stage.append("<div class='indiv' id='d2'>敵人生成率："+"1/"+parseInt((sumenemyp/FPS)*100)/100+"</div>");
						$stage.append("<div class='indiv' id='d3'>敵人發射子彈機率："+"1/"+parseInt((shootgunp/FPS)*100)/100+"</div>");
						$stage.append("<div class='indiv' id='d4'>燃料生成機率："+"1/"+parseInt((sump/FPS)*100)/100+"</div>");

					}
				}
			}
		})
		//遊戲開始/停止(按鈕)
		$(".stopstrat").click(function(){
			if (start=="true"){
				if (stop=="true"){
					starttime();
					$(".stop").remove();
					$(".indiv").remove();
					$(".stopstrat").css({
						'background-image':"url('image/gamestop.png')",
					})
				}
				else if (stop=="false"){
					$(".stopstrat").css({
						'background-image':"url('image/gameplay.png')",
					})
					offtime();
					start="true";
					$stage.append("<div class='stop'>遊戲暫停，按P開始遊戲</div>");
					$stage.append("<div class='indiv' id='d1'>隕石生成率："+"1/"+parseInt((sumbackp/FPS)*100)/100+"</div>");
					$stage.append("<div class='indiv' id='d2'>敵人生成率："+"1/"+parseInt((sumenemyp/FPS)*100)/100+"</div>");
					$stage.append("<div class='indiv' id='d3'>敵人發射子彈機率："+"1/"+parseInt((shootgunp/FPS)*100)/100+"</div>");
					$stage.append("<div class='indiv' id='d4'>燃料生成機率："+"1/"+parseInt((sump/FPS)*100)/100+"</div>");

				}
			}
		})
		//子彈前進
		function gunflying(){
			$findgun=$stage.find(".mygun").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))+gunspped)
				//刪除子彈(超出螢幕)
				if (parseFloat($(this).css("left"))>parseFloat($stage.css("width"))){
					$(this).remove();
				}
			})
		}
		//遊戲結束
		function gameover(){
			mycon=parseInt($(".icon").text());
			offtime();
			$stage.append("<div class='gameover' id='gameover'><table border='1' id='e0'><tbody><tr><td>Input Your Name</td></tr><tr><td><form id='ff'><br><input type='text' name='name' id='name'><input type='text' id='con' name='con' value="+mycon+" style=\"display:none\"><input type='text' id='time' name='time' value="+flyalltime+" style=\"display:none\"><br><br><input type='button' value='檢視分數' disabled='disabled' id='b0'><br><br></form></td></tr></tbody</table></div>");
			//(開始新遊戲)
			$("#b0").click(function(){
				//送出資料
				var name=$("#name").val();
				var con=$("#con").val();
				var time=$("#time").val();
				$.post({
					async:false,
					url:"ck.php",
					data:{name:name,con:con,time:time},
				})
				$.post({
					async:false,
					url:"out.php",
					data:{myname:name},
					success:function(list){
						var arr=list.split(':')
						line0=arr[0].split(',');
						line1=arr[1].split(',')
						line2=arr[2].split(',')
						line3=arr[3].split(',')
						fin=arr[4];
					}
				})
				$("#gameover").remove();
				if (fin=="false"){
					var temp="<div class='gameover' id='showcon'><table border='1' id='e1'><tbody><tr><td colspan='4'>排名</td></tr><tr><td>名次</td><td>名子</td><td>分數</td><td>存活時間</td></tr><tr><td>"+line0[0]+"</td><td>"+line0[1]+"</td><td>"+line0[2]+"</td><td>"+line0[3]+"秒</td></tr><tr><td>"+line1[0]+"</td><td>"+line1[1]+"</td><td>"+line1[2]+"</td><td>"+line1[3]+"秒</td></tr><tr><td>"+line2[0]+"</td><td>"+line2[1]+"</td><td>"+line2[2]+"</td><td>"+line2[3]+"秒</td></tr><tr style='background-color: cornflowerblue'><td>"+line3[0]+"</td><td>"+line3[1]+"</td><td>"+line3[2]+"</td><td>"+line3[3]+"秒</td></tr><tr><td colspan='4'><input type='button' id='finish' value='Start game'><br></td></tr></tbody></table></div>"
				}
				else{
					var tline1="";
					var tline2="";
					var tline3="";
					if (line3==1){
						tline1="style='background-color: cornflowerblue'";
					}
					else if (line3==2){
						tline2="style='background-color: cornflowerblue'";
					}
					else {
						tline3="style='background-color: cornflowerblue'";
					}
					var temp="<div class='gameover' id='showcon'><table border='1' id='e1'><tbody><tr><td colspan='4'>排名</td></tr><tr><td>名次</td><td>名子</td><td>分數</td><td>存活時間</td></tr><tr "+tline1+"><td>"+line0[0]+"</td><td>"+line0[1]+"</td><td>"+line0[2]+"</td><td>"+line0[3]+"秒</td></tr><tr "+tline2+"><td>"+line1[0]+"</td><td>"+line1[1]+"</td><td>"+line1[2]+"</td><td>"+line1[3]+"秒</td></tr><tr "+tline3+"><td>"+line2[0]+"</td><td>"+line2[1]+"</td><td>"+line2[2]+"</td><td>"+line2[3]+"秒</td></tr><tr><td colspan='4'><input type='button' id='finish' value='Start game'><br></td></tr></tbody></table></div>"
				}
				$stage.append(temp);
				$("#finish").click(function(){
					//清理雜物
					$("#showcon").remove();
					$(".gascon").remove();
					$(".mygun").remove();
					$(".back").remove();
					$(".gameover").remove();
					$(".allenemy").remove();
					$(".enemygun").remove();
					//變數重設
					flyalltime=0;
					shootgunp=500;
					sumenemyp=500;
					sumbackp=300; 	//生成隕石機率sumbackp分之1
					score=10; 		//擊破隕石得分數
					FPS=60;
					gasmiss=1000;	//燃料消耗速度
					gashave=15;		//初始燃料
					backattack=15;	//撞到隕石損失的燃料
					sump=400;		//燃料生成機率sump/1s
					gasspeed=1;		//汽油下降速度
					stop="false";	//檢查遊戲是否暫停
					start="true";
					$(".icon").html("0得分");
					//位置重刷
					$player.css({
						top:(parseFloat($stage.css("height"))-parseFloat($player.css("height")))/2,
						left:0,
					})
					$icon.css({
						left:(parseFloat($stage.css("width"))-parseFloat($icon.css("width")))/2
					})
					$gas.html(gashave)
					$gas.css({
						left:(parseFloat($stage.css("width"))-parseFloat($gas.css("width"))),
						top:10,
						'background-image':"url(gas/gas_"+gashave+".png)",
					})
					//系統重啟
					starttime();
				})
			})
		}
		
		//飛行時間計算
		function fly(){
			flyalltime++;
			$("#showtime").html(parseInt(flyalltime/60)+":"+flyalltime%60);
		}
		//燃料消耗
		function gas(){
			if (gashave>0){
				gashave=gashave-1;
				$gas.html(gashave)
				$gas.css({
					'background-image':"url(gas/gas_"+gashave+".png)"
				})
			}
			else{
				stop=="true";
				start="false";
				gameover();
			}
		}
		//生成智能AI
		function sumenemy(){
			if (rand(0,sumenemyp)==0){
				var list=["<div class='enemy1 allenemy'></div>","<div class='enemy2 allenemy'></div>","<div class='enemy3 allenemy'></div>"];
				$stage.append(list[rand(0,2)]);
				$find=$stage.find(".allenemy:last");
				$find.css({
					top:rand(0,600-parseInt($find.css("height"))),
					left:parseFloat($stage.css("width")),
				})
			}
			
		}
		//智能AI移動
		function moveenemy(){
			$stage.find(".allenemy").each(function(){
				$(this).css("left",parseFloat($(this).css("left"))-enemyspeed)
				//刪除AI
				if ((parseInt($(this).css("left"))<-60)){
					$(this).remove();
				}
			})
		}
		//隨機整數
		function rand(nn,mm){
			var num=parseInt(Math.random()*(mm-nn+1))+nn
			return num;
		}
		//判斷
		function yn(){
			//判斷子彈位置(隕石)
			$stage.find(".back").each(function(){
				var tt=$(this)
				var find=$stage.find(".mygun").each(function(){
					if (parseFloat($(this).css("top"))+parseFloat($(this).css("height"))/2>parseFloat(tt.css("top"))//上
					&&parseFloat($(this).css("top"))+parseFloat($(this).css("height"))/2<parseFloat(tt.css("top"))+parseFloat(tt.css("height"))//下
					&&parseFloat($(this).css("left"))+parseFloat($(this).css("width"))/2>parseFloat(tt.css("left"))//左
					&&parseFloat($(this).css("left"))+parseFloat($(this).css("width"))/2<parseFloat(tt.css("left"))+parseFloat(tt.css("width")))//右
					{
						//擊中隕石(消除子彈)
						if (tt.text()==2){
							$(this).remove();
							tt.html(1);
						}
						else{
							//加分
							$(".icon").html(parseInt($(".icon").text())+score+"得分");
							//消除隕石子彈
							$(this).remove();
							tt.remove();
						}
					}
				})
				//玩家碰撞隕石
				if (parseFloat($player.css("top"))+parseFloat($player.css("height"))/2>parseFloat($(this).css("top"))//上
					&&parseFloat($player.css("top"))+parseFloat($player.css("height"))/2<parseFloat($(this).css("top"))+parseFloat($(this).css("height"))//下
					&&parseFloat($player.css("left"))+parseFloat($player.css("width"))/2>parseFloat($(this).css("left"))//左
					&&parseFloat($player.css("left"))+parseFloat($player.css("width"))/2<parseFloat($(this).css("left"))+parseFloat($(this).css("width")))//右
					{
						//撞到隕石減少燃料
						if (gashave>backattack){
							gashave=gashave-backattack;
							$(this).remove();
							$gas.css({
								'background-image':"url(gas/gas_"+gashave+".png)",
							})
						}
						else {//遊戲結束
							stop=="true";
							start="false";
							$(this).remove();
							gameover();
						}
					}
				})
			//玩家碰到燃料
			$stage.find(".gascon").each(function(){
				if (parseFloat($player.css("top"))+parseFloat($player.css("height"))>parseFloat($(this).css("top"))//上
					&&parseFloat($player.css("top"))<parseFloat($(this).css("top"))+parseFloat($(this).css("height"))//下
					&&parseFloat($player.css("left"))+parseFloat($player.css("width"))>parseFloat($(this).css("left"))//左
					&&parseFloat($player.css("left"))<parseFloat($(this).css("left"))+parseFloat($(this).css("width")))//右
					{
						if (gashave<15){
							gashave=gashave+15;
							$gas.css({
								'background-image':"url(gas/gas_"+gashave+".png)",
							})
						}
						else{
							gashave=30;
							$gas.css({
								'background-image':"url(gas/gas_"+gashave+".png)",
							})
						}
						$(this).remove();
					}
			})
			//判斷子彈位置(打敵人)
			$stage.find(".allenemy").each(function(){
				var tt=$(this)
				var find=$stage.find(".mygun").each(function(){
					if (parseFloat($(this).css("top"))+parseFloat($(this).css("height"))/2>parseFloat(tt.css("top"))//上
					&&parseFloat($(this).css("top"))+parseFloat($(this).css("height"))/2<parseFloat(tt.css("top"))+parseFloat(tt.css("height"))//下
					&&parseFloat($(this).css("left"))+parseFloat($(this).css("width"))/2>parseFloat(tt.css("left"))//左
					&&parseFloat($(this).css("left"))+parseFloat($(this).css("width"))/2<parseFloat(tt.css("left"))+parseFloat(tt.css("width")))//右
					{
						//擊中敵人(消除子彈)
						if (tt.text()==2){
							$(this).remove();
							tt.html(1);
						}
						else{
							//加分
							$(".icon").html(parseInt($(".icon").text())+5+"得分");
							//消除敵人&子彈
							$(this).remove();
							tt.remove();
						}
					}
				})
				if (parseFloat($player.css("top"))+parseFloat($player.css("height"))>parseFloat($(this).css("top"))//上
					&&parseFloat($player.css("top"))<parseFloat($(this).css("top"))+parseFloat($(this).css("height"))//下
					&&parseFloat($player.css("left"))+parseFloat($player.css("width"))>parseFloat($(this).css("left"))//左
					&&parseFloat($player.css("left"))<parseFloat($(this).css("left"))+parseFloat($(this).css("width")))//右
					{
						//撞到敵人減少燃料
						if (gashave>backattack){
							gashave=gashave-backattack;
							$(this).remove();
							$gas.css({
								'background-image':"url(gas/gas_"+gashave+".png)",
							})
						}
						else {//遊戲結束
							stop=="true";
							start="false";
							$(this).remove();
							gameover();
						}
					}
			})
			//判斷子彈位置(被敵人打)
			$stage.find(".enemygun").each(function(){
				//玩家碰到敵人子彈
				
				if (parseFloat($player.css("top"))+parseFloat($player.css("height"))>parseFloat($(this).css("top"))//上
					&&parseFloat($player.css("top"))<parseFloat($(this).css("top"))+parseFloat($(this).css("height"))//下
					&&parseFloat($player.css("left"))+parseFloat($player.css("width"))>parseFloat($(this).css("left"))//左
					&&parseFloat($player.css("left"))<parseFloat($(this).css("left"))+parseFloat($(this).css("width")))//右
					{
						//撞到子彈減少燃料
						if (gashave>backattack){
							gashave=gashave-backattack;
							$(this).remove();
							$gas.css({
								'background-image':"url(gas/gas_"+gashave+".png)",
							})
						}
						else {//遊戲結束
							stop=="true";
							start="false";
							$(this).remove();
							gameover();
						}
					}
			})
		}
		//FPS
		var frames = 0,fff=0;
		setInterval(function(){
			fff++;    
		},1000/60)
		setInterval(function(){
			frames++;  
			if (frames>0){
				$(".FPS").html("FPS:"+fff);
				FPS=fff;
				fff=0;
				frames = 0;
		}}, 1000);
		//開始遊戲按鈕
		$(".button").click(function(){
			starttime()
			$(".start").remove();
		})
		$(".button").mousemove(function(){
			$(".button").css("background","#f19e0d")
		})
		$(".button").mouseout(function(){
			$(".button").css("background","white")
		})
		//永遠執行(FPS)
		setInterval(function(){
			if (document.getElementsByName("name")[0])
			if (document.getElementsByName("name")[0].value!=""){
				$("#b0").removeAttr("disabled")
			}
			else{
				$('#b0').attr('disabled',true);
			}
		},1000/60)
		//永遠執行(1s)
		setInterval(function(){
			
		},1000)
		//遊戲教學
		$("#study").click(function(){
			$("#biggen").hide();
			$stage.append("<div class='menu'><div class='in' style='top:80px;'>1.空白艦發射子彈</div><div class='in' style='top:110px;'>2.WSAD或左下角方向建移動</div><div class='in' style='top:140px;'>3.擊碎隕石得10分，擊碎敵人機體得5分</div><div class='in' style='top:170px;'>4.玩得愉快</div><input type='button' id='back' value='back'></div>");
			$("#back").click(function(){
				$(".menu").remove();
				$("#biggen").show();
			})
		})
		
	})
</script>
<link type="text/css" href="allcss.css" rel="stylesheet">
</head>
	
<body>
	<div class="stage">
		<div class="bg"></div>
		<div class="player" id="player"></div>
		<!--移動(上，下，左，右，中)-->
		<div class="move up"></div>
		<div class="move down"></div>
		<div class="move left"></div>
		<div class="move right"></div>
		<div class="move midden"></div>
		<div class="icon">0得分</div>
		<div class='gas misstext' id="gas"></div>
		<div class='gastext'>燃料</div>
		<div class="FPS">FPS:60</div>
		<div class="start" id="biggen">
			<input type="button" value="start" class="button"> 
			<input id="study" type="button" value="遊戲教學" style="width: 64px;height: 64px;position: absolute; top: 270px;left: 620px">
		</div>
		<div class="stopstrat"></div>
		<div class="clack" id="showtime" style="user-select:none">0:0</div>
	</div>
</body>
</html>
