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

    public function getSumAction()
    {
      $amount = $_REQUEST['amount'];
      $category = $_REQUEST['category'];
      if(empty($amount) || !is_numeric($amount) || $category == "undefined"){
        exit();
      }
      $limit = Expenses::selectLimit($category);
      if(empty($limit['category_limit'])){
        exit();
      }
      $sumExpenses = Expenses::getSumExpensesFromOneCategory($category);
      if(empty($sumExpenses)){
        $sumExpenses = 0;
      }else{
        $sumExpenses = floatval($sumExpenses);
      } 
      $limit = floatval($limit['category_limit']);
      $amount = floatval($amount);
      
      $expensesWithAmount = $amount + $sumExpenses;
      $expenseDifferential = $limit - $expensesWithAmount;

      echo $limit."|".$sumExpenses."|".$expenseDifferential."|".$expensesWithAmount;
      
    }
    
    

    
}