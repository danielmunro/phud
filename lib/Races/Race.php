<?php
namespace Phud\Races;
use \Exception,
	Phud\Alias,
	Phud\Instantiate,
	Phud\Server,
	Phud\Debug;

abstract class Race
{
	use Alias, Instantiate;

	const SIZE_TINY = 2;
	const SIZE_SMALL = 3;
	const SIZE_NORMAL = 4;
	const SIZE_LARGE = 5;
	const SIZE_GIGANTIC = 6;

	const FORM_HUMANOID = 'humanoid';
	const FORM_REPTILE = 'reptile';
	
	protected $attributes = null;
	protected $max_attributes = null;
	protected $affects = array();
	protected $move_verb = 'leaves';
	protected $unarmed_verb = 'punch';
	protected $size = self::SIZE_NORMAL;
	protected $playable = false;
	protected $alias = null;
	protected $proficiencies = [];
	protected $thirst = 20;
	protected $hunger = 20;
	protected $full = 40;
	protected $movement_cost = 1;
	protected $form = self::FORM_HUMANOID;
	protected $parts = ['head', 'arm', 'leg', 'heart', 'brain', 'guts', 'hand', 'foot', 'finger', 'ear', 'eye', 'long tongue', 'tentacles', 'fins', 'wings', 'tail'];
	
	protected function __construct()
	{
		if(!$this->alias) {
			throw new Exception("Need to set an alias for racial class: ".get_class($this));
		}
		self::addAlias($this->alias, $this);
		$this->setPartsFromForm();
	}

	public function getAttribute($key)
	{
		return $this->attributes->getAttribute($key);
	}

	protected function addParts($parts_add)
	{
		$this->parts = array_merge($this->parts, array_diff($this->parts, $parts_add));
	}

	protected function removeParts($parts_remove)
	{
		$this->parts = array_diff($this->parts, $parts_remove);
	}

	protected function setPartsFromForm()
	{
		if($this->form === self::FORM_HUMANOID) {
			$this->removeParts(['long tongue', 'tentacles', 'fins', 'wings', 'tail']);
		}
		else if($this->form === self::FORM_REPTILE) {
			$this->removeParts(['foot', 'finger', 'ear', 'tentacles', 'fins', 'wings']);
		}
	}

	public function getThirst()
	{
		return $this->thirst;
	}

	public function getHunger()
	{
		return $this->hunger;
	}
	
	abstract public function getListeners();

	abstract public function getAbilities();
	
	public function getProficiencies()
	{
		return $this->proficiencies;
	}

	public function getParts()
	{
		return $this->parts;
	}
	
	public function getMaxAttributes()
	{
		return $this->max_attributes;
	}
	
	public function getSize()
	{
		return $this->size;
	}

	public function getMovementCost()
	{
		return $this->movement_cost;
	}

	public function getUnarmedVerb()
	{
		return $this->unarmed_verb;
	}

	public function getMoveVerb()
	{
		return $this->move_verb;
	}

	public function getFull()
	{
		return $this->full;
	}

	public function isPlayable()
	{
		return $this->playable;
	}
	
	public function getAlias()
	{
		return $this->alias;
	}
	
	public function __toString()
	{
		if($this->alias)
			return $this->alias->getAliasName();
		return '';
	}

	public function __sleep()
	{
		return ['alias'];
	}
}

Server::instance()->on('initialized', function($event) {
	Race::init();
});
?>
