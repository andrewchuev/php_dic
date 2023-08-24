<?php
class Container {
	private $services = [];
	private $instances = [];

	public function set($name, $service) {
		$this->services[$name] = $service;
	}

	public function get($name) {
		if (!isset($this->instances[$name])) {
			if (isset($this->services[$name])) {
				$this->instances[$name] = $this->build($this->services[$name]);
			} else {
				throw new Exception("Service not found: " . $name);
			}
		}

		return $this->instances[$name];
	}

	private function build($className) {
		$reflector = new ReflectionClass($className);

		if (!$reflector->isInstantiable()) {
			throw new Exception("Class {$className} is not instantiable");
		}

		$constructor = $reflector->getConstructor();

		if (is_null($constructor)) {
			return new $className;
		}

		$parameters = $constructor->getParameters();
		$dependencies = [];

		foreach ($parameters as $parameter) {
			$dependency = $parameter->getClass();

			if ($dependency === NULL) {
				throw new Exception("Cannot resolve class dependency {$parameter->name}");
			}

			$dependencies[] = $this->get($dependency->name);
		}

		return $reflector->newInstanceArgs($dependencies);
	}
}

$container = new Container();

$container->set('PDO', 'PDO');
$container->set('Database', 'Database');

$db = $container->get('Database');
