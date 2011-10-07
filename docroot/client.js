var sock;
$(function()
{
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
	
	initSock();
});

function out(message)
{
	out.append(m.data.replace(/\r\n/, '<br />'));
}

function parse(transport)
{
	console.log(transport.req+': '+transport.data);
	switch(transport.req) {
		case 'out':
			return out(transport.data);
	}
}

function initSock()
{
	sock = new WebSocket("ws://192.168.0.111:9000");
	
	sock.onopen = function() {
		console.log('connection open');
	};
	
	sock.onmessage = function(m) {
		parse(eval(m.data));
	};
	
	sock.onclose = function() {
		console.log('connection closed');
		out('Connection closed.');
	};
}
