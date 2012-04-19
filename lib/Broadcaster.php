<?php
namespace Phud;

trait Broadcaster
{
	public function fire($event)
	{
		foreach(Listener::getEvents() as $event) {
		}
	}
}
?>
