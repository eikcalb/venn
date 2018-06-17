<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */

namespace Venn\core;

/**
 * Description of InterceptorManager
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
final class InterceptorManager {

    private $preInterceptors = [], $postInterceptors = [];

    public function __construct() {
        
    }

    public function invokePre() {
        foreach ($this->preInterceptors as $intercept) {
            $this->invoke($intercept);
        }
    }

    public function invokePost() {
        foreach ($this->postInterceptors as $intercept) {
            
        }
    }

    private function invoke(\Venn\request\interceptor\Interceptor $interceptor) {
        $interceptor->intercept();
    }

}
