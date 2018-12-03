<?php

class MicroKernel
{
    /** @var string */
    private $applicationPath;

    /** @var Configuration $configuration */
    private $configuration;

    /** @var string */
    private $controllerPath;


    public function __construct()
    {
        $this->applicationPath = realpath(ROOT_PATH.'/application');
        $this->configuration   = new Configuration();
        $this->controllerPath  = null;
    }

    public function bootstrap()
    {
        //Activer le chargement automatique des classes de projet. (trad. litt.)
        spl_autoload_register([ $this, 'loadClass' ]);

        //Charger les fichiers de configuration.
        $this->configuration->load('database');
        $this->configuration->load('library');

        //Convertissez toutes les erreurs PHP en exceptions. (trad. litt.)
        error_reporting(E_ALL);
        set_error_handler(function($code, $message, $filename, $lineNumber)
        {
            throw new ErrorException($message, $code, 1, $filename, $lineNumber);
        });

        return $this;
    }

    public function loadClass($class)
    {
        //Activer la prise en charge des espaces de noms de style PSR-4. (trad. litt.)
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if(substr($class, -10) == 'Controller')
        {
            //Ceci est un fichier de classe de contrôleur. (trad. litt.)
            $filename = "$this->controllerPath/$class.class.php";
        }
        else if(substr($class, -4) == 'Form')
        {
            //Ceci est un fichier de classe de formulaire. (trad. litt.)
            $filename = "$this->applicationPath/forms/$class.class.php";
        }
        elseif(substr($class, -5) == 'Model')
        {
            //Ceci est un fichier de classe de modèle. (trad. litt.)
            $filename = "$this->applicationPath/models/$class.class.php";
        }
        else
        {
            //C'est un fichier de classe d'application (en dehors de MVC). (trad. litt.)
            $filename = "$this->applicationPath/classes/$class.class.php";
        }

        if(file_exists($filename) == true)
        {
            /** @noinspection PhpIncludeInspection */
            include $filename;
        }
        else
        {
            if($this->configuration->get('library', 'autoload-chain', false) == false)
            {
                throw new ErrorException
                (
                    "La classe <strong>$class</strong> ne se trouve pas ".
                    "dans le fichier<br><strong>$filename</strong>"
                );
            }
        }
    }

    public function run(FrontController $frontController)
    {
        try
        {
            //Activer la mise en mémoire tampon de sortie. (trad. litt.)
            ob_start();

            //Construisez les données de contexte HTTP. (trad. litt.)
            $requestPath = $frontController->buildContext($this->configuration);

            //Construisez la chaîne du chemin du contrôleur pour le chargement automatique de la classe du contrôleur. (trad. litt.)
            $this->controllerPath = "$this->applicationPath/controllers$requestPath";

            //Exécutez le contrôleur frontal. (trad. litt.)
            $frontController->run();
            $frontController->renderView();

            //Envoyer une réponse HTTP et désactiver la mise en mémoire tampon de sortie. (trad. litt.)
            ob_end_flush();
        }
        catch(Exception $exception)
        {
            //Détruit tout contenu de tampon de sortie qui aurait pu être ajouté. (trad. litt.)
            ob_clean();

            $frontController->renderErrorView
            (
                implode('<br>',
                [
                    $exception->getMessage(),
                    "<strong>Fichier</strong> : ".$exception->getFile(),
                    "<strong>Ligne</strong> : ".$exception->getLine()
                ])
            );
        }
    }
}