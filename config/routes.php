<?php
declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes): void {
    $routes->plugin('Kareylo/Comments', ['path' => '/comments'], function (RouteBuilder $routes): void {
        $routes->fallbacks(DashedRoute::class);
    });
};
