<?php


final class SqueezeTranslation
{
	public $titre;
	public $disque;
	public $ecoute;
	public $album;
	public $artiste;
	public $total_album;
	public $album_ajoute;
	public $album_charge;
	//initialisation des variables
	private $lang;
	private $tag;
	private $second_gen;

	function __construct($lang)
	{
		$this->setLang($lang);

	}
	
	//création des setters / getter
	public function setLang($lang)
	{
		$this->_lang = $lang;
	}

	public function getLang()
	{
		return $this->_lang;
	}

	//retourne la traduction de l'élément $tag en fonction de $lang cf: voir la fonction InitModuleLangue
	function Translate($lang,$tag)
	{	
	return $_SESSION['lang']["$lang"]["$tag"];
	}
	
	public function getTranslation($tag)
	{

		return $this->Translate($this->getLang(),$tag);

/*
		//[0] => $titre, [1] => $disque, [2] => $ecoute, [3] => $album, [4] => $artiste, [5] => $total_album, [6] => $album_ajoutee, [7] => $album_charge 
		$this-> $translation = array($titre, $disque, $ecoute, $album, $artiste, $total_album, $album_ajoute, $album_charge);
		return $translation;
		*/
	}	
	/*
	//appel de la fonction Translate qui stock dans une variable de Session 3 paramètres lang $langue et $tag
	private function translation($lang, $tag)
	{
		$titre = $this->Translate($lang,$tag);
		$disque = $this->Translate($_SESSION['selected']['lang'],'mus_disque');
		$ecoute = $this->Translate($_SESSION['selected']['lang'],'mus_ecoute');
		$album = $this->Translate($_SESSION['selected']['lang'],'mus_album');
		$artiste = $this->Translate($_SESSION['selected']['lang'],'mus_artiste');
		$total_album = $this->Translate($_SESSION['selected']['lang'],'mus_total_album');
		$album_ajoute = $this->Translate($_SESSION['selected']['lang'],'mus_album_ajoute');
		$album_charge = $this->Translate($_SESSION['selected']['lang'],'mus_album_charge');	
	}
*/

}


?>