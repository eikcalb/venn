<?php
namespace controller;

class CreateProject extends Controller {

    public function start($routeinfo) {
        $manager = new \project\Manager($routeinfo);
    }
}
