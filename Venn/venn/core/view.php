<?php

namespace Venn\core;

/**
 * Description of view
 *
 * @author LORD EIKCALB
 */
class View {

    private $twig;
    private static $instance;

    private function __construct($loader = null) {
        if ($loader == null) {
            $loader = new \Twig_Loader_Chain([new \Twig_Loader_Filesystem(SERVICE_ROOT_DIRECTORY . "app" . DIRECTORY_SEPARATOR . "view")]);
        }
        $this->twig = new \Twig_Environment($loader, ["cache" => false]);
    }

    public static final function render($input, array $data) {
        self::noop();
        return self::$instance->twig->render($input, $data);
    }

    private static function noop() {
        if (self::$instance == null) {
            self::$instance = new View();
        }
    }

}
