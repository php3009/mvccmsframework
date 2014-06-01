var directionArr = new Array('northEast', 'northWest', 'southEast');
var ballMovement = directionArr[Math.floor(Math.random() * directionArr.length)];


var ballPixelMovement = 2;
var brokenBrickArr = new Array();	
			
var horizontalBrickArr1 = new Array();
for(var h=113; h<=126; h++) {
	horizontalBrickArr1.push(h)
}

var horizontalBrickArr2 = new Array();
for(var h=99; h<=112; h++) {
	horizontalBrickArr2.push(h)
}

var horizontalBrickArr3 = new Array();
for(var h=85; h<=98; h++) {
	horizontalBrickArr3.push(h)
}

var horizontalBrickArr4 = new Array();
for(var h=71; h<=84; h++) {
	horizontalBrickArr4.push(h)
}

var horizontalBrickArr5 = new Array();
for(var h=57; h<=70; h++) {
	horizontalBrickArr5.push(h)
}
			
var horizontalBrickArr6 = new Array();
for(var h=43; h<=56; h++) {
	horizontalBrickArr6.push(h)
}

var horizontalBrickArr7 = new Array();
for(var h=29; h<=42; h++) {
	horizontalBrickArr7.push(h)
}

var horizontalBrickArr8 = new Array();
for(var h=15; h<=28; h++) {
	horizontalBrickArr8.push(h)
}

var horizontalBrickArr9 = new Array();
for(var h=1; h<=14; h++) {
	horizontalBrickArr9.push(h)
}

var verticalBrickArr1 = new Array();
for(var v=14; v<=126; v+=14) {
	verticalBrickArr1.push(v)	
}
			
var verticalBrickArr2 = new Array();
for(var v=13; v<=125; v+=14) {
	verticalBrickArr2.push(v)	
}

var verticalBrickArr3 = new Array();
for(var v=12; v<=124; v+=14) {
	verticalBrickArr3.push(v)	
}

var verticalBrickArr4 = new Array();
for(var v=11; v<=123; v+=14) {
	verticalBrickArr4.push(v)	
}

var verticalBrickArr5 = new Array();
for(var v=10; v<=122; v+=14) {
	verticalBrickArr5.push(v)	
}

var verticalBrickArr6 = new Array();
for(var v=9; v<=121; v+=14) {
	verticalBrickArr6.push(v)	
}
			
var verticalBrickArr7 = new Array();
for(var v=8; v<=120; v+=14) {
	verticalBrickArr7.push(v)	
}

var verticalBrickArr8 = new Array();
for(var v=7; v<=119; v+=14) {
	verticalBrickArr8.push(v)	
}

var verticalBrickArr9 = new Array();
for(var v=6; v<=118; v+=14) {
	verticalBrickArr9.push(v)	
}

var verticalBrickArr10 = new Array();
for(var v=5; v<=117; v+=14) {
	verticalBrickArr10.push(v)	
}

var verticalBrickArr11 = new Array();
for(var v=4; v<=116; v+=14) {
	verticalBrickArr11.push(v)	
}

var verticalBrickArr12 = new Array();
for(var v=3; v<=115; v+=14) {
	verticalBrickArr12.push(v)	
}
			
var verticalBrickArr13 = new Array();
for(var v=2; v<=114; v+=14) {
	verticalBrickArr13.push(v)	
}

var verticalBrickArr14 = new Array();
for(var v=1; v<=113; v+=14) {
	verticalBrickArr14.push(v)	
}
					
			
				
$(document).ready(function() {
	
	$( "#bar" ).draggable({ axis: "x",  containment: "#barcontainer", scroll: false});
	
	$(document).bind('keydown', function(e) {
		var code = parseInt(e.keyCode ? e.keyCode : e.which);
		var barposition = $('#bar').position(); 
		
		switch(code) {
			case 37:
				if(parseInt(barposition.left) != 65) {		
					$('#bar').css('left', (parseInt(barposition.left) - 15) + 'px');
				}
				if(parseInt(barposition.left) < 65) {
					$('#bar').css('left', '65px');	
				}
			break;
				
			case 39:
				if(parseInt(barposition.left) != 740) {
					$('#bar').css('left', (parseInt(barposition.left) + 15) + 'px');							
				}
				if(parseInt(barposition.left) > 740) {
					$('#bar').css('left', '740px');
				}
			break;					
		}
	});
				
	var gameOverStatus = 0;
	var pageloaded = 0;				
	
	$('#newgame').click(function() {
		location.reload(); 
		return false;					
	});
	
	function changeBallDirection() {
		switch(ballMovement) {
			case "northEast":
				ballMovement = "southEast";
			break;
			case "northWest":
				ballMovement = "southWest";
			break;
			case "southEast":
				ballMovement = "northEast";
			break;
			case "southWest":
				ballMovement = "northWest";
			break;					
		}				
	}
	
	function startGame() {
		
		//Win Check
		var winFlag = 1					
		for(var g=1; g<=126; g++) {
			if($.inArray(g, brokenBrickArr) == -1) {
				winFlag = 0;						
			}
		}
		if(winFlag) {
			$('#countdown').text('You Won!');
			return false;
		}
		
		var ballposition = $('#ball').position();
		var ballLeftPos = parseInt(ballposition.left);
		var ballTopPos = parseInt(ballposition.top);	
		//$('#log').text($('#log').text() + 'status: ' + ballMovement + ' Left :' + ballLeftPos + ' Top :' + ballTopPos + '<br>');
		if(gameOverStatus == 0) {
			
			if(ballTopPos > 654){
				gameOverStatus == 1;
				$('#countdown').text('Game Over!');
				return false;						
			}
			else {
				//Boundary Collision detection
				//Check left boundary
				if(ballLeftPos <= 62) {
					switch(ballMovement) {
						case "northWest":
							ballMovement = "northEast";	
						break;
						case "southWest":
							ballMovement = "southEast";
						break;								
					}		
				}
				//Check right boundary
				if(ballLeftPos >= 874) {
					switch(ballMovement) {
						case "northEast":
							ballMovement = "northWest";
						break;
						case "southEast":
							ballMovement = "southWest";
						break;								
					}
				}
				//Check top boundary
				if(ballTopPos <= 128) {
					switch(ballMovement) {
						case "northWest":
							ballMovement = "southWest";
						break;
						case "northEast":
							ballMovement = "southEast";
						break;								
					}							
				}
				

				//Check if hitting the bar
				if(ballTopPos == 654) {
					
					var barPositionTemp = $('#bar').position();
					ballLimitLeft = barPositionTemp.left;
					ballLimitRight = barPositionTemp.left + 140;
					
					if(ballLeftPos >= ballLimitLeft && ballLeftPos <= ballLimitRight) {
						switch(ballMovement) {
							case "southWest":
								ballMovement = "northWest";
							break;
							case "southEast":
								ballMovement = "northEast";
							break;									
						}								
					}							
				}
				
				//Check if ball hits the brick
				//Ball is going up
				if(ballLeftPos >= 126 && ballLeftPos <= 810) {
					
					var brickElementIndex = Math.ceil((ballLeftPos - 126) / 50);
						
					switch(ballTopPos) {
						case 334:
							//alert($.inArray(brickArr[brickElementIndex - 1], brokenBrickArr));
							if($.inArray(horizontalBrickArr1[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr1[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr1[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 314:
							if($.inArray(horizontalBrickArr2[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr2[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr2[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 294:
							if($.inArray(horizontalBrickArr3[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr3[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr3[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 274:
							if($.inArray(horizontalBrickArr4[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr4[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr4[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 254:
							if($.inArray(horizontalBrickArr5[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr5[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr5[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 234:
							if($.inArray(horizontalBrickArr6[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr6[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr6[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 214:
							if($.inArray(horizontalBrickArr7[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr7[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr7[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break
						
						case 194:
							if($.inArray(horizontalBrickArr8[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr8[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr8[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 174:
							if($.inArray(horizontalBrickArr9[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr9[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr9[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					}
				}
				
				//Ball is going left
				if(ballTopPos >= 154 && ballTopPos <= 316) {
					var brickElementIndex = Math.ceil((ballTopPos - 154) / 20);
					
					switch(ballLeftPos) {
						case 826:
							if($.inArray(verticalBrickArr1[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr1[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr1[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 776:
							if($.inArray(verticalBrickArr2[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr2[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr2[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					
						case 726:
							if($.inArray(verticalBrickArr3[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr3[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr3[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 676:
							if($.inArray(verticalBrickArr4[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr4[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr4[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 626:
							if($.inArray(verticalBrickArr5[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr5[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr5[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					
						case 576:
							if($.inArray(verticalBrickArr6[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr6[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr6[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 526:
							if($.inArray(verticalBrickArr7[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr7[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr7[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 476:
							if($.inArray(verticalBrickArr8[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr8[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr8[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 426:
							if($.inArray(verticalBrickArr9[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr9[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr9[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					
						case 376:
							if($.inArray(verticalBrickArr10[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr10[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr10[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 326:
							if($.inArray(verticalBrickArr11[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr11[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr11[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 276:
							if($.inArray(verticalBrickArr12[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr12[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr12[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 226:
							if($.inArray(verticalBrickArr13[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr13[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr13[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					
						case 176:
							if($.inArray(verticalBrickArr14[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr14[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr14[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					}
				}
				
				//ball is going down
				if(ballLeftPos >= 126 && ballLeftPos <= 810) {
					var brickElementIndex = Math.ceil((ballLeftPos - 126) / 50);
					
					switch(ballTopPos) {
						case 136:
							if($.inArray(horizontalBrickArr9[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr9[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr9[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 156:
							if($.inArray(horizontalBrickArr8[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr8[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr8[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 176:
							if($.inArray(horizontalBrickArr7[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr7[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr7[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 196:
							if($.inArray(horizontalBrickArr6[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr6[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr6[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 216:
							if($.inArray(horizontalBrickArr5[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr5[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr5[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 236:
							if($.inArray(horizontalBrickArr4[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr4[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr4[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 256:
							if($.inArray(horizontalBrickArr3[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr3[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr3[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 276:
							if($.inArray(horizontalBrickArr2[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr2[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr2[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 296:
							if($.inArray(horizontalBrickArr1[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + horizontalBrickArr1[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(horizontalBrickArr1[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					}								
				}
				
				//ball is going right
				if(ballTopPos >= 154 && ballTopPos <= 316) {
					var brickElementIndex = Math.ceil((ballTopPos - 154) / 20);	
					
					switch(ballLeftPos) {
						
						case 110:
							if($.inArray(verticalBrickArr14[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr14[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr14[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 160:
							if($.inArray(verticalBrickArr13[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr13[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr13[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 210:
							if($.inArray(verticalBrickArr12[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr12[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr12[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 260:
							if($.inArray(verticalBrickArr11[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr11[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr11[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 310:
							if($.inArray(verticalBrickArr10[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr10[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr10[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 360:
							if($.inArray(verticalBrickArr9[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr9[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr9[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 410:
							if($.inArray(verticalBrickArr8[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr8[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr8[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 460:
							if($.inArray(verticalBrickArr7[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr7[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr7[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 510:
							if($.inArray(verticalBrickArr6[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr6[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr6[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 560:
							if($.inArray(verticalBrickArr5[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr5[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr5[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 610:
							if($.inArray(verticalBrickArr4[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr4[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr4[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 660:
							if($.inArray(verticalBrickArr3[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr3[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr3[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 710:
							if($.inArray(verticalBrickArr2[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr2[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr2[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
						
						case 760:
							if($.inArray(verticalBrickArr1[brickElementIndex - 1], brokenBrickArr) == -1) {
								$('#sprite' + verticalBrickArr1[brickElementIndex - 1]).css('visibility', 'hidden');
								brokenBrickArr.push(verticalBrickArr1[brickElementIndex - 1]);
								changeBallDirection();										
							}
						break;
					}						
				}
				
				switch(ballMovement) {
					case "northEast":
						$('#ball').css('left', (ballLeftPos + ballPixelMovement) + 'px').css('top', (ballTopPos - ballPixelMovement) + 'px');
						setTimeout(function() {
 							startGame();
						}, 10);
						return false;		
					break;
					
					case "northWest":
						$('#ball').css('left', (ballLeftPos - ballPixelMovement) + 'px').css('top', (ballTopPos - ballPixelMovement) + 'px');
						setTimeout(function() {
 							startGame();
						}, 10);
						return false;
					break;
					
					case "southEast":
						$('#ball').css('left', (ballLeftPos + ballPixelMovement) + 'px').css('top', (ballTopPos + ballPixelMovement) + 'px');
						setTimeout(function() {
 							startGame();
						}, 10);
						return false;
					break;
					
					case "southWest":
						$('#ball').css('left', (ballLeftPos - ballPixelMovement) + 'px').css('top', (ballTopPos + ballPixelMovement) + 'px');
						setTimeout(function() {
 							startGame();
						}, 10);
						return false;
					break;							
				}
										
			}
		}		
	}	
	
	var countDown = 3;
	runCountDown();
	function runCountDown() {
		if(countDown == 0) {
			$('#countdown').text('');
			startGame();
		}
		else {
			$('#countdown').text('Ready ' + countDown + '...');
			countDown--;
			setTimeout(function() {
 						runCountDown();
			}, 1000);
		}				
	}		
});