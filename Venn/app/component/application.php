<?php

namespace app\component;

use Venn\component\ComponentParent;

class Application extends ComponentParent {

    protected function bootstrap() {
        parent::bootstrap();
    }

    public function isRootComponent() {
        return true;
    }

    protected function render() {
        
    }

}
    