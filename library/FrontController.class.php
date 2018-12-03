<?php

class FrontController
{
    private $http;
    private $viewData;

    public function __construct()
    {
        $this->http = new Http();

        //configuration de l'afficage des données
        $this->viewData =
        [
            'template'  => null,
            'variables' =>
            [
                'requestUrl' => $_SERVER['SCRIPT_NAME'],
                'wwwUrl'     => str_replace('index.php', 'application/www', $_SERVER['SCRIPT_NAME'])
            ]
        ];
    }

    public function buildContext(Configuration $configuration)
    {

        // trouver tous les filtres d'interception à charger (trad. litt.)
        $filters = $configuration->get('library', 'intercepting-filters', array());

        //execute tous les filtres d'interception (trad. litt.)
        foreach($filters as $filterName)
        {
            //si filterName est vide
            if(empty($filterName) == true)
            {
                //on continue
                continue;
            }

            //on donne la valeur 'Filter' a filterName
            $filterName = $filterName.'Filter';

            /** @var InterceptingFilter $filter */
            $filter = new $filterName();

            //si la classe a ete instanciée
            if ($filter instanceof InterceptingFilter)
            {
                //Fusionner les variables de filtres d'interception avec les variables de vue. (trad. litt.)
                $this->viewData['variables'] = array_merge
                (
                    $this->viewData['variables'],
                    (array) $filter->run($this->http, $_GET, $_POST)
                );
            }
        }

        return $this->http->getRequestPath();
    }

    public function renderErrorView($_fatalErrorMessage)
    {
        //Injecte les variables du modèle de vue.  (trad. litt.)
        extract($this->viewData['variables'], EXTR_OVERWRITE);

        //Charge le modèle d'erreur puis quitte. (trad. litt.)
        include 'ErrorView.phtml';
        die();
    }

    public function renderView()
    {
        //Construit le chemin complet du modèle et le nom du fichier en utilisant les valeurs par défaut. (trad. litt.)
        $this->viewData['template'] = WWW_PATH.
            $this->http->getRequestPath().DIRECTORY_SEPARATOR.
            $this->http->getRequestFile().'View.phtml';

        //Le contrôleur a-t-il créé un formulaire? (trad. litt.)
        if(array_key_exists('_form', $this->viewData['variables']) == true)
        {
            if($this->viewData['variables']['_form'] instanceof Form)
            {
                // Oui, récupère l'objet formulaire. (trad. litt.)

                /** @var Form $form */
                $form = $this->viewData['variables']['_form'];

                if($form->hasFormFields() == false)
                {
                    //Le formulaire n'a pas encore été construit. (trad. litt.)
                    $form->build();
                }

                //Fusionner les champs de formulaire avec les variables de modèle. (trad. litt.)
                $this->viewData['variables'] = array_merge
                (
                    $this->viewData['variables'],
                    $form->getFormFields()
                );

                // Add the form field error message template variable. (trad. litt.)
                $this->viewData['variables']['errorMessage'] = $form->getErrorMessage();
            }

            unset($this->viewData['variables']['_form']);
        }

        //Injecte les variables du modèle de vue. (trad. litt.)
        extract($this->viewData['variables'], EXTR_OVERWRITE);

        if(array_key_exists('_raw_template', $this->viewData['variables']) == true)
        {
            unset($this->viewData['variables']['_raw_template']);

            //Charge le modèle directement, en contournant la disposition. (trad. litt.)
            /** @noinspection PhpIncludeInspection */
            include $this->viewData['template'];
        }
        else
        {
            //Charge la mise en page qui charge ensuite le modèle. (trad. litt.)
            include WWW_PATH.'/LayoutView.phtml';
        }
    }

    public function run()
    {
        //Détermine la classe du contrôleur de page à exécuter. (trad. litt.)
        $controllerClass = $this->http->getRequestFile().'Controller';

        if(ctype_alnum($controllerClass) == false)
        {
            throw new ErrorException
            (
                "Nom de contrôleur invalide : <strong>$controllerClass</strong>"
            );
        }

        //Crée le contrôleur de page. (trad. litt.)
        $controller = new $controllerClass();

        //Sélectionnez la méthode HTTP GET ou HTTP POST du contrôleur de page à exécuter et les champs de données HTTP à donner à la méthode. (trad. litt.)
        if($this->http->getRequestMethod() == 'GET')
        {
            $fields = $_GET;
            $method = 'httpGetMethod';
        }
        else
        {
            $fields = $_POST;
            $method = 'httpPostMethod';
        }

        if(method_exists($controller, $method) == false)
        {
            throw new ErrorException
            (
                'Une requête HTTP '.$this->http->getRequestMethod().' a été effectuée, '.
                "mais vous avez oublié la méthode <strong>$method</strong> dans le contrôleur ".
                '<strong>'.get_class($controller).'</strong>'
            );
        }

        //Exécutez la méthode du contrôleur de page et fusionnez toutes les variables de vue des contrôleurs. (trad. litt.)
        $this->viewData['variables'] = array_merge
        (
            $this->viewData['variables'],
            (array) $controller->$method($this->http, $fields)
        );
    }
}