<?php
namespace Phud;

trait Instantiate
{
	public static function init()
	{
		list($phud, $namespace, $class) = explode('\\', get_called_class());
		$d = dir(__DIR__.'/../deploy/init/'.$namespace);
		Debug::log("initializing ".$namespace);
		while($class = $d->read()) {
			if(substr($class, -4) === ".php") {
				$class = substr($class, 0, strpos($class, '.'));
				$called_class = 'Phud\\'.$namespace.'\\'.$class;
				Debug::log("initializing class ".$called_class);
				(new $called_class())->setupAliases();
			}
		}
	}

	public static function initializeInstances()
	{
		foreach(get_declared_classes() as $class) {
			if(strpos($class, 'Phud') !== false) {
				$ref = new \ReflectionClass($class);
				if(in_array('Phud\Instantiate', $ref->getTraitNames())) {
					$class::init();
				}
			}
		}
	}

	abstract public function setupAliases();
}
