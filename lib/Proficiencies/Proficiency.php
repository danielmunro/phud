<?php
namespace Phud\Proficiencies;
use Phud\Actors\Actor,
	Phud\Actors\User;

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

	public function modifyScore($score)
	{
		if(is_numeric($score)) {
			$this->score += $score;
		} else {
			Debug::error('Proficiency::modifyScore() expects a numeric score, got: '.$score);
		}
	}

	public function checkImprove(Actor $actor)
	{
		if(chance() <= static::$base_improvement_chance && chance() < ($this->score / 100)) {
			$this->score++;
			$actor->notify("Your abilities in ".static::$name." have improved!");
		}
	}

	public static function register($class)
	{
		$qualified_class = 'Phud\\Proficiencies\\'.$class;
		self::$proficiencies[$qualified_class::getName()] = $qualified_class;
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
