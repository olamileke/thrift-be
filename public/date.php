
<!DOCTYPE html>
<html>
	<head>
		<title></title>
	</head>
	<body>


	<script type="text/javascript">
		
		const date=new Date();

		let hour=date.getHours();

		let minutes=date.getMinutes();

		if(hour < 12) {

			alert(`${hour}:${minutes} AM`);
		}
		else if(hour == 12) {

			alert(`${hour}:${minutes} PM`);			
		}
		else {

			alert(`${hour - 12}:${minutes} PM`);			
		}
	</script>
	</body>
</html>