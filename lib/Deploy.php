<?php
namespace Phud;

class Deploy
{
	protected $dir_lib = '';
	protected $dir_deploy = '';

	public function __construct($dir_lib, $dir_deploy)
	{
		$this->dir_lib = $dir_lib;
		$this->dir_deploy = $dir_deploy;
	}

	public function deployEnvironment(Server $server)
	{
		// phud framework classes
		Debug::log("including libs");
		$this->readDeploy($server, $this->dir_lib.'/');

		// all the game classes
		Debug::log("including deploy scripts");
		$this->readDeploy($server, $this->dir_deploy.'/init/');

		// instantiate any classes that are traits of Instantiate
		Instantiate::initializeInstances();

		// game is initialized
		$server->fire('initialized');

		// area scripts
		Debug::log("including area scripts");
		$this->readDeploy($server, $this->dir_deploy.'/areas/');

		// finished deployment
		$server->fire('deployed');
	}
	
	protected function readDeploy(Server $server, $start)
	{
		$path = __DIR__.'/../'.$start;
		if(file_exists($path)) {
			$d = dir($path);
			$deferred = [];
			while($cd = $d->read()) {
				if(strpos($cd, '.') === false) {
					$this->readDeploy($server, $start.$cd.'/');
					continue;
				}
				list($class, $ext) = explode('.', $cd);
				if($ext === 'php') {
					$deferred[] = $class;
				} else if($ext === 'area') {
					Debug::log("deploy area ".$path.$cd);
					new Parser($server, $path.$cd);
				}
			}
			foreach($deferred as $class) {
				call_user_func(function() use ($d, $class, $server) {
					require_once($d->path.$class.'.php');
				});
			}
		} else {
			throw new \Exception('Invalid deploy directory defined: '.$start);
		}
	}
}
