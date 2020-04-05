<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Start extends Authenticated
{

   

    protected function before()
    {
        $this->requireLogin();
    }
    

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
            View::renderTemplate('start/index.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
