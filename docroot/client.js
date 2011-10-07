$(function()
{
	var sock = new WebSocket("ws://192.168.0.111:9000");
	
	sock.onopen = function() { console.log('connection open'); };
	sock.onmessage = function(m) { out.append('<p>'+m.data+'</p>'); };
	sock.onclose = function() { console.log('connection closed'); };

	var canvas = $('#frame')[0];
	var context = canvas.getContext('2d');
	context.fillStyle = "rgb(255, 0, 0)";
	context.fillRect(30, 30, 50, 50);
	
	var out = $("#out");
	var input = $("#input");

	input.bind('keydown', function(e) {
		if(e.which == 13 || e.keyCode == 13) {
			sock.send(input.val());
			input.val('');
		}
	});
});
