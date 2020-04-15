<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ChangeData;
use \App\Models\Revenues;
use \App\Models\Expenses;
use \App\Models\Delete;
use \App\Models\User;

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
        $email = $_POST['newEmail'];

        if((ChangeData::validateEmail($email)) || ($email == '')){

            Flash::addMessage('Invalid email or already taken.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);

        } else {
            ChangeData::changeEmail($email);

            Flash::addMessage('Email has been changed.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        }
        
    }

    public function addRevenueCategoryAction()
    {
        if(ChangeData::revenueCategoryExists($_POST['newCategory']))
        {
            Flash::addMessage('This category already exists, try again.', Flash::WARNING);

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
            Flash::addMessage('This category already exists, try again.', Flash::WARNING);

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

    public function deletePaymentMethodAction()
    {
        
        Delete::deletePaymentMethod($_POST['delete_payment_id']);

        Flash::addMessage('Method has been deleted.');

        $this->before();

        View::renderTemplate('Settings/index.html', [
            'revenueCategories' => $this->revenueCategories,
            'expenseCategories' => $this->expenseCategories,
            'payments' => $this->payments
        ]);

    }

    public function addPaymentMethodAction()
    {
        
        if(ChangeData::methodPaymentExists($_POST['newMethod'])){

            Flash::addMessage('This method already exists, try again.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);

        } else {
          
            ChangeData::addPaymentMethod($_POST['newMethod']);

            Flash::addMessage('Method has been added.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);

        }
    }

    public function changeNameAction()
    {
        if (empty($_POST['newName'])) {

            Flash::addMessage('Please provide the name, try again.', Flash::WARNING);

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        } else {

            ChangeData::changeName($_POST['newName']);

            Flash::addMessage('Name has been changed.');

            $this->before();

            View::renderTemplate('Settings/index.html', [
                'revenueCategories' => $this->revenueCategories,
                'expenseCategories' => $this->expenseCategories,
                'payments' => $this->payments
            ]);
        }
    }

    public function changePasswordAction()
    {
        if(User::authenticatePasswordbById($_SESSION['user_id'], $_POST['oldPassword'])){
            
            if($_POST['newPassword1'] == $_POST['newPassword2']){

                ChangeData::editPassword($_POST['newPassword1']);

                Flash::addMessage('Password has been changed.');

                $this->before();

                View::renderTemplate('Settings/index.html', [
                    'revenueCategories' => $this->revenueCategories,
                    'expenseCategories' => $this->expenseCategories,
                    'payments' => $this->payments
                ]);
            } else {

                Flash::addMessage('Passwords are different.', Flash::WARNING);

                $this->before();

                View::renderTemplate('Settings/index.html', [
                    'revenueCategories' => $this->revenueCategories,
                    'expenseCategories' => $this->expenseCategories,
                    'payments' => $this->payments
                ]);
            }
        } else {

                Flash::addMessage('Old password is invalid.', Flash::WARNING);

                $this->before();

                View::renderTemplate('Settings/index.html', [
                    'revenueCategories' => $this->revenueCategories,
                    'expenseCategories' => $this->expenseCategories,
                    'payments' => $this->payments
                ]);

        }
    }
    

}
            