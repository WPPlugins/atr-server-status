<?php

/*
 * Opens a unix domain socket connection (using fsockopen)
 * 
 * You can connect using one of the following two methods 
 * 
  	$con = new SocketConnection();
	$con->set('host', 't3.systematic.co.vu')
		->set('name', 'TeamSpeak3')
		->set('port', '10011')
		->set('timeout', 5)
		->connect();

 	$con = new SocketConnection($hostname, $port, $timeout, $display_name);
	$con->connect();

	$con->write('command'); //Writes query to the file pointer, //returns no. bits written
	$con->read(optional $length); //Reads $length bits from the file pointer, if $length is not provided entire stream is returned
	$con->status(); //Retrieves header/meta data from file pointer
	$con->disconnect(); //Closes the connection
 */
	class SocketConnection {
		private $host, $port, $timeout, $name, $protocol, $block;
		private $pointer, $connect, $connected;
		private $errno, $errstr;

		public function connect() {
			if($this->host === null) {
				throw new Exception('No hostname provided for ' . $this->name);
				return false;
			}

			$this->connect = $this->protocol . $this->host;

			//After many hours consulting the sacred tome of google, I found this to be the best way of silencing fsockopen() upon invalid hostname.
			$this->pointer = @fsockopen($this->connect, $this->port, $this->errno, $this->errstr, $this->timeout);
			
			if(is_resource($this->pointer)) {
				$this->connected = true;
				
				return $this;

			} else {
				$this->connected = false;
				throw new Exception($this->errno.': '.$this->errstr);

				return false;
			}
		}

		public function read($length = null) {
			if($length === null) {
				return fgets($this->pointer);
			} else {
				return fgets($this->pointer, $length);
			}
		}

		public function block($BlockMode) {
			return stream_set_blocking($this->pointer, $BlockMode);	
		}
		
		public function write($command) {
			return fputs($this->pointer, $command);
		}

		public function status() {
			return stream_get_meta_data($this->pointer);
		}

		public function encode($encoding) {
			return stream_encoding($this->socket, $encoding);
		}
		
		public function disconnect() {
			if(is_resource($this->pointer)) {
				fclose($this->pointer);
			}
		}

		public function get($property) {
	        return $this->$property;
	    }

		public function set($property, $value) {
			$this->$property = $value;	
			return $this;
		}

		public function __construct($hostname = null, $port = 80, $timeout = 10, $protocol = 'tcp://') {
			$this->host = $hostname;
			$this->name = 'server';
			$this->port = $port;
			$this->timeout = $timeout;
			$this->protocol = $protocol;

			if(($hostname !== null) && ($hostname !== false)) {
				$this->connect();
			}

			return $this;
		}

		public function __destruct() {
			return $this->disconnect();
		}

		public function __tostring() {
			print_r($this);
		}
	}