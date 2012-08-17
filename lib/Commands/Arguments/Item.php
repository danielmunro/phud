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
		}
		$this->failItem($actor);
	}

	protected function failItem($actor)
	{
		if($this->search_in instanceof aActor) {
			$this->fail($actor, ($actor === $this->search_in ? "You do" : ucfirst($this->search_in)." does")." not have that.");
		} else if($this->search_in instanceof Room) {
			$this->fail($actor, "You can't find that anywhere.");
		} else {
			$this->fail($actor, "Nothing is there.");
		}
	}
}
