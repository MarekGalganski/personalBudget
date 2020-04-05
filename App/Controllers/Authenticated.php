<?php

namespace App\Controllers;

/**
 * Authenticated base controller
 *
 * PHP version 7.0
 */
abstract class Authenticated extends \Core\Controller
{
    
    protected function before()
    {
        $this->requireLogin();
    }
}
