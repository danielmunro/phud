<?php
namespace Phud\Proficiencies;
use Phud\Actors\Actor;

abstract class Proficiency
{
	protected static $name = '';
	protected static $attributes = [];
	protected static $base_improvement_chance = 0.01;
	protected static $proficiencies = [];
	protected $score = 15;

	abstract public function getImprovementListeners();

	public function getScore()
	{
		return $this->score;
	}

	public function checkImprove(Actor $actor)
	{
		if(chance() <= static::$base_improvement_chance && chance() < ($this->score / 100)) {
			$this->score++;
			Server::out($actor, "Your abilities in ".static::$name." have improved!");
		}
	}

	public static function register($class)
	{
		self::$proficiencies[$class] = 'Phud\\Proficiencies\\'.$class;
	}

	public static function getProficiencies()
	{
		return self::$proficiencies;
	}

	public static function getName()
	{
		return static::$name;
	}

	public function __toString()
	{
		return static::$name;
	}
}
?>
