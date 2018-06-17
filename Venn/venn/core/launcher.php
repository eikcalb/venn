<?php

namespace Venn\core;

/**
 * Root control point for application lifecyle
 */
final class Launcher {

    private static $instance;
    private $rootComponent;

    private function __construct() {
        $this->initEnvironment();
    }

    /**
     * Application entry point.
     * This function initializes the root component, provided in <var>app.json</var>.
     * 
     * @param string $configPath Path to application configuration file 
     * @throws \Venn\Exception\ComponentException Provided bootstrap component must implement @see \Venn\component\ComponentParent.
     */
    public static function launch($configPath = null) {
        Launcher::noop();
        /*
         *  Bootstrap application with configuration file.
         *  The returned component becomes the root component for the application.
         */
        $rootComponent = Kernel::bootstrap($configPath);
        if (empty($rootComponent) || !$rootComponent instanceof \Venn\component\ComponentParent || !$rootComponent->isRootComponent()) {
            throw new \Venn\Exception\ComponentException("Bootstrap component is not root component. Use \Venn\component\ComponentParent as a generic parent component!");
        }
        /*
         * Run app initialization
         */
        Launcher::$instance->startApp(Kernel::getAppConfig(), $rootComponent);

        /*
         * Run app cleanup procedures
         */
        Launcher::$instance->stopApp();
    }

    private static function noop() {
        if (empty(Launcher::$instance)) {
            Launcher::$instance = new Launcher();
            return true;
        } else {
            return false;
        }
    }

    private function initEnvironment() {
        
    }

    private function startApp(&$appConfig, $rootComponent) {
        if (!empty($appConfig[Config::STARTUP_SCRIPT]) && file_exists($appConfig[Config::STARTUP_SCRIPT]) && is_executable($appConfig[Config::STARTUP_SCRIPT])) {
            \Amp\Loop::run(function() use (&$appConfig) {
                include $appConfig[Config::STARTUP_SCRIPT];
            });
        } else {
            \Amp\Loop::run();
        }

        $this->rootComponent = $rootComponent;
        $this->rootComponent->route();
    }

    private function stopApp() {
        \Amp\Loop::stop();
    }

}
