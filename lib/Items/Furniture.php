<?php
namespace Items;
use \Mechanics\Actor;

class Furniture extends Item
{
	protected $can_own = false;
	protected $regen = 0;
	protected $capacity = 1;
	protected $actors = [];

	public function hasCapacity(Actor $actor = null)
	{
		if($actor) {
			$i = array_search($actor, $this->actors);
		}
		return $i === false ? sizeof($this->actors) < $this->capacity : sizeof($this->actors) - 1 < $this->capacity;
	}

	public function addActor(Actor $actor)
	{
		$this->actors[] = $actor;
		if($actor->getFurniture()) {
			$actor->setFurniture(null);
		}
		$actor->setFurniture($this);
	}

	public function removeActor(Actor $actor)
	{
		$i = array_search($actor, $this->actors);
		if($i !== false) {
			unset($this->actors[$i]);
		}
	}
}

?>
