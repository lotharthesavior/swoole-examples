<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Form</title>
</head>
<body>

	<h1>Form</h1>
	<div>
		<form id="form1">
			<label for="message">Message:</label>
			<textarea id="message"></textarea>
			<input type="submit" value="Submit" />
		</form>
	</div>
	<div><ul id="messages"></ul></div>

	<script type="text/javascript">
		function startWsConnection(name) {
			const socket = new WebSocket('ws://localhost:8181?name=' + name);

			socket.addEventListener('open', function (e) {
			    socket.send('Hello everybody!');
			});

			socket.addEventListener('message', function (e) {
			    console.log('Message from server ', e.data);
			    let parsedMessage = JSON.parse(e.data);
			    let liEl = document.createElement('li');
				liEl.innerHTML = '(' + parsedMessage['name'] + ') ' + parsedMessage['message'];
				document.getElementById('messages').appendChild(liEl);
			});

			socket.addEventListener('close', function (e) {
			    console.log('Connection terminated with status ' + e.code + ': ' + e.reason);
			    alert(e.reason);
			});

			document.getElementById('form1').onsubmit = (e) => {
				e.preventDefault();
				socket.send(document.getElementById('message').value);
			};
		}

		window.onload = (event) => {
			var name = prompt("Please enter your name", "Harry Potter");
		  	startWsConnection(name);
		};
	</script>

</body>
</html>