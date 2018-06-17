<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */
namespace Venn\request\route;

/**
 * Description of RouteLoader
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
abstract class RouteLoader {

    protected $routes;

    public abstract function addRoute(\Symfony\Component\Routing\Route $route);

    abstract public function getRoutes($params);

    public abstract function removeRoute($route);

    public abstract function setRoutes($routes);

    public abstract function setContext(array $context);
}
