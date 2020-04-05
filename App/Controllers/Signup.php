<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        $this->requireNotLogin();

        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

            USER::assignDefaultExpensesToUser($user->email);
            USER::assignDefaultPaymentsToUser($user->email);
            USER::assignDefaultIncomesToUser($user->email);

            Flash::addMessage('Account has been created');

           $this->redirect('/');

        } else {

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);

        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}
