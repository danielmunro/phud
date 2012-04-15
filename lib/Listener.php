<?php
namespace Phud;

trait Listener
{
	protected $listeners = [];

	public function on($event, $callback, $inserted_to = 'front')
	{
		if(!isset($this->listeners[$event])) {
			$this->listeners[$event] = [];
		}
		if($inserted_to === 'front') {
			array_unshift($this->listeners[$event], $callback);
		} else if($inserted_to === 'end') {
			$this->listeners[$event][] = $callback;
		} else {
			Debug::log('[error] Unknown Event::$inserted_to value: '.$inserted_to);
		}
	}

	public function unlisten($event, $callback)
	{
		if(!isset($this->listeners[$event])) {
			$this->listeners[$event] = [];
		}
		$i = array_search($callback, $this->listeners[$event]);
		if($i !== false) {
			unset($this->listeners[$event][$i]);
		}
	}

	public function fire($event, &$a1 = null, &$a2 = null, &$a3 = null, &$a4 = null)
	{
		$response = '';
		if(isset($this->listeners[$event])) {
			foreach($this->listeners[$event] as $i => $listener) {
				$instance = new Event($this, $listener, $a1, $a2, $a3, $a4);
				$status = $instance->getStatus();
				if($status === 'satisfied') {
					return true;
				} else if($status === 'killed') {
					unset($this->events[$event][$i], $response);
					return true;
				}
			}
		}
	}
}
?>
