<?php

namespace service;

abstract class Service {

    /**
     * This contains initialization options for curl and should <b><i>not</i></b> be used by subclasses directly
     * @var array 
     */
    protected static $curl_opt = [];
    protected static $details = [];
    protected $resource = null;

    /**
     *  These are the general curl settings for microservices
     * 
     *  The kernel @see \core\Kernel::load() load function calls this function on each of the children classes
     *  It returns the cURL object to the child class or further manipulation then execution
     */
    public static function init($request, $path, $details) {
        $curl = curl_init(str_replace('https', 'http', $request) . $path);
//        empty($details['content_type']) ? $options[CURLOPT_HTTPHEADER][] = "Content-Type: application/json" : $options[CURLOPT_HTTPHEADER][] = "Content-Type: " . $details['content-type'];
        call_user_func("self::" . $details['method']);
        Service::base($curl);
        self::$details = $details;
        return $curl;
    }

    private static final function base($curl) {
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($curl, CURLOPT_DEFAULT_PROTOCOL, "https://");
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    }

    private static function delete() {
        Service::$curl_opt[CURLOPT_CUSTOMREQUEST] = "DELETE";
        Service::$curl_opt[CURLOPT_UPLOAD] = FALSE;
    }

    private static function get() {
        Service::$curl_opt+=[
            CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_UPLOAD => FALSE
        ];
    }

    private static function head() {
        Service::$curl_opt+=[
            CURLOPT_CUSTOMREQUEST => "HEAD", CURLOPT_UPLOAD => FALSE
        ];
    }

    private static function patch() {
        Service::$curl_opt+=[
            CURLOPT_CUSTOMREQUEST => "PATCH", CURLOPT_UPLOAD => TRUE
        ];
    }

    private static function post() {
        Service::$curl_opt[CURLOPT_CUSTOMREQUEST] = "POST";
        Service::$curl_opt[CURLOPT_UPLOAD] = FALSE;
    }

    private static function put() {
        Service::$curl_opt+=[
            CURLOPT_CUSTOMREQUEST => "PUT", CURLOPT_UPLOAD => TRUE
        ];
    }

    protected function __construct($curl) {
        $this->resource = $curl;
    }
    
    protected function __destruct() {
        ;
    }

}
