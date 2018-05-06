<?php

namespace core;

class Launcher {
    private static $instance;

    private function __construct() {
        $this->initEnvironment();
    }

    /**
     * Application entry point.
     * This function initializes the root component, provided in <var>app.json</var>.
     * 
     * @param string $configPath Path to application configuration file 
     * @throws \Exception\ComponentException Provided bootstrap component must implement @see \component\ComponentParent.
     */
    public static function launch($configPath = null) {
        Launcher::noop();
        /*
         *  Bootstrap application with configuration file.
         *  The returned component becomes the root component for the application.
         */
        $rootComponent = Kernel::bootstrap($configPath);
        if(empty($rootComponent) || !$rootComponent instanceof \component\Component || !$rootComponent instanceof \component\ComponentParent || !$rootComponent->isRootComponent()){
            throw new \Exception\ComponentException("Bootstrap component is not root component");
        }
        /*
         * Runs app initialization
         */
        Launcher::$instance->startApp(Kernel::getAppConfig());
        $rootComponent->route();
        /*
         * Run app cleanup procedures
         */
    }
    
    private static function noop() {
        if(empty(Launcher::$instance)){
            Launcher::$instance = new Launcher();
            return true;
        } else {
            return false;
        }
    }

    private function initEnvironment() {
        
    }

    private function startApp(&$appConfig) {
        
    }

}
