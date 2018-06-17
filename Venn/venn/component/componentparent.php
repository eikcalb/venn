<?php

namespace Venn\component;

use Venn\core\Kernel;

abstract class ComponentParent extends Component {

    //  TODO: multiple instances of the same component should not exist!
    protected $children = [];
    private $childComponentPath = [];

    public function addChild(Component $child) {
        if (!empty($child)) {

            if (empty($this->children[$child->getName()])) {
                $this->children[$child->getName()] = [];
            }
            $newId = array_push($this->children[$child->getName()], $child);
            array_push(childComponentPath, $this->generateComponentPath($child->getName(), $newId));
            return $this;
        } else {
            return false;
        }
    }

    public function findChild(Component $child) {
        $name = $child->getName();
        if (array_key_exists($name, $this->children)) {
            for ($i = 0; $i < count($this->children[$name]); $i++) {
                if ($this->children[$name][$i] === $child) {
                    return $i;
                }
            }
        }
        return false;
    }

    public function getChild($i) {
        if (empty($i)) {
            return null;
        }
        if (is_integer($i)) {
            return $this->_parseChildPath($this->childComponentPath[$i]);
        } elseif (is_string($i)) {
            return $this->_parseChildPath($i);
        }
        return $i < count($this->children) && $i > 0 ? $this->children[$i] : false;
    }

    protected function generateComponentPath($group, $id) {
        return "/$group/$id";
    }

    private function _parseChildPath($componentPath) {
        $result = null;
        $name = [strtok($componentPath, '/')];
        $index = [strtok('/')];
        // Remanining path is not used here, only two lwvwls of hierarchy is maintained.
        // Send @see $unhandled relative path down to child for further processing
        $unhandled = strtok(null);
        if (array_key_exists($name, $this->children) && empty($unhandled)) {
            return $this->children[$name][$index];
        } elseif (array_key_exists($name, $this->children) && !empty($unhandled)) {
            return $this->children[$name][$index]->getChild($unhandled);
        } else {
            return null;
        }
    }

    public function removeChild(Component $child) {
        if (empty($child)) {
            return false;
        }
        if (false !== $oldChild = $this->findChild($child)) {
            unset($this->children[$child->getName()][$oldChild]);
            return $oldChild;
        }
        return false;
    }

    protected function render() {
        
    }

    protected function updateChildren() {
        array_walk_recursive($this->children, function ($value, $keys) {
            if ($value instanceof Component) {
                $value->render();
            }
        });
    }

    public final function route() {
        if (Kernel::getRouter()->route()) {
            $controller = \Venn\request\Request::$current->getResolvedController();
            $this->children[] = $controller;
            echo '<p><p>';
            return $this->render();
        } else {
            return false;
        }
    }

    public function isRootComponent() {
        return false;
    }

}
