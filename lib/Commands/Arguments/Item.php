<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor;

class Item extends Argument
{
	protected $search_in = null;

	public function __construct($search_in)
	{
		$this->search_in = $search_in;
	}

	protected function parseArg(aActor $actor, $arg)
	{
		if($this->search_in && $item = $this->search_in->getItemByInput($arg)) {
			return $item;
		} else {
			$this->status = self::STATUS_INVALID;
			if($this->search_in instanceof aActor) {
				return $actor->notify(ucfirst($this->search_in)." does not have that.");
			}
			if($this->search_in instanceof Room) {
				return $actor->notify("You can't find that anywhere.");
			} else {
				return $actor->notify("You can't buy that here.");
			}
		}
	}
}
