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

	public function fire($event_type, &$a1 = null, &$a2 = null, &$a3 = null, &$a4 = null)
	{
		$response = '';
		$event = new Event();
		if(isset($this->listeners[$event_type])) {
			foreach($this->listeners[$event_type] as $i => $listener) {
				$event->evaluate($this, $listener, $a1, $a2, $a3, $a4);
				$status = $event->getStatus();
				if($status === 'satisfied') {
					return $event;
				} else if($status === 'killed') {
					unset($this->listeners[$event_type][$i], $response);
					return $event;
				}
			}
		}
		return $event;
	}
}
?>
