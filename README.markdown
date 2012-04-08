Phud
====

Phud is a telnet socket server for playing text dungeon games, with races, abilities, items, and mobiles.
The name is an abbreviation of php mud.



Requirements
------------

Requirements include [php 5.4](http://www.php.net/) and up, and [redis](http://redis.io/).



Configuring
-----------

To set up, open up 'game.php' in the project root and change the $address to your local network ip, ie 192.168.0.x,
and port to an open, unused port, such as 9000.

	$address = '192.168.0.106';
	$port = 9000;



Running
-------

	php game.php &
	telnet <address> <port>

If everything was successful, you should see a prompt, 'By what name do you wish to be known?'
