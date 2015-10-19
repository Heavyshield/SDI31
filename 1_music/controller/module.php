<?php session_start();

require_once "/var/www/1_music/controller/class.SqueezeCLI.php";
require_once "/var/www/1_music/controller/class.SqueezeTranslation.php";
require_once "/var/www/1_music/controller/function.php";


//!!!!!!!!!!!!
$_SESSION['mac'] = "b8:27:eb:fd:65:48";
$_SESSION['LMS']['port'] = '9090';
$_SESSION['LMS']['server'] = '192.168.1.13';
//$_SESSION['LMS']['server'] = '10.131.50.16';


 if ($_POST['req']=='translation') 
{
	$mySqueezeTranslation = new SqueezeTranslation($_SESSION['selected']['lang']);
	$title =$mySqueezeTranslation->getTranslation('mus_titre');
	$disk =$mySqueezeTranslation->getTranslation('mus_disque');
	$listen =$mySqueezeTranslation->getTranslation('mus_ecoute');
	$album =$mySqueezeTranslation->getTranslation('mus_album');
	$artist =$mySqueezeTranslation->getTranslation('mus_artiste');
	$totalAlbum =$mySqueezeTranslation->getTranslation('mus_total_album');
	$albumAdd =$mySqueezeTranslation->getTranslation('mus_album_ajoute');
	$albumLoad =$mySqueezeTranslation->getTranslation('mus_album_charge');	
	//$arr = array('a'=> $a, 'a'=> $a, 'b'=> $b, 'c'=> $c, 'd'=> $d, 'e'=> $e, 'f'=> $f, 'g'=> $g, 'h'=> $h);
	$translation = array('title'=> $title,'disk'=> $disk,'listen'=> $listen,'album'=> $album,'artist'=> $artist,'totalAlbum'=> $totalAlbum,'albumAdd'=> $albumAdd,'albumLoad'=> $albumLoad);

	echo json_encode($translation);

}



//le POST req fait par l'Ajax doit prendre la forme fonction_paramètre exemple: getTitlesAlbum_8 Mile


//list($action,$parameter)=explode('_', $_POST['req'],1);

$req = explode("_", $_POST['req']);
$action = $req[0];
//echo "contenu de action: $action \n";
$parameter = $req[1];

switch ($action) 
{
	//associe aux albums un genre dans une variable de SESSION
	case 'setAlbumsGenre':
		setAlbumsGenre();
		break;


	//action type database: récupères des informations comme le nombre, d'élements et leurs ID------------------------------------------------------------------------------------
	case 'playerCount': //compte le nombre de player ->ok
		$mySqueezeCLI = new SqueezeCLI(playerCount());
		$response = $mySqueezeCLI->receiveCLI();
		echo $response."\n";
		break;
/*
	case 'getGenres': //retourne tous les genres disponibles en brut->ok
		$mySqueezeCLI = new SqueezeCLI(getGenres());
		$response = $mySqueezeCLI->receiveCLI();
		echo $response."\n";
		break;
*/
	case 'listGenres': //formate le résultat de getGenres en html ->ok
		$mySqueezeCLI = new SqueezeCLI(getGenres());
		//var_dump($mySqueezeCLI);
		$response = $mySqueezeCLI->receiveCLI();
		listGenres($response);
		break;

	case 'getAllAlbums': //retourne tous les albums disponibles (avec nom, artiste, pochette) ->ok
		$mySqueezeCLI = new SqueezeCLI(getAllAlbums());
		$response = $mySqueezeCLI->receiveCLI();
		echo $response."\n";
		break;


	case 'getAlbumsByGenre': //les fichiers au format mp3 n'indique pas forcément le genre de l'album, que l'on peut retrouver dans ses titres exemple: setAlbumsGenre_Hip-hop/Rap 5
		//recherche tous les titres avec genre = parameter, puis recherche leurs albums
		//print_r($tabAlbumsGenre);
		//setAlbumsGenre retourne un tableau contenant key -> album_id | value -> genre
			//echo "contenu de la SESSION AlbumGenre \n";
			//print_r($_SESSION['AlbumGenre']);
			$tabResponse = getAlbumsByIdGenre($parameter);
			//echo "les albums correspondant au genre \n";
			//print_r($tabResponse);

			foreach ($tabResponse as $key => $response) 
			{
				listAlbumsGenre($response);
			}


		break;


	case 'listTitles': //formate le résultat de getTitlesAlbums en html -> ...
		listTitles(getTitlesAlbum($parameter));
		break;

//action de type playlist ------------------------------------------------------------------------------------------------------------------------------------------------------
	//rajoute dans la playlist tous les albums correspondant au genre	
	case 'addGenre': //ajoute tous les albums correspondant au genre indiqué , la key correspond à l'id de l'album et la value au genre
	//pour chaque id d'album, un genre y est attaché, si le genre est le même que celui passé en paramètre l'album est ajouté.
	foreach ($_SESSION['AlbumGenre'] as $key => $value) 
	{
		echo "le parametre de l'album a ajouter : $parameter \n";
		if ($value == $parameter) 
		{
			echo "album ajouté: ".$key."\n";
			addAlbum($key);
		}
		else
		{
			//echo "ne correspond pas au genre demandé";
		}
	}
		echo "ajout des albums";
		break;

	//supprime de la playlsit tous les albums correspondant au genre
	case 'removeGenre':
		foreach ($_SESSION['AlbumGenre'] as $key => $value) 
		{
			echo "le parametre de l'album a supprimer : $parameter \n";
			if ($value == $parameter) 
			{
				echo "album supprimé: ".$key."\n";
				removeAlbum($key);
			}
			else
			{
				//echo " ne correspond pas au genre demandé";
			}
		}
		echo "suppression des albums";
		break;

	case 'loadGenre': //supprime la playlist et ajoute tous les albums correspondant au genre indiqué -> exemple: $parameter=Rock ->ok
		$mySqueezeCLI = new SqueezeCLI(loadGenre($parameter));
		break;

	case 'addAlbum': //ajoute l'album indiqué en paramètre a la fin de la playlist -> exemple :addAlbum_8 Mile ->ok
		addAlbum($parameter);
		break;

	case 'loadAlbum': //supprime la playlist et ajoute l'album indiqué en paramètre -> exemple: loadAlbum_8 Mile ->ok
		$mySqueezeCLI = new SqueezeCLI(loadAlbum($parameter));
		break;

	case 'addTitle': //ajoute le titre en paramètre a la fin de la playlist -> exemple :addTitle_02 No Woman No Cry.mp3 ->ok
		$mySqueezeCLI = new SqueezeCLI(addTitle($parameter));
		break;

	case 'loadTitle': //supprime la playlist, ajoute le titre en paramètre ->  exemple :loadTitle_02 No Woman No Cry.mp3 ->ok
		$mySqueezeCLI = new SqueezeCLI(addTitle($parameter));
		break;

	case 'deleteTitle': //retire un titre de la playlist, attention ici il s'agit des ID de l'index, 0 étant le titre en cour de la playlist-> exemple : deleteTitle_1 ->ok
		deleteTitle($parameter);
		echo "titre supprimé \n";
		break;


	case 'getPlaylist': // récupere la current playlist du media server et la retourne en HTML pour l'ajax ->ok
		$parameter = getPlaylistId(); //Il n'y a qu'une playlist donc le première ID
		getPlaylist($parameter);
		break;
	/*

	$mySqueezeCLI = new SqueezeCLI($_SESSION['mac']." playlist title 8 ?\n");
	echo "la requete ".$_SESSION['mac']." playlist title 8 ?\n";
	$response = $mySqueezeCLI->receiveCLI();
		echo $response."\n";



		*/
		/*
		$_SESSION['currentPlaylist']=array();


		$i = 0;
		//$request = 	$_SESSION['mac']." playlist title $i ?\n";
		while ( true ) 
		{

		$request = 	$_SESSION['mac']." playlist title $i ?\n";
		$mySqueezeCLI = new SqueezeCLI($request);
		$response = decodeAscii($mySqueezeCLI->receiveCLI());
		$response = str_replace($_SESSION['mac']." playlist title $i ", '', $response);
		$_SESSION['currentPlaylist'][$i]['title'] = $response;

		$request = 	$_SESSION['mac']." playlist album $i ?\n";
		$mySqueezeCLI = new SqueezeCLI($request);
		$response = decodeAscii($mySqueezeCLI->receiveCLI());
		$response = str_replace($_SESSION['mac']." playlist album $i ", '', $response);
		$_SESSION['currentPlaylist'][$i]['album'] = $response;

		$request = 	$_SESSION['mac']." playlist genre $i ?\n";
		$mySqueezeCLI = new SqueezeCLI($request);
		$response = decodeAscii($mySqueezeCLI->receiveCLI());
		$response = str_replace($_SESSION['mac']." playlist genre $i ", '', $response);
		$_SESSION['currentPlaylist'][$i]['genre'] = $response;

		$request = 	$_SESSION['mac']." playlist artist $i ?\n";
		$mySqueezeCLI = new SqueezeCLI($request);
		$response = decodeAscii($mySqueezeCLI->receiveCLI());
		$response = str_replace($_SESSION['mac']." playlist artist $i ", '', $response);
		$_SESSION['currentPlaylist'][$i]['artist'] = $response;
		print_r($_SESSION['currentPlaylist']);
		$i++;

		}

		print_r($_SESSION['currentPlaylist']);

	/*
	$i = 0;
	$mySqueezeCLI = new SqueezeCLI(refreshPlaylist("title",$i));
	$response = $mySqueezeCLI->receiveCLI();
		echo "le titre\n";
		echo "la reponse".$reponse."\n";
/*
		$i = 0;
		while ($i <= 10) 
		{
			echo "dans la boucle while\n";
		$mySqueezeCLI = new SqueezeCLI(refreshPlaylist("title",$i));
		$response = $mySqueezeCLI->receiveCLI();
		echo "le titre";
		echo $reponse."\n";
		$mySqueezeCLI = new SqueezeCLI(refreshPlaylist("album",$i));
		$response = $mySqueezeCLI->receiveCLI();
		echo "l'album";
		echo $reponse."\n";
		$mySqueezeCLI = new SqueezeCLI(refreshPlaylist("genre",$i));
		$response = $mySqueezeCLI->receiveCLI();
		echo "le genre";
		echo $reponse."\n";
		$mySqueezeCLI = new SqueezeCLI(refreshPlaylist("artist",$i));
		$response = $mySqueezeCLI->receiveCLI();
		echo "l'artist";
		echo $reponse."\n";
		$i++;
		}
		*/


			
/*
	case 'addTracksTitle':
		$mySqueezeCLI = new SqueezeCLI(addTracksTitle($parameter));
		break;	
		*/
	//action type player: active/désactive met en pause la lecture + le son-------------------------------------------------------------------------
	case 'playerStatus':
	echo playerStatus();
		
		break;
	case 'currentTitle':
		currentTitle();
		break;

	case 'play': //lance le player exemple: play ->ok
		$mySqueezeCLI = new SqueezeCLI(play());
		break;	
	
	case 'pause': //met en pause le player exemple: pause ->ok
		$mySqueezeCLI = new SqueezeCLI(pause());
		break;

	case 'stop': //stop le player exemple: stop ->ok
		$mySqueezeCLI = new SqueezeCLI(stop());
		break;

	case 'volumeUp': //augmente le volume du mixer de +5 ->ok
		$mySqueezeCLI = new SqueezeCLI(volumeUp());
		break;

	case 'volumeDown': //réduit le volume du mixer de -5 ->ok
		$mySqueezeCLI = new SqueezeCLI(volumeDown());
		break;

	case 'setVolume': //set le volume exemple: volumeSet_100 ->ok
		$mySqueezeCLI = new SqueezeCLI(setVolume($parameter));
		break;

	case 'getVolume': //stop le player exemple: stop ->ok
	echo getVolume();
		break;

		//action type alarm: addAlarm, deleteAlarm-------------------------------------------------------------------------------------------------------------

	case 'setAlarm': //pour l'instant l'application ne gère qu'un réveil à la fois, ainsi a chaque fois qu'un reveil est set le précédent est delete -> exemple: setAlarm_0,1,2,3*8*30*10 ->ok
		//echo "entrée dans le case setAlarm \n";
		deleteAlarm();
		setAlarm($parameter);
		break;

	case 'deleteAlarm': //-> ok
		deleteAlarm();
		break;

	default:
		# code...
		break;
}


?>