<?php

namespace app\component;

use component\Component;
use component\ComponentParent;

class Application extends Component implements ComponentParent {

    public function isRootComponent() {
        return true;
    }
    

}
