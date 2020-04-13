<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ChangeData;
use \App\Models\Revenues;
use \App\Models\Expenses;
use \App\Models\Delete;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Settings extends Authenticated
{

    public $revenueCategories;
    public $expenseCategories;
    public $payments;

    protected function before()
    {
        $this->requireLogin();
        $this->revenueCategories = Revenues::getCategories($_SESSION['user_id']);
        $this->expenseCategories = Expenses::getCategories($_SESSION['user_id']);
        $this->payments = Expenses::getPayments($_SESSION['user_id']);

    }
    

    
    
    public function indexAction()
    {
            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
    }

    public function deleteRevenueCategoryAction()
    {
   
            Delete::deleteRevenueCategory($_POST['delete_revenue_id']);

            Flash::addMessage('Category has been deleted.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
    
    }

    public function deleteExpenseCategoryAction()
    {

        Delete::deleteExpenseCategory($_POST['delete_expense_id']);

        Flash::addMessage('Category has been deleted.');

        $this->before();

        View::renderTemplate('Settings/index.html', [
            'revenueCategories' => $this->revenueCategories,
            'expenseCategories' => $this->expenseCategories,
            'payments' => $this->payments
        ]);

    }

    public function editExpenseCategoryAction()
    {
        if(empty($_POST['expense_name_edit']))
        {
            Flash::addMessage('Please provide the name, try again.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        } elseif (! empty($_POST['expense_limit_edit'])){

            ChangeData::editExpenseCategoryWithLimit($_POST['edit_expense_id'], $_POST['expense_name_edit'], $_POST['expense_limit_edit']);

            Flash::addMessage('Category has been edited.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        } else {

            ChangeData::editExpenseCategoryWithoutLimit($_POST['edit_expense_id'], $_POST['expense_name_edit']);

            Flash::addMessage('Category has been edited.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        }
    }

    public function changeEmailAction()
    {
        $email = $_POST['email'];

        if((ChangeData::validateEmail($email)) || ($email == '')){

            $errorEmail = "Invalid email or already taken.";

            View::renderTemplate('Settings/index.html', [
                'errorEmail' => $errorEmail
            ]);

        } else {

            ChangeData::changeEmail($email);

            Flash::addMessage('Email has been changed.');

            $this->redirect('/Settings');
        }
        
    }

    public function addRevenueCategoryAction()
    {
        if(ChangeData::revenueCategoryExists($_POST['newCategory']))
        {
            Flash::addMessage('Something went wrong, try again.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        } else {

            ChangeData::addRevenueCategory($_POST['newCategory']);

            Flash::addMessage('Category has been added.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        }
        
    }

    public function addExpenseCategoryAction()
    {
        if(ChangeData::expenseCategoryExists($_POST['nameCategory']))
        {
            Flash::addMessage('Something went wrong, try again.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        } elseif((! ChangeData::expenseCategoryExists($_POST['nameCategory'])) && (empty($_POST['limitCategory']))){

            ChangeData::addExpenseCategory($_POST['nameCategory']);

            Flash::addMessage('Category has been added.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        }else{

            ChangeData::addExpenseCategoryWithLimit($_POST['nameCategory'], $_POST['limitCategory']);

            Flash::addMessage('Category has been added.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);

        }
    }
    

}
            