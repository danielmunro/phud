<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Event,
	Phud\Alias,
	Phud\Debug,
	\ReflectionClass,
	\Exception;

abstract class Ability
{
	use Alias;

	protected $proficiency = '';
	protected $required_proficiency = 0;
	protected $hard_modifier = [];
	protected $easy_modifier = [];
	protected $normal_modifier = [];
	protected $needs_target = false;
	protected $alias = '';
	protected $is_offensive = false;
	protected $delay = 0;
	
	protected function __construct()
	{
		if(empty($this->proficiency)) {
			throw new Exception(get_class($this).' is not fully configured, missing: proficiency');
		}
		if(empty($this->required_proficiency)) {
			throw new Exception(get_class($this).' is not fully configured, missing: required proficiency level');
		}
		if(empty($this->alias)) {
			throw new Exception(get_class($this).' is not fully configured, missing: alias');
		}
		self::addAlias($this->alias, $this);
	}

	public function getDelay()
	{
		return $this->delay;
	}

	public function getProficiency()
	{
		return $this->proficiency;
	}

	public function isOffensive()
	{
		return $this->is_offensive;
	}
	
	public static function runInstantiation()
	{
		global $global_path;
		$namespaces = ['Skills', 'Spells'];
		foreach($namespaces as $namespace) {
			$d = dir($global_path.'/deploy/init/'.$namespace);
			while($ability = $d->read()) {
				if(substr($ability, -4) === ".php") {
					Debug::log("init ability: ".$ability);
					$class = substr($ability, 0, strpos($ability, '.'));
					$called_class = 'Phud\\Abilities\\'.$class;
					$reflection = new ReflectionClass($called_class);
					new $called_class();
				}
			}
		}
	}

	public function perform(Actor $actor, $args = [])
	{
		// check for a target if necessary
		$target = $this->determineTarget($actor, $args);
		if($this->needs_target && !$target) {
			return false;
		}
		if(!$target) {
			$target = $actor;
		}
		if($this->is_offensive && $actor != $target) {
			$actor->setTarget($target);
			$target->setTarget($actor);
		}
		// check if actor satisfies requirements as far as mana, mv, etc
		if($this->applyCost($actor) === false) {
			return false;
		}
		// do a proficiency roll to determine success or failure
		$roll = chance() + ($actor->getProficiencyIn($this->proficiency) + $actor->getAttribute('saves') - (($target->getAttribute('saves') + $target->getProficiencyIn($this->proficiency))/2));
		foreach($this->hard_modifier as $m) {
			$roll += $this->getHardAttributeModifier($actor->getAttribute($m));
			$roll -= $this->getHardAttributeModifier($target->getAttribute($m));
		}
		foreach($this->normal_modifier as $m) {
			$roll += $this->getNormalAttributeModifier($actor->getAttribute($m));
			$roll -= $this->getNormalAttributeModifier($target->getAttribute($m));
		}
		foreach($this->easy_modifier as $m) {
			$roll += $this->getEasyAttributeModifier($actor->getAttribute($m));
			$roll -= $this->getEasyAttributeModifier($target->getAttribute($m));
		}
		$roll += $this->modifyRoll($actor);
		if($roll > chance()) {
			$this->success($actor, $target, $args);
			return true;
		} else {
			$this->fail($actor, $target);
			return false;
		}
	}
	
	protected function determineTarget(Actor $actor, $args)
	{
	}

	protected function success(Actor $actor, Actor $target, $args = [])
	{
	}

	protected function fail(Actor $actor, Actor $target, $args = [])
	{
	}

	protected function applyCost(Actor $actor, $args = [])
	{
	}

	protected function modifyRoll(Actor $actor)
	{
	}

	protected function getEasyAttributeModifier($attribute)
	{
		switch($attribute)
		{
			case ($attribute < 15):
				return rand(12, 17);
			case ($attribute < 17):
				return rand(8, 12);
			case ($attribute < 20):
				return rand(0, 6);
			case ($attribute < 22):
				return 0;
			case ($attribute < 25):
				return -(rand(0, 5));
			default:
				return -(rand(0, 10));
		}
	}
	
	protected function getNormalAttributeModifier($attribute)
	{
		switch($attribute)
		{
			case ($attribute < 15):
				return rand(18, 25);
			case ($attribute < 17):
				return rand(10, 18);
			case ($attribute < 20):
				return rand(4, 10);
			case ($attribute < 22):
				return rand(0, 4);
			case ($attribute < 25):
				return -(rand(0, 3));
			default:
				return -(rand(1, 4));
		}
	}
	
	protected function getHardAttributeModifier($attribute)
	{
		switch($attribute)
		{
			case ($attribute < 15):
				return rand(30, 40);
			case ($attribute < 17):
				return rand(20, 30);
			case ($attribute < 20):
				return rand(10, 20);
			case ($attribute < 22):
				return rand(0, 10);
			case ($attribute < 25):
				return 0;
			default:
				return rand(0, 5);
		}
	}
}
?>
