<?php
//fonction divers----------------------------------------------------------------------------------------------------------
		
		function get_string_between($string, $start, $end)
		{
			$string = " ".$string;
			$ini = strpos($string,$start);
			if ($ini == 0) return "";
			$ini += strlen($start);   
			$len = strpos($string,$end,$ini) - $ini;
			return substr($string,$ini,$len);
		}

		function decodeAscii($parameter)
		{
			$replacement = array(' ','\'','_','-',',');
			$patterns = array('%20','%27','%5F','%2D','%2C');
			$parameter = str_replace($patterns, $replacement, $parameter);
			return $parameter;
		}

		function encodeAscii($parameter)
		{
			$patterns = array(' ','\'','_','-','/',':');
			$replacement = array('%20','%27','%5F','%2D','%2F','%3A');
			$parameter = str_replace($patterns, $replacement, $parameter);
			return $parameter;
		}

//les fonctions types databases------------------------------------------------------------------------------------------------------
		function getGenres() //->ok
		{
			$request = " genres 0 \n";
			return $request;
		}

		function listGenres($parameter)
		{
			$parameter = substr($parameter, 0,strpos($parameter, 'count'));
			$parameter = substr($parameter, strrpos($parameter, 'genres 0  '));

			$tabParameter = split("id:", $parameter);
			array_shift($tabParameter);

			//Formate une div par genre qui contiendra les <ul> des albums
			foreach ($tabParameter as $key => $value) 
			{

					list($genreId,$genre)=explode(" genre:", $tabParameter[$key]);

					echo "<ul id=\"$genre$genreId\">";
					//echo "<li class=\"genre\" id=\"$genre$genreId\">$genre</li>";
					echo " <a class=\"btn btn-info\" href=\"#\" id=\"$genre$genreId\">$genre</a>";
					echo "<a class=\"btn btn-warning buttonAddGenre\" href=\"#\" id=\"$genreId\"> <span class=\"glyphicon glyphicon-plus\"></span> </a>";
					echo "</ul>";
			}

			if (empty($tabParameter)) 
			{
				echo "<li> Erreur aucun genre n'est disponible </li>";

			}
			

		}

function setAlbumsGenre() //surcouche car les albums uniques n'ont pas de genre, la variable a pour key l'id de l'album et pour value le genre !
		{
			//récupères tous les titres tags important e -> id de l'album, p-> l'id du genre
			$request = " titles 0 1000 count tags:lgep \n";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();


			$tabSplit = split(" id:", $response);
			//Suppression de la premiere valeur correspondant à la requête
			array_shift($tabSplit); 
			$lenght = count($tabSplit) - 1;
			$tabSplit[$lenght] = substr($tabSplit[$lenght], 0, strrpos($tabSplit[$lenght], "count:")); //suppression du count à la fin
			$_SESSION['AlbumGenre'] = array();
			//print_r($tabSplit);

			foreach ($tabSplit as $key => $value) 
			{
				//Le passage de $album en key permet d'automatiquement supprimer les doublons puisque il ne peut y avoir 2 clés identiques
				//$genre = get_string_between($value,"genre:"," album_id:");
				$albumId = get_string_between($value," album_id:"," genre_id:");
				$genreId = substr($value, strrpos($value, " genre_id:"));
				$genreId = substr( $genreId , 10 );

				$_SESSION['AlbumGenre'][$albumId] = $genreId;
				$_SESSION['AlbumGenre'][$albumId] = trim( $_SESSION['AlbumGenre'][$albumId]);

			}

			//echo "le tableau \n";
			//print_r($_SESSION['AlbumGenre']);

		}

		function getAllAlbums() //récupère tous les albums ->ok
		{

			$request = " albums 0 100 tags:Itj\n";
			return $request;
		}

		function getAlbumsByIdGenre($parameter) //récupère en paramètre l'id du genre, extrait les id des albums correspondant dans la SESSION
		{

			//echo "contenu du parametre $parameter \n";
			$tabParameter = explode(" ", $parameter);
			$genre = $tabParameter[1];
			//print_r($tabParameter);
			//echo "Id du genre $genre \n";
			//print_r($_SESSION['AlbumGenre']);

			$tabResponse = array();
			//echo "tableau des albums genre";
			//print_r($_SESSION['AlbumGenre']);
			//echo "la premiere valeur correspondant a hiphop ".$_SESSION['AlbumGenre'][14] ;

			foreach ($_SESSION['AlbumGenre'] as $key => $value) 
			{
				//echo "key $key \n value $value \n genreId $genre \n";
				if ($value == $genre) 
				{
					//echo "entré dans le if \n";
					$request = " albums 0 1000 album_id:$key tags:Itj\n";
					$mySqueezeCLI = new SqueezeCLI($request);
					$response = $mySqueezeCLI->receiveCLI();
					//echo "les info de l'album en fonction de son id $response \n";
					array_push($tabResponse, $response);
				}
			}
			//print_r($tabResponse);
			//echo "le tableau";
			//print_r($tabResponse);
			return $tabResponse;
		}

		function getAlbumsById($parameter) //récupère les infos de l'Album correspondant à l'album_id passé en paramètre
		{

			//echo "contenu du parametre $parameter \n";
			//$tabParameter = explode(" ", $parameter);
			$request = " albums 0 1000 album_id:$parameter tags:Itj\n";
			//echo " contenu de request:$request";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			return $response;
		}

		function listAlbumsGenre($parameter)
		{
			$albumId = get_string_between($parameter," id:"," artwork_track_id:");
			$title = get_string_between($parameter," title:"," count:");
			$artwork = get_string_between($parameter," artwork_track_id:"," title:");



			echo "<ul id=\"$title|$albumId\">";
			echo " <a class=\"btn btn-info\" href=\"#\" id=\"$title|$albumId\">$title</a>";
			echo "<img class=\"album\" id=\"$artwork\" src=\"http://".$_SESSION['LMS']['server'].":9000/music/$artwork/cover_96x96_p.png\"/>";
			echo "<a class=\"btn btn-warning buttonAddGenre\" href=\"#\" id=\"$genreId\"> <span class=\"glyphicon glyphicon-plus\"></span> </a>";
			echo "</ul>";


		}


		function listAlbums($parameter)
		{
			//echo "la valeur a formater : $parameter \n";
			//$parameter = substr($parameter, 0,strpos($parameter, 'count'));
			//$parameter = substr($parameter, strrpos($parameter, 'genres 0  '));
			$tabParameter = split(" id:", $parameter);
			//print_r($tabParameter);
			$i = 2;
			$len = count($tabParameter);
			//print_r($tabParameter);

			foreach ($tabParameter as $key => $value) 
			{
				if ($key > 0 && $i < $len ) 
				{
					//$id = get_string_between($value,"id:"," artwork_track_id");
					$id = substr($value, 0,strpos($value, " "));
					$artwork = get_string_between($value,"artwork_track_id:"," title:");
					$title = substr($value,strpos($value, "title:"));
					//echo "contenu de title: $title";
					//$title = substr_replace($title, '', strpos($title, ' count:'));
					//echo "titre après replace: $title";
					//$title = substr_replace($value, '', strpos($value, "count"));
					


				//list($id,$genre)=explode(" genre:", $tabParameter[$key]);
					echo "<li id=\"$title$id\">$title</li>";
					echo "<img id=\"$artwork\" src=\"http://".$_SESSION['LMS']['server'].":9000/music/$artwork/cover_96x96_p.png\"/>";
					$i++;
					//echo "i et len : $i $len";
				}

				elseif ($i == $len)
				 {
					//$id = get_string_between($value,"id:"," artwork_track_id");
					$id = substr($value, 0,strpos($value, " "));
					$artwork = get_string_between($value,"artwork_track_id:"," title:");
					$title = substr($value,strpos($value, "title:"));
					
					//echo "contenu de title: $title";


				//list($id,$genre)=explode(" genre:", $tabParameter[$key]);
					echo "<li class=\"album\" id=\"$title|$id\">$title</li>";
					echo "<img class=\"album\" id=\"$artwork\" src=\"http://".$_SESSION['LMS']['server'].":9000/music/$artwork/cover_96x96_p.png\"/>";
					$i++;
				 }

				else
				{
					
				}
			}

		}

		function getTitlesAlbum($parameter) //->ok
		{
			$tabParameter = explode("|", $parameter);
			$albumId = $tabParameter[1];

			$request = " titles 0 1000 album_id:$albumId \n";
			//$request = "albums 0 1000 album_id:$tabParameter[1] tags:taj\n";
			
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();


			//echo "les titres de l'album en brut $response \n";
			return $response;
			
		}

		function listTitles($parameter)
		{
			//echo "contenu du parameter:$parameter \n";
			$parameter = decodeAscii($parameter);
			$tabParameter = split(" id:", $parameter);
			//suppression du premier élement qui correspond à la requette
			array_shift($tabParameter);


			foreach ($tabParameter as $key => $value) 
			{
				//$titleId = substr($tabParameter[$key], 0,strpos($tabParameter[$key], ' '));
				$titleId = substr($value, 0, strpos($value, " "));
				//echo "contenu de id $id \n";
				//$artwork = get_string_between($tabParameter[$key],"track_id:"," title");
				//echo "contenu de artwork $artwork \n";
				$title = get_string_between($value,"title:"," genre:");
				//echo "contenu de title $title \n";
				$artist = get_string_between($value," artist:"," album:");

			echo "<ul id=\"$title|$titleId\">";
			echo "<li class=\"genre\" id=\"$title|$titleId\">Titre:$title Artist:$artist</li>";
			echo "<img class=\"button\" id=\"buttonAddTitle\" src=\"1_music/view/images/player/plus.png\">";
			echo "</ul>";


			}



		}




	//les fonctions players	---------------------------------------------------------------------------------------------------------------------------------

		//Retourne si le player est en mode play ou stop
		function playerStatus()
		{
			$request = $_SESSION['mac']." status 0 100 \n ";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			//echo "la réponse non traité : $response \n";
			//parfois le retour CLI change attention
			//$response = get_string_between($response," mode:","rate:");
			$response = substr($response, strpos($response, "mode:"));
			$response = substr($response, 0, strpos($response, " "));
			$response = substr($response, 5);

			return $response;
		}

		function currentTitle()
		{
			$request = $_SESSION['mac']." status 0 10 \n ";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			$title = get_string_between($response,"title:"," genre:");
			$artist = get_string_between($response,"artist:"," album:");
			echo "<li class=\"currentTitle\" id=\"current|$title\">Titre:$title Artist:$artist</li>";
		}

		function playerCount() //->ok
		{
			$request = " player count ?\n";
			return $request;
		}

		function play()
		{
			$request = $_SESSION['mac']." play\n";
			return $request;
		}

		function pause()
		{
			$request = $_SESSION['mac']." pause\n";
			return $request;
		}

		function stop()
		{
			$request = $_SESSION['mac']." stop\n";
			return $request;
		}

		function volumeUp()
		{
			$request = $_SESSION['mac']." mixer volume +5\n";
			return $request;
		}

		function volumeDown()
		{
			$request = $_SESSION['mac']." mixer volume -5\n";
			return $request;
		}

		function setVolume($parameter)
		{
			$request = $_SESSION['mac']." mixer volume $parameter\n";
			return $request;
		}

		function getVolume()
		{
			$request = $_SESSION['mac']." mixer volume ?\n";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			$response = substr($response, strpos($response, "volume "));
			$response = substr($response, 7);
			return $response;
		}

		//les fonctions playlist------------------------------------------------------------------------------------------------------------------------------------
		function newPlaylist($parameter)//Création d'une playlist, $parameter correspond au nom de la playlist, ici nous utiliserons currentPlaylist. ->ok
		{
			$request = $_SESSION['mac']." playlists new name:$parameter\n";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			$playlistId = substr($response, strrpos($response, "playlist_id:"));
			$playlistId = substr($playlistId, 12,-1);
			return $playlistId;
		}

		function clearPlaylist()//remise a 0 de la variable de Session pour éviter les doublons ->
		{
			$_SESSION['currentPlaylist'] = array();
		}

		function copyCurrentPlaylist($parameter)//Récupère les id des titres dans $_SESSION['currentPlaylist'] pour faire des addTitle dans un foreach, $parameter corresponde a l'id de la playlist fournit par newPlaylist
		{
			//syntaxe juste de la requête:playlists edit cmd:add playlist_id:52 url:file%3A%2F%2F%2Fvar%2Fwww%2Fmedia%2Fmusic%2F01%2520Around%2520The%2520World.mp3
			//syntaxe de la requete envoyé:playlists edit cmd:add playlist_id:52 url:file3A%2F%2F%2Fvar%2Fwww%2Fmedia%2Fmusic%2F8%2520Mile%2F1%2D09%2520Time%2520Of%2520My%2520Life.mp3%20
			//echo "contenu du parameter: $parameter \n";
			//echo "contenu de la variable de session currentPlaylist \n";
			//print_r($_SESSION['currentPlaylist']);
			foreach ($_SESSION['currentPlaylist'] as $key => $value) 
			{
				//echo "avant encodage ascci: $value \n";
				$url = encodeAscii($value);
				//$url = substr($url, 1);
				//echo "contenu de url après encodage: $url \n";
				$request = "playlists edit cmd:add playlist_id:$parameter url:$url  \n";
				//echo "contenu de la requete: $request \n";
				$mySqueezeCLI = new SqueezeCLI($request);
				//echo "contenu de request: $request \n";
			}
		}

		function addAlbum($parameter)
		{
		//le paramètre est l'id d'un album
			$request = $_SESSION['mac']." playlistcontrol cmd:add album_id:$parameter\n";
			$mySqueezeCLI = new SqueezeCLI($request);
			return true;
		}

		function removeAlbum($parameter)
		{
		//le paramètre est l'id d'un album
			$request = $_SESSION['mac']." playlistcontrol cmd:delete album_id:$parameter\n";
			$mySqueezeCLI = new SqueezeCLI($request);
			return true;
		}

		function loadGenre($parameter)
		{
			$request = $_SESSION['mac']." playlist loadalbum $parameter\n";
			return $request;
		}

		//syntaxe pour rajouter un titre b8:27:eb:fd:65:48 playlist add %2Fvar%2Fwww%2Fmedia%2Fmusic%2F02%20No%20Woman%20No%20Cry.mp3
		function addTitle($parameter)
		{
			$patterns = array(' ','\'','_','-');
			$replacement = array('%20','%27','%5F','%2D');
			$parameter = str_replace($patterns, $replacement, $parameter);
			$request = $_SESSION['mac']." playlist add %2Fvar%2Fwww%2Fmedia%2Fmusic%2F$parameter\n";
			return $request;
		}

		function loadTitle($parameter)
		{
			$patterns = array(' ','\'','_','-');
			$replacement = array('%20','%27','%5F','%2D');
			$parameter = str_replace($patterns, $replacement, $parameter);
			$request = $_SESSION['mac']." playlist load %2Fvar%2Fwww%2Fmedia%2Fmusic%2F$parameter\n";
			return $request;
		}


		function deleteTitle($parameter) // suppression du titre de la playlist, le parameter étant l'id du titre
				{
					//echo "l'id du titre a supprimer";
					$request = $_SESSION['mac']." playlistcontrol cmd:delete track_id:$parameter\n";
					$mySqueezeCLI = new SqueezeCLI($request);
					
					return true;
				}



		function loadAlbum($parameter)
		{

			$patterns = array(' ','\'','_','-');
			$replacement = array('%20','%27','%5F','%2D');
			$parameter = str_replace($patterns, $replacement, $parameter);
			$request = $_SESSION['mac']." playlist loadalbum * * $parameter\n";
			return $request;
		}

		function getPlaylistId() //retourne l'id de la premiere (et seul) playlist donc la current->ok
		{
			$request = "playlists count\n";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			$replacement = array(' ','\'','_','-');
			$patterns = array('%20','%27','%5F','%2D');
			$response = str_replace($patterns, $replacement, $response);
			$response = get_string_between($response,'id:',' ');
			return $response;



		}

function getPlaylist($parameter) // envoi une CLI pour récupérer toutes les informations de la current playlist, titre, artist, index, id , url, le formate dans des balises li + stocl les urls dans une variables de session pour plus tard
		{
			//retrait du parametre u pour réduire la taille du buffer
			$request = $_SESSION['mac']." status 0 10000 playlist_id:$parameter tags:agl\n";
			//echo "contenu requete getPlaylsit: $request";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			//echo "la reponse brut: $response \n";
			$response = decodeAscii($response);

			$indexPlaylist = split("playlist index:", $response);
			array_shift($indexPlaylist);
		
			//Clear de la variable de SESSION
			clearPlaylist();
			


			foreach ($indexPlaylist as $key => $value) 
			{

				$id = get_string_between($indexPlaylist[$key],"id:"," title");

				$title = get_string_between($indexPlaylist[$key],"title:"," artist:");
				$artist = get_string_between($indexPlaylist[$key],"artist:"," genre:");
				$genre = get_string_between($indexPlaylist[$key],"genre:"," album:");
				$album = get_string_between($indexPlaylist[$key],"album:"," url:");
				$url = substr($indexPlaylist[$key], strrpos($indexPlaylist[$key], " url:"));
				$url = substr($url, 5);
				//echo "contenu de url: $url \n";
				// Attention ici est sauvegardée en session les url des titres de la current playlist, ils seront utilisés pour faire une copie de la playlist
				$_SESSION['currentPlaylist'][$key] = $url;
				
				echo "<ul class=\"title\" id=\"$id\">";
				echo "<li id=\"$title\">$title</li>";
				echo "<li id=\"$artist\">$artist</li>";
				echo "<li id=\"$genre\">$genre</li>";
				echo "<li id=\"$album\">$album</li>";
				echo "<img class=\"buttonDeleteTitle\" id=\"\" src=\"1_music/view/images/player/delete.png\">";
				echo "</ul>";
			}


		}

		//Les fonctiond Alarm -----------------------------------------------------------------------------------------------------------------------------------------------------
		function setAlarm($parameter) //$parameter prendre la forme day*hours*minutes*secondes, par exemple 0,1,2,3*8*30*10 qui correspond à dimanche,lundi,mardi,mercredi à 8h30 et 10 secondes
		{
			$alarm = explode('*', $parameter);
			$dow = $alarm[0];
			$hours = $alarm[1]*3600;
			$minutes = $alarm[2]*60;
			$secondes = $alarm[3];
			$time = $hours+$minutes+$secondes;

			$request = $_SESSION['mac']." alarm add dow:$dow enabled:1 0 time:$time \n";
			//echo "contenu de request :$request \n";
			$mySqueezeCLI = new SqueezeCLI($request);
			$response = $mySqueezeCLI->receiveCLI();
			//echo "contenu de response: $response \n";
			//$response = decodeAscii($reponse);
			$response = substr($response, strpos($response, "id:"));
			$response = substr($response, 3);
			//echo "contenu de response après traitement: $response \n";
			$_SESSION['alarm'] = $response;
			//echo "contenu de session alarm".$_SESSION['alarm']."\n";
		}

		function deleteAlarm()//$parameter-> id de l'alarm trouvable dans $_SESSION['alarm']
		{
			$request = $_SESSION['mac']." alarm delete id:".$_SESSION['alarm']."\n";
			echo "contenu request delete: $request \n";
			$mySqueezeCLI = new SqueezeCLI($request);
		}
?>