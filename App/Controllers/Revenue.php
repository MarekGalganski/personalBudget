<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Revenues;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Revenue extends Authenticated
{

   public $categories;

    protected function before()
    {
        $this->requireLogin();
        $this->categories = Revenues::getCategories($_SESSION['user_id']);
    }
    

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
            View::renderTemplate('revenue/index.html', [
                'categories' => $this->categories
            ]);
    }

    
    public function addAction()
    {
        $revenue = new Revenues($_POST);

        if ($revenue->save()) {

            Flash::addMessage('Revenue has been added.');

            $this->redirect('/revenue');
 
         } else {

            View::renderTemplate('revenue/index.html', [
            'categories' => $this->categories,
            'revenue' => $revenue
            ]);
        }
    }

}