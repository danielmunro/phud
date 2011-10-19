var sock;
var room;
var user;
var canvas_height = 145;
var canvas_width = 295;
var border_padding = 30;

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
			send({'cmd': 'moveX', 'x': -5});
		}
		else if(pressed == 38) {
			send({'cmd': 'moveY', 'y': -5});
		}
		else if(pressed == 39) {
			send({'cmd': 'moveX', 'x': 5});
		}
		else if(pressed == 40) {
			send({'cmd': 'moveY', 'y': 5});
		}
	});
	initSock(function() {
		input.focus();
	});
});

function scrollConsole()
{
	var o = $('#out');
	o.scrollTop(o[0].scrollHeight);
}

function parse(transport)
{
	var api_methods = ['out', 'loggedIn', 'room.actor', 'room.load', 'user.images', 'user.moveX', 'user.moveY'];
	if(api_methods.indexOf(transport.req) > -1) {
		var m = transport.req;
		eval(m+'(transport.data)');
	} else {
		console.log('invalid method: '+m);
	}
}

function out(data)
{
	$('#out').append(data.replace(/\n/, '<br />'));
	scrollConsole();
}

function loggedIn(data)
{
	user = new User(data);
	room = new Room();

	// request room info from the server
	send({'cmd': 'reqRoom'});
	send({'cmd': 'reqImages'});
}

function initSock(callback)
{
	/**
	 * there are three events associated with a websocket:
	 * 
	 * onopen() - which is fired after making the initial request to the server (by requesting a new WebSocket())
	 * and the server successfully completes the handshake. Currently, phud is configured to accept handshakes
	 * following the hybi-10 draft (Chrome 14).
	 *
	 * onmessage() - this event is fired when we get data from the server. For the purposes of this application,
	 * all data is sent and received in JSON.
	 *
	 * onclose() - will fire when the server is no longer responsive and the connection is lost.
	 */
	
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
	var _bg_image_collisions = null;
	var _images_loaded = false;
	
	_canvas.bind('click', function(e_cl) {
		var x = e_cl.pageX || e_cl.clientX;
		var y = e_cl.pageY || e_cl.clientY;
		console.log('click at '+x+', '+y);
	});

	return {
		load: function(data) {
			// background image stuff
			_images_loaded = false;
			var fn_image_loaded = function() {
				if(room.imagesLoaded())
					room.redraw();
			};
			_bg_image = new Image();
			_bg_image_collisions = new Image();
			_bg_image.src = '/resources/'+data['bg_image'];	
			_bg_image_collisions.src = '/resources/'+data['bg_image_collisions'];
			_bg_image.onload = fn_image_loaded;
			_bg_image_collisions.onload = fn_image_loaded;

			// set offsets
			_offset_x = -user.getX();
			_offset_y = -user.getY();

			// actors
			_actors = data['actors'];
		},
		redraw: function() {
			// Clear the existing screen
			_context.clearRect(0, 0, canvas_height, canvas_width);

			// Redraw the background image if it exists
			if(_images_loaded) {
				_context.drawImage(_bg_image, _offset_x, _offset_y);
			}

			// Redraw the actors
			// TODO don't draw actors that are off screen
			for(a in _actors) {
				if(_actors[a]['id'] == user.getID() && user.getImage('walking')) {
					_context.drawImage(user.getImage('walking'), user.getOffsetX(), user.getOffsetY());
				} else {
					_context.fillStyle = "rgb(255, 0, 0)";
					_context.fillRect(_actors[a]['x'], _actors[a]['y'], 5, 5);
				}
			}
		},
		getContext: function() {
			return _context;
		},
		actor: function(data) {
			_actors[data['id']] = data;
		},
		imagesLoaded: function() {
			console.log('calling imagesLoaded: '+_images_loaded);
			if(_images_loaded)
				return true;
			if(_bg_image.complete) {
				_height = _bg_image.height;
				_width = _bg_image.width;
			}
			if(_bg_image.complete && _bg_image_collisions.complete) {
				_images_loaded = true;
				return true;
			}
			return false;
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
	var _offset_x = parseInt(canvas_width / 2);
	var _offset_y = parseInt(canvas_height / 2);
	var _images = {};

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
			_x += x;
			_offset_x += x;
			console.log('user offset '+_offset_x+', '+_offset_y);
			room.redraw();
		},
		moveY: function(y) {
			_y += y;
			_offset_y += y;
			console.log('user offset '+_offset_x+', '+_offset_y);
			room.redraw();
		},
		images: function(data) {
			for(i in data) {
				console.log('loading image '+i+', '+data[i]);
				_images[i] = new Image();
				_images[i].src = data[i];
			}
		},
		getImage: function(type) {
			if(_images[type])
				return _images[type];
			if(_images['*'])
				return _images['*'];
		}
	};
}
