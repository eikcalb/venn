<?php

namespace app\component;

use Venn\component\Component;
use Venn\component\ComponentParent;

class Application extends Component implements ComponentParent {

    public function isRootComponent() {
        return true;
    }

    public function route() {
        parent::route();
    }

}
    