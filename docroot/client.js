var sock;
var room;
var user;
var canvas_height = 145;
var canvas_width = 295;
var border_padding = 15;

$(function()
{
	var input = $("#input");
	input.bind('keydown', function(e) {
		var pressed = e.which || e.keyCode;
		if(pressed == 13) {
			send({'cmd': 'input', 'transport': input.val()});
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
	switch(transport.req) {
		
		// Output to console
		case 'out':
			return out(transport.data);

		// Reload one actor (if they move, etc)
		case 'actor':
			room.actor(transport.data);
			break;

		// Logged in, build context for the user
		case 'loggedIn':
			user = new User(transport.data);
			room = new Room();
			break;

		// Loading room info
		case 'room':
			room.load(transport.data);
			break;
	}
	if(room) {
		room.redraw();
	}
}

function initSock(callback)
{
	sock = new WebSocket("ws://24.17.220.111:9000");
	
	sock.onopen = function() {
		console.log('connection open');
		callback();
	};
	
	sock.onmessage = function(transport) {
		var msg = eval('('+transport.data+')');
		if(msg.req != 'out') {
			// Don't log stuff going to the console, too spammy
			console.log('recv: '+msg.req+': '+JSON.stringify(msg.data));
		}
		parse(msg);
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

function Room()
{
	var _canvas = $('#frame');
	var _offset_x = 0;
	var _offset_y = 0;
	var _height = 0;
	var _width = 0;
	var _context = _canvas[0].getContext('2d');
	var _actors = {};
	var _bg_image = null;
	var _bg_image_loaded = false;

	// Initialize the room info
	send({'cmd': 'reqRoom'});

	return {
		load: function(data) {
			// background image stuff
			_bg_image_loaded = false;
			_bg_image = new Image();
			_bg_image.src = '/resources/'+data['bg_image'];
			_bg_image.onload = function() {
				room.BGImageLoaded();
			};

			// actors
			_actors = data['actors'];
		},
		redraw: function() {
			// Clear the existing screen
			_context.clearRect(0, 0, canvas_height, canvas_width);

			// Redraw the background image if it exists
			if(_bg_image_loaded) {
				_context.drawImage(_bg_image, _offset_x, _offset_y);
			}

			// Redraw the actors
			for(a in _actors) {
				_context.fillStyle = "rgb(255, 0, 0)";
				var x = _actors[a]['x'];
				var y = _actors[a]['y'];
				if(_actors[a]['id'] == user.getID()) {
					x = user.getOffsetX();
					y = user.getOffsetY();
				}
				// TODO don't draw actors that are off screen
				_context.fillRect(x, y, 5, 5);
			}
		},
		getContext: function() {
			return _context;
		},
		actor: function(data) {
			_actors[data['id']] = data;
		},
		BGImageLoaded: function() {
			_height = _bg_image.height;
			_width = _bg_image.width;
			_bg_image_loaded = true;
		},
		getHeight: function() {
			return _height;
		},
		getWidth: function() {
			return _width;
		},
		moveOffsetX: function(x) {
			_offset_x += x;
		},
		moveOffsetY: function(y) {
			_offset_y += y;
		}
	};
}

function User(data)
{
	var _id = data['id'];
	var _x = data['x'];
	var _y = data['y'];
	var _offset_x = 0;
	var _offset_y = 0;
	
	return {
		getID: function() {
			return _id;
		},
		getX: function() {
			return _x;
		},
		getY: function() {
			return _y;
		},
		getOffsetX: function() {
			return _offset_x;
		},
		getOffsetY: function() {
			return _offset_y;
		},
		moveX: function(x) {
			var new_x = _x + x;
			if(new_x < room.getWidth() && new_x > 0) {
				_x = new_x;
				_offset_x += x;
				if(_offset_x < border_padding) {
					_offset_x = border_padding;
					room.moveOffsetX(-x);
				}
				else if(_offset_x > canvas_width - border_padding) {
					_offset_x = canvas_width - border_padding;
					room.moveOffsetX(-x);
				}
				this.moved();
			}
		},
		moveY: function(y) {
			var new_y = _y + y;
			if(new_y < room.getHeight() && new_y > 0) {
				_y = new_y;
				_offset_y += y;
				if(_offset_y < border_padding) {
					_offset_y = border_padding;
					room.moveOffsetY(-y);
				}
				else if(_offset_y > canvas_height - border_padding) {
					_offset_y = canvas_height - border_padding;
					room.moveOffsetY(-y);
				}
				this.moved();
			}
		},
		moved: function() {
			room.actor(this.getProperties());
			room.redraw();
			send({'cmd': 'updateCoords', 'x': _x, 'y': _y});
		},
		getProperties: function() {
			return {'id': _id, 'x': _x, 'y': _y};
		}
	};
}
