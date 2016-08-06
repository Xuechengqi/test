<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>Mideo Login</title>
	<style type="text/css">
	*{box-sizing:border-box;}
	html,body{
		width: 100%;
		height: 100%;
	}
	body{
		overflow: hidden;
		position: relative;
	    text-align: center;
	    text-shadow: 0 1px 3px rgba(0,0,0,.4),0 0 30px rgba(0,0,0,.075);
	    background: #020031;
	    background: -moz-linear-gradient(45deg,#020031 0,#6d3353 100%);
	    background: -webkit-gradient(linear,left bottom,right top,color-stop(0%,#020031),color-stop(100%,#6d3353));
	    background: -webkit-linear-gradient(45deg,#020031 0,#6d3353 100%);
	    background: -o-linear-gradient(45deg,#020031 0,#6d3353 100%);
	    background: -ms-linear-gradient(45deg,#020031 0,#6d3353 100%);
	    background: linear-gradient(45deg,#020031 0,#6d3353 100%);
	}
	.form{
		margin-left: auto;
		margin-right: auto;
		margin-top: 200px;
		width: 500px;
		height: 350px;
		background: #fff;
		border: 1px solid #ccc;
	}
	.form-item{
		text-align: center;
		margin: 20px;
		font-size: 16px;
	}
	.form-item input{
		padding: 10px;
		width: 280px;
	}
	</style>
</head>
<body>
	<div class="main">
		<form class="form" action="login" method="post">
			<div class="form-item">
				<h1>Mideo Login</h1>
			</div>
			<div class="form-item">
				<input type="text" name="username" placeholder="username" />
			</div>
			<div class="form-item">
				<input type="password" name="password" placeholder="password" />
			</div>
			<div class="form-item">
				<input type="text" name="token" placeholder="token" />
			</div>
			<div class="form-item">
				<input type="submit" value="LOGIN">
			</div>
		</form>
	</div>
</body>
</html>