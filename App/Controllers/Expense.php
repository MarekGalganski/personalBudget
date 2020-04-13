<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Expense extends Authenticated
{

   public $categories;
   public $payments;

    protected function before()
    {
        $this->requireLogin();
        $this->categories = Expenses::getCategories($_SESSION['user_id']);
        $this->payments = Expenses::getPayments($_SESSION['user_id']);
    }
    

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
            View::renderTemplate('Expense/index.html', [
              'categories' => $this->categories,
              'payments' => $this->payments
           ]);
         
    }

    
   public function addAction()
   {
     $expense = new Expenses($_POST);

      if ($expense->save()) {

          Flash::addMessage('Expense has been added.');
          $this->redirect('/expense');
 
        } else {

           View::renderTemplate('Expense/index.html', [
           'categories' => $this->categories,
           'payments' => $this->payments,
           'expense' => $expense
           ]);
        }
    }

    
}