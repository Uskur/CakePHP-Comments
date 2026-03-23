<?php
declare(strict_types=1);

namespace Kareylo\Comments\Controller;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    /**
     * Setup AppController for the plugin
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }
}
