<?php

class Container {
	private $services = [];
	private $instances = [];

	public function set( $name, $service ) {
		$this->services[ $name ] = $service;
	}

	public function get( $name ) {
		if ( ! isset( $this->instances[ $name ] ) ) {
			if ( isset( $this->services[ $name ] ) ) {
				$this->instances[ $name ] = $this->services[$name]();
			} else {
				throw new Exception( "Service not found: " . $name );
			}
		}

		return $this->instances[ $name ];
	}
}

$container = new Container();

$container->set( 'database', function () {
	return new PDO( 'mysql:host=localhost;dbname=test', 'user', 'password' );
} );

$db = $container->get( 'database' );
