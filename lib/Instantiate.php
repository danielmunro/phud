<?php
namespace Phud;

trait Instantiate
{
	public static function init()
	{
		global $global_path;
		list($phud, $namespace, $class) = explode('\\', get_called_class());
		$d = dir($global_path.'/deploy/init/'.$namespace);
		Debug::log("init ".$namespace);
		while($class = $d->read()) {
			if(substr($class, -4) === ".php") {
				$class = substr($class, 0, strpos($class, '.'));
				$called_class = 'Phud\\'.$namespace.'\\'.$class;
				new $called_class();
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
}
