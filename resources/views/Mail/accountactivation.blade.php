<!DOCTYPE html>
<html>
	<head>
		<title></title>
	</head>
	<style type="text/css">
		@import url('https://fonts.googleapis.com/css?family=Quicksand&display=swap');
		@import url('https://fonts.googleapis.com/css?family=Raleway&display=swap');
		body
		{
			margin:0;
			padding:0;
			background:url("{{ env('APP_URL') }}/Images/Mail/dust.png");
			display:flex;
			flex-flow:column;
			justify-content: center;
			align-items: center;
			padding:10vh 0;
			min-height: 100vh;
		}

		.heading 
		{
			position:relative;
			top:-5vh;
			margin-bottom:30px;
			font-family:'Quicksand', cursive;
			font-size:1.95em;
			color:rgba(0,0,0,0.7);
		}

		.container
		{
			position:relative;
			top:-5vh;
			width:50vw;
			border:1px solid rgba(0,0,0,0.07);
			background:#fff;
			font-family:'Quicksand', sans-serif;
			color:rgba(0,0,0,0.7);
			line-height: 1.7;			
			padding:50px 70px;
			box-sizing: border-box;
		}

		.container a
		{
			position:relative;
			top:15px;
			text-decoration: none;
			color:#fff;
			background:#CC0000;
			padding:5px;
			display: block;
			width:70px;
			box-sizing: border-box;
			font-family:'Nunito', sans-serif;
			border-radius:3px;
		}

		p
		{
			position:relative;
			top:-10px;
			margin:0 0 -3px 0;
		}

		.social-links
		{
			position:relative;
			top:3px;
			display:flex;
			justify-content: center;
		}

		.social-links a
		{
			color:rgba(0,0,0,0.75);
			margin-right:5px;
			text-decoration: none;
			font-family:'Quicksand', sans-serif
		}

		.location
		{
			position:relative;
			top:18px;
			color:rgba(0,0,0,0.75);
			font-family:'Raleway', sans-serif;
		}
	</style>
	<body>

		<p class='heading'>Thrift</p>

		<div class='container'>
			
			Hello <b>{{ $user->getLastName() }}</b>!,<br/> Welcome to Thrift. One more step to complete your Thrift account registration.<br/>
			Kindly click this button to activate your account<br/>

			<a href="{{ env('CLIENT_URL') }}/account/activate/{{ $token }}">Activate</a> <br/>

			<p>Happy expense tracking!</p>

			Love,<br/>

			The <b>Thrift</b> Team
		</div>

		<div class='social-links'>
			<a href="">Facebook</a>
			<a href="">Twitter</a>
			<a href="">Instagram</a>
		</div>

		<div class='location'>
			3, Bisi Awosika Street, Ologolo, Lekki, Lagos
		</div>
	</body>
</html>