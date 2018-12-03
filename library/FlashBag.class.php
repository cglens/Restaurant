<?php

//but : gestion des messages 'votre commande est prete' ...

class FlashBag
{
    public function __construct()
    {
        //s'il n'y a pas de session
        if(session_status() == PHP_SESSION_NONE)
        {
            //on en demarre une
            session_start();
        }

        //si la session ne contient pas de message
        if(array_key_exists('flash-bag', $_SESSION) == false)
        {
            //on en crée un
            $_SESSION['flash-bag'] = array();
        }
    }

    public function add($message)
    {
        //ajoute le messsage a la fin de la session
        array_push($_SESSION['flash-bag'], $message);
    }

    public function fetchMessage()
    {
        //renvoie le message le plus ancien
        return array_shift($_SESSION['flash-bag']);
    }

    public function fetchMessages()
    {
        //récupère tous les messages de la session
        $messages = $_SESSION['flash-bag'];

        //vide la session
        $_SESSION['flash-bag'] = array();

        //renvoie les messages
        return $messages;
    }

    public function hasMessages()
    {
        //renvoie si la sessio contient des messags ou non (= verification)
        return empty($_SESSION['flash-bag']) == false;
    }
}