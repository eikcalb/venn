<?php

namespace Venn\service;

class userservice extends \service\Service {

    public static function init($request, $path, $details) {
        $resource = parent::init($request, $path, $details);
        $options = parent::$curl_opt;

        //     change the access_token name to depict access path for curl
//        new \ssl\CurlSSl($resource, $details['access_token'] . "/cert", $details['access_token'] . "/key", $details['access_token'] . "/ca", $details['access_name']); //   use this instead of bearer for authentication

        $options+=[CURLOPT_USERAGENT => $details['app_name'] . ' ' . $details['app_version'],
            CURLOPT_XOAUTH2_BEARER => $details['access_token'], //   change this!
        ];
        empty($details['content_type']) ? $options[CURLOPT_HTTPHEADER][] = "Content-Type: application/json" : $options[CURLOPT_HTTPHEADER][] = "Content-Type: " . $details['content-type'];
        $options[CURLOPT_HTTPHEADER][] = "X-Client-User: " . $details['access_name'];
        $options[CURLOPT_HTTPHEADER][] = "Authorization: Bearer " . password_hash($details['access_token'], PASSWORD_BCRYPT);

        if (!curl_setopt_array($resource, $options) || !self::rebase($resource)) {
            throw new \Exception\CurlException("options cannot be set");
        }

        $instance = new userservice($resource);
        
        var_dump([$options,curl_exec($resource)]);
        echo '<p>';
        return [ curl_getinfo($resource), 'error' => curl_error($resource)]; //[$options,curl_exec($resource)]; , curl_getinfo($resource),  curl_error($resource)];
    }

    private static function rebase($curl) {
        $option = [CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP, //    remove 'http' support. this should only be done via 'https'
            CURLOPT_HEADER => TRUE, //    header is not returned to reduce the overhead and increase performance
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_COOKIEFILE => '',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4|CURL_IPRESOLVE_V6
        ];
        return curl_setopt_array($curl, $option);
    }

}
