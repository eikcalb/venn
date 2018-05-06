<?php

/*
 * (c) Agwa Israel Onome<eikcalb.agwa.io>
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of assetmanager
 *
 * @author Agwa Israel Onome<eikcalb.agwa.io>
 */
final class AssetManager {

    private static $instance = null;
    private static $loaders = ['config' => 'config'];

    private function __construct() {
        
    }

    private static function noop() {
        if (empty(static::$instance)) {
            static::$instance = new AssetManager();
        }
    }

    public static function __callStatic($name, $args) {
        static::noop();
        if (($pos = stripos($name, "load")) === 0) {
            $func = strtolower(substr($name, strlen("load")));
            if (!in_array($func, static::$loaders)) {
                throw new \BadMethodCallException("Function does not exist!");
            }
            return static::$instance->load($func, ...$args);
        }
    }

    private function load($target, $file, $exceptionMessage = null) {
        if (!is_string($file)) {
            throw new \InvalidArgumentException("File path must be a string");
        }
        try {
            switch ($target) {
                case static::$loaders['config']:
                    if (is_readable(SERVICE_ROOT_DIRECTORY . $file) && ($data = file_get_contents(SERVICE_ROOT_DIRECTORY . $file))) {
                        return json_decode($data, true);
                    } else {
                        throw new \exception\FileNotFound(!empty($exceptionMessage) && is_string($exceptionMessage) ? $exceptionMessage : "Could not load config {$file}");
                    }
                    break;

                default:
                    break;
            }
        } catch (\Exception $ex) {
            if (!empty($exceptionMessage) && $exceptionMessage instanceof \Exception) {
                throw $exceptionMessage;
            } else {
                throw $ex;
            }
        }
    }

    private function get_file($file) {
        if (is_readable(SERVICE_ROOT_DIRECTORY . $file) && ($data = file_get_contents(SERVICE_ROOT_DIRECTORY . $file))) {
            return $data;
        } else {
            return false;
        }
    }

}
