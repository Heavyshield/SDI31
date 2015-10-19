<?php
	final class SqueezeCLI
	{
		public $buffer;
		public $reply;
		private $cli;

		function __construct($message)
		{
			//$this->setBuffer();
			$this->socket();
			$this->connect();
			$this->sendCLI($message);
			//$this->receiveCLI();
			//$this->conversion();
			//$this->disconnect();
		}

		private function socket()
		{
			if ($_SESSION['socket'] = socket_create(AF_INET, SOCK_STREAM,0))
			{
				//echo "création socket ok \n";
			}
			else
			{
				$errorcode = socket_last_error();
       		    $errormsg = socket_strerror($errorcode);
        		die("Couldn't create socket: [$errorcode] $errormsg \n");
			}

		}

		private function connect()
		{
			if(socket_connect($_SESSION['socket'] , $_SESSION['LMS']['server'] , $_SESSION['LMS']['port'] ))
			{
				//echo "création connexion ok \n";
			}
			else
			{
				$errorcode = socket_last_error();
		        $errormsg = socket_strerror($errorcode);
		        die("Could not connect: [$errorcode] $errormsg \n");
			}


		}

		private function sendCLI($message)
		{
			$cli = $message;
			if(socket_send ($_SESSION['socket'], $cli , strlen($cli) , 0))
			{

				//echo "envoi réussi de $cli\n";
			}
			else
			{
				$errorcode= socket_last_error();
		        $errormsg= socket_strerror($errorcode);
		        die("Could not send data '$cli': [$errorcode] $errormsg \n");

			}

		}
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/*
if($this->buffer .= socket_read($_SESSION['socket'], 2048))  //<--------------------- je veu récuperer ce $buffer
			{
				
				return $this->buffer;
			}

*/


		public function receiveCLI()
		{
			if($this->buffer .= socket_read($_SESSION['socket'], 3000))  //<---------------------  attention la taille initiale de 2048 est insufisant pour les grandes réponses
			{
				$result  = str_replace("%3A",":",$this->buffer);
				$result = str_replace("%20", " ", $result);
				$result = str_replace("%2F", "/", $result);
				$result = str_replace("%26", "&", $result);
				return $result;
				$this->disconnect();
			}
			else
			{
				 $errorcode= socket_last_error();
		        $errormsg= socket_strerror($errorcode);
		        die("Could not recived data '$message': [$errorcode] $errormsg \n");
			}

		}

		public function disconnect()
		{

			socket_close($_SESSION['socket']);
			echo "deconnection\n";
		}

/*
//--------------------------------------------------------------------------------------- Les différentes commandes CLI
		public function foundGenres()
		{
			$request = " genres 0  <LF>\n";
			return $request;
		}

		public function foundAlbums()
		{
			$request = " albums 0 100 tags:Itj\n";
			return $request;
		}
		*/
	}
	
?>