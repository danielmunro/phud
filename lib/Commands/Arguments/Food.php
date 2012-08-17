<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Items\Food as iFood;

class Food extends Item
{
	protected function parseArg(aActor $actor, $arg)
	{
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iFood) {
				return $item;
			}
		}
		$this->failItem($actor);
	}
}
