<?php

//but : gestion du formulaire d'authentification

abstract class Form
{
    private $errorMessage;
    private $formFields;

    abstract public function build();

    public function __construct()
    {
        //on crée un message d'erreur vide et un tableau vide
        $this->errorMessage = null;
        $this->formFields   = array();
    }

    protected function addFormField($name, $value = null)
    {
        //récupératin du champ
        $this->formFields[$name] = $value;
    }

    public function bind(array $formFields)
    {
        //compilation ?
        $this->build();

        //parcours du formulaire
        foreach($formFields as $name => $value)
        {
            //verification de l'existance dans la BDD
            if(array_key_exists($name, $this->formFields) == true)
            {
                //affectation de la valeur du name a la variable name du tableau formFields
                $this->formFields[$name] = $value;
            }
        }
    }

    //retourne un message d'erreur
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    //retourne le formulaire
    public function getFormFields()
    {
        return $this->formFields;
    }

    //retourne si le formulaire est vide ou non (= verification)
    public function hasFormFields()
    {
        return empty($this->formFields) == false;
    }

    //affect le messageage d'erreur
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}