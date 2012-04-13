<?php
namespace Phud;

trait Listener
{
	protected $events = [];

	public function on($event, $callback, $inserted_to = 'front')
	{
		if(!isset($this->events[$event])) {
			$this->events[$event] = [];
		}
		if($inserted_to === 'front') {
			array_unshift($this->events[$event], $callback);
		} else if($inserted_to === 'end') {
			$this->events[$event][] = $callback;
		} else {
			Debug::log('[error] Unknown Event::$inserted_to value: '.$inserted_to);
		}
	}

	public function unlisten($event, $callback)
	{
		if(!isset($this->events[$event])) {
			$this->events[$event] = [];
		}
		$i = array_search($this->events[$event], $callback);
		if($i !== false) {
			unset($this->events[$event][$i]);
		}
	}

	public function fire($event, &$a1, &$a2, &$a3, &$a4)
	{
		$response = '';
		if(isset($this->events[$event])) {
			foreach($this->events[$event] as $i => $event) {
				$ev_response = $event($this, $a1, $a2, $a3, $a4);
				$response = $response || $ev_response;
				if($response === 'kill') {
					unset($this->events[$event][$i], $response);
				} else if($response === 'satisfy') {
					return;
				}
			}
		}
		return $response;
	}
}
?>
