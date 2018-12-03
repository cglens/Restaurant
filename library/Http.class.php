<?php

//but : definir l'URL ?

class Http
{
	private $requestMethod;
	private $requestPath;


	public function __construct()
	{
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];

		//si $_SERVER['PATH_INFO'] est différent de nul ou vaut '/'
		if(isset($_SERVER['PATH_INFO']) == false || $_SERVER['PATH_INFO'] == '/')
		{
			//on lui donne la valeur NULL
			$this->requestPath = null;
		}
		else
		{
			//met le chemin en minuscule (?)
			$this->requestPath = strtolower($_SERVER['PATH_INFO']);
		}
	}

	public function getRequestFile()
	{
		//si requestPath est null
		if($this->requestPath == null)
		{
			//on defini donc la page par defaut : home
			//retourne 'home'
			return 'Home';
		}

		//sépare les elements de requestPath par des '/'
        $pathSegments = explode('/', $this->requestPath);

        //aray_pop : recupere la derniere valeur du tableau, retourne null si ce n'est pas un tableau ou qu'il est vide
        //si cette derniere vaut null
        if(($pathSegment = array_pop($pathSegments)) == null)
        {
            //Une barre oblique de fin a été ajoutée à l'URL, supprimez-la. (trad. litt.)
            $pathSegment = array_pop($pathSegments);
        }

        //retourne pathSegment avec le premier caractère en majuscule
        return ucfirst($pathSegment);
	}

	public function getRequestMethod()
	{
		return $this->requestMethod;
	}

	public function getRequestPath()
	{
		return $this->requestPath;
	}

    public function getUploadedFile($name)
    {
    	//si name est dans le tableau _FILES
        if(array_key_exists($name, $_FILES) == true)
        {
        	//un index 'error' est automatiquement crée dans un tableau lors du telechargement PHP
        	//si ce code correspond a UPLOAD_ERR_OK (=0) c'est qu'il n'y a pas d'erreur
            if($_FILES[$name]['error'] == UPLOAD_ERR_OK)
            {
            	//retourne name
                return $_FILES[$name];
            }
        }

        //ne s'execute seulement si les conditions IF ne sont pas vérifier. c'est donc qu'il y a une erreur
        //memo : on peut fait un affichage de 'error' pour savoir de quel type d'erreur il s'agit
        return false;
    }

    public function hasUploadedFile($name)
    {
      	//si name est dans le tableau _FILES
        if(array_key_exists($name, $_FILES) == true)
        {
        	//s'il n'y a pas d'erreur
            if($_FILES[$name]['error'] == UPLOAD_ERR_OK)
            {
            	//retourne true
                return true;
            }
        }
        //on ne retourne false que s'il y a une erreur
        return false;
    }

    public function moveUploadedFile($name, $path = null)
    {
    	//s'il y a eu une erreur
        if($this->hasUploadedFile($name) == false)
        {
        	//retourne false
            return false;
        }

        //constructin de l'URL de destination
        $filename = WWW_PATH."$path/".$_FILES[$name]['name'];

        //déplace le fichier téléchargé (=[$name]['tmp_name']) dans $filename
		move_uploaded_file($_FILES[$name]['tmp_name'], $filename);

		//retourne l'information 'basename' (=nom de base ?) du chemin du fichier $filename
        return pathinfo($filename, PATHINFO_BASENAME);
    }

	public function redirectTo($url)
	{
		//si le 1er caractere de l'url est '/''
		if(substr($url, 0, 1) !== '/')
		{
			//l'url vaut "/$url"
			$url = "/$url";
		}

		//redicrection vers un chemin défini
		header('Location: http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'].$url);
		//sortie de la fonction
		exit();
	}

	public function sendJsonResponse($data)
	{
		//affiche la representation JSON de la valeur data
        echo json_encode($data);
        //sortie de la fonction
		exit();
	}
}