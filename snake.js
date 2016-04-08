$(document).ready(function(){
// settings 
	var userScore = 0; /// рамер сетки
	var key = {LEFT: 37,
  				UP: 38,
  				RIGHT: 39,
  				DOWN: 40};
  	var direction = 'down';
  	var interval = 400;
  	var chSpeed = 50; 
  	var infoField = 150;
  	var snakeSize = parseInt($("#snake").css('width'));
  	var step = snakeSize;
	var snakeHead;
	var snakeHeadPB;// head place before
	var snakeTail = new Array;
	
  	var gameFieldHeight = document.documentElement.clientHeight - infoField - (document.documentElement.clientHeight - infoField)%snakeSize;
  	
  	if(document.documentElement.clientWidth < 800){
		var gameFieldWidth = document.documentElement.clientWidth - document.documentElement.clientWidth%snakeSize;
	}else{
		var gameFieldWidth = document.documentElement.clientWidth - 420 - (document.documentElement.clientWidth - 420)%snakeSize;
	}
	
	$("#game_field").css({width:gameFieldWidth+'px',
						  height:gameFieldHeight+'px'});
	$("#head_info").css({height:'110px'});
	
	var zeroPoint = $("#game_field").offset();
	
	var Xlen = gameFieldWidth/snakeSize;
	var Ylen = gameFieldHeight/snakeSize;


// менять только direction
	function handler(event){
		if (event.keyCode == key.LEFT) {
			
			if(direction == 'left'){
				Speed('-', chSpeed);
			}else if(direction == 'right'){
				Speed('+', chSpeed);
			}else direction = 'left';	
					
		}
		if (event.keyCode == key.UP) {
			
			if(direction == 'up'){
				Speed('-', chSpeed);
			}else if(direction == 'down'){
				Speed('+', chSpeed);
			}else direction = 'up';
			
		}
		if (event.keyCode == key.RIGHT) {
			
			if(direction == 'right'){
				Speed('-', chSpeed);
			}else if(direction == 'left'){
				Speed('+', chSpeed);
			}else direction = 'right';
			
		}
		if (event.keyCode == key.DOWN) {
			
			if(direction == 'down'){
				Speed('-', chSpeed);
			}else if(direction == 'up'){
				Speed('+', chSpeed);
			}else direction = 'down';
			
		}

	}
	window.addEventListener('keydown', handler, false);
	
//--------------
	
	function MoveSnake()
	{
		var  curent = $("#snake").offset();
		
		snakeHeadPB = curent;

		if (direction == 'left') {
			$("#snake").offset({top:curent.top,
								left:curent.left-step});
		}
		if (direction == 'up') {
			$("#snake").offset({top:curent.top-step,
								left:curent.left});
		}
		if (direction == 'right') {
			$("#snake").offset({top:curent.top,
								left:curent.left+step});
		}
		if (direction == 'down') {
			$("#snake").offset({top:curent.top+step,
								left:curent.left});
		}
		snakeHead = $("#snake").offset();
		BorderImpact(snakeHead);
		
		var data = CheckFoodInCell(snakeHead);
		
		if(data.flag) {GrowSnake(snakeHead,data.img); WriteInfo(data);}
		else MoveTail(snakeHead);

		CreateFood();
		CreateDanger();
	}
	var move = setInterval( MoveSnake, interval );

	function Speed(val, change)
	{
		if(val == '-' && interval > 100) interval -= change;
		if(val == '+' && interval < 700) interval += change;
		clearInterval(move);
		move = setInterval( MoveSnake, interval );
	}
	
	function BorderImpact(position)
	{
		var gOver = false;
		// Check bottom
		if(position.top >= (gameFieldHeight+infoField-snakeSize)){
			clearInterval(move); gOver = true;
		}
		// Check top
		if(position.top < (infoField-snakeSize)){
			clearInterval(move); gOver = true;
		}
		// Check left
		if(position.left < (zeroPoint.left)){
			clearInterval(move); gOver = true;
		}
		// Check right
		if(position.left > (zeroPoint.left+gameFieldWidth)){
			clearInterval(move); gOver = true;
		}
		if(gOver == true){
			alert('Game Over');
		}
	}
	/*
	* Создание пищи *****************************************************
	*/
	function CreateFood()
	{
		if( $(".food").length < 50 ){
			var newFood = RandomCoordinate();//
			var div = document.createElement('div');
			$(div).addClass("food");
			$(div).css('text-align', 'center');
			foodBG = Math.floor(Math.random() * 8);
			var img = document.createElement('img');
			$(img).attr('src', '/images/'+foodBG+'.png');
			$(img).attr('height', snakeSize);
			$(img).attr('data-unit', foodBG);
			$(img).css('border-radius', '50%');
			$(img).appendTo(div);
			$(div).appendTo("#game_field");

			$(div).offset({top:newFood.top,
						   		left:newFood.left});
		}
	}
	
	/*
	* Будет создавать ловушки
	*/
	function CreateDanger()
	{
		
	}
	/*
	* генератор координат
	*/
	function RandomCoordinate()
	{
		var  curent = $("#snake").offset();
		do{
			var flag = false;
			var coord = new Object;
			coord.left = (Math.floor(Math.random() * Xlen))*snakeSize+zeroPoint.left+1;// +1 поправка для -- 
			coord.top = (Math.floor(Math.random() * Ylen))*snakeSize+zeroPoint.top+1;// -- совпадения координат со snake
			
			$("#game_field > div").each(function(){
				if($(this).offset()['top'] == coord.top &&
				   $(this).offset()['left'] == coord.left)
						flag = true;
				
			});
		}while(flag)
		
		return coord;
	}
	
	/*
	* Проверка на "еду"
	*/
	function CheckFoodInCell(head)
	{
		var data = new Object;
			data.flag = false;
		$("#game_field > .food").each(function(){
			if($(this).offset()['top'] == head.top &&
				   $(this).offset()['left'] == head.left){
				   		data.img = $(this).find('img').attr('src');
				   		data.unit = $(this).find('img').attr('data-unit');
						$(this).remove();
						userScore++;
						$("#score").html(userScore*10);
						data.flag = true;
			}
		});

		return data;
	}
	/*
	* Удлинитель змейки
	*/
	function GrowSnake(head,img_src)
	{
		//координаты предыдущего места
		var newFood = RandomCoordinate();//
		var div = document.createElement('div');
		$(div).addClass("snake_body");

		var img = document.createElement('img');
			$(img).attr('src', '/images/snBG.png');
			$(img).attr('height', snakeSize);
			$(img).css('border-radius', '3%');
			$(img).appendTo(div);

		$(div).css('width', snakeSize);
		$(div).css('height', snakeSize);
		$(div).appendTo("#game_field");
		$(div).offset({top:snakeHeadPB.top,
				   		left:snakeHeadPB.left});

		snakeTail.unshift(snakeHeadPB);
	}
	
	/*
	* подтаскивание хвоста
	*/
	function MoveTail()
	{
		snakeTail.unshift(snakeHeadPB);
		snakeTail.pop();
		var collection = $(".snake_body");
		for(var i=0; i<snakeTail.length; i++){
			$(collection[i]).offset({top:snakeTail[i].top,
				   				 left:snakeTail[i].left});
		}
	}
	
	/*
	*	Вывод информации
	*/
	function WriteInfo(data)
	{
		var score = parseInt($("#inf"+data.unit).html());
		console.log(score+'s');
		if(!score){
			console.log(data.unit+'u');
			var div = document.createElement('div');
			$(div).css('text-align','left');
			var img = document.createElement('img');
			$(img).attr('src', '/images/'+data.unit+'.png');
			$(img).attr('width', '25');
			$(img).appendTo(div);
			var span = document.createElement('span');
			$(span).html(' ');
			$(span).appendTo(div);
			var span = document.createElement('span');
			$(span).attr('id','inf'+data.unit);
			$(span).html('1');
			$(span).appendTo(div);
			$(div).appendTo("#results");
		}else{
			$("#inf"+data.unit).html(score+1);
		}
	}
});