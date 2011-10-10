var sock;
var map;
var users = [];
var user;

$(function()
{
	var input = $("#input");
	input.bind('keydown', function(e) {
		var pressed = e.which || e.keyCode;
		if(pressed == 13) {
			sock.send(JSON.stringify({'cmd': 'input', 'transport': input.val()}));
			input.val('');
			out('\n');
		}
		else if(pressed == 37) {
			user.moveX(-5);
		}
		else if(pressed == 38) {
			user.moveY(-5);
		}
		else if(pressed == 39) {
			user.moveX(5);
		}
		else if(pressed == 40) {
			user.moveY(5);
		}
	});
	initSock(function() {
		input.focus();
	});
});

function out(message)
{
	$('#out').append(message.replace(/\n/, '<br />'));
	scrollConsole();
}

function scrollConsole()
{
	var o = $('#out');
	o.scrollTop(o[0].scrollHeight);
}

function parse(transport)
{
	console.log(transport.req+': '+JSON.stringify(transport.data));
	switch(transport.req) {
		case 'out':
			return out(transport.data);
		case 'actors':
			return map.actors(transport.data);
		case 'actor':
			return map.actor(transport.data);
		case 'loggedIn':
			user = new User(transport.data);
			map = new Map();
			map.requestActors();
			return;
	}
}

function initSock(fn)
{
	sock = new WebSocket("ws://24.17.220.111:9000");
	
	sock.onopen = function() {
		console.log('connection open');
		fn();
	};
	
	sock.onmessage = function(m) {
		parse(eval('('+m.data+')'));
	};
	
	sock.onclose = function() {
		console.log('connection closed');
		out('Connection closed.');
	};
}

function send(json)
{
	var s = JSON.stringify(json);
	console.log('sending: '+s);
	sock.send(s);
}

function Map()
{
	var _height = 500;
	var _width = 800;
	var _canvas = $('#frame');
	var _context = _canvas[0].getContext('2d');
	var _actors = {};

	//send({'cmd': 'reqMap'});

	return {
		redraw: function() {
			_context.clearRect(0, 0, _height, _width);
			if(user) {
				user.redraw();
			}
			for(a in _actors) {
				_context.fillStyle = "rgb(255, 0, 0)";
				_context.fillRect(_actors[a]['x'], _actors[a]['y'], 5, 5);
			}
		},
		getContext: function() {
			return _context;
		},
		requestActors: function() {
			send({'cmd': 'reqActors'});
		},
		actors: function(data) {
			_actors = data;
			this.redraw();
		},
		actor: function(data) {
			_actors[data['id']] = data;
			this.redraw();
		}
	};
}

function User(data)
{
	var _id = data['id'];
	var _x = data['x'];
	var _y = data['y'];
	
	return {
		getX: function() {
			return _x;
		},
		getY: function() {
			return _y;
		},
		moveX: function(x) {
			_x += x;
			map.redraw();
			this.updateCoords();
		},
		moveY: function(y) {
			_y += y;
			map.redraw();
			this.updateCoords();
		},
		redraw: function() {
			var img = map.getContext();
			img.fillStyle = "rgb(255, 0, 0)";
			img.fillRect(_x, _y, 5, 5);
		},
		updateCoords: function() {
			send({'cmd': 'updateCoords', 'x': _x, 'y': _y});
		}
	};
}
