<?php

	require 'ddbb/credentials.php';
	class Database{
		public static $instance;
		public $conexion;
		function __construct(){
			$this->makeConnection();
			
		}

		public static function getInstance(){
			if(!self::$instance instanceof self){
				self::$instance = new self();
			}
		}
		public function get_database_instance(){
			return $this->conexion;
		}

		public function makeConnection(){
			$this->conexion = new mysqli(URL_DB,USERNAME,PASSWORD,DATABASE);
		}
	}

?>