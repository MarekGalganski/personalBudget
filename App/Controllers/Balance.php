<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Revenues;
use \App\Models\Expenses;
use \App\Models\Delete;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Balance extends Authenticated
{

   public $categories;

    protected function before()
    {
        $this->requireLogin();
    }
    

    public function indexAction()
    {
        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);

        $groupedRevenues = Revenues::getGroupedRevenues($first_day_this_month, $last_day_this_month);
        $groupedExpenses = Expenses::getGroupedExpenses($first_day_this_month, $last_day_this_month);

        $balance = $sumRevenues-$sumExpenses;

        
        
        View::renderTemplate('Balance/index.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance,
            'groupedRevenues' => $groupedRevenues,
            'groupedExpenses' => $groupedExpenses
            ]);
    }

    public function balancePreviousMonthAction()
    {
        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balancePreviousMonth.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance
        ]);
    }

    public function balanceCurrentYearAction()
    {
        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balanceCurrentYear.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance
        ]);
    }

    public function balanceNonStandardAction()
    {
        
            View::renderTemplate('Balance/balanceNonStandard.html');
    }

    public function addDateAction()
    {
            $first_day = $_POST['date1'];
            $last_day = $_POST['date2'];

            if((! Revenues::validateDate($first_day)) || (! Revenues::validateDate($last_day))){

                $error = 'Please provide the date in "YYYY-MM-dd" .';

                View::renderTemplate('Balance/balanceNonStandard.html', [
                    'error' => $error
                ]);
               
            }else{

                $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day, $last_day);
                $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day, $last_day);

                $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day, $last_day);
                $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day, $last_day);

                $balance = $sumRevenues-$sumExpenses;

                View::renderTemplate('Balance/balanceNonStandard.html', [
                    'revenues' => $revenues,
                    'expenses' => $expenses,
                    'sumRevenues' => $sumRevenues,
                    'sumExpenses' => $sumExpenses,
                    'balance' => $balance,
                    'first_day' => $first_day,
                    'last_day' => $last_day
                ]);
            }

    }

    public function getBalanceToCharCurretMonthAction(){
        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        
        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);
        
        $dataPoints = array(
            array("y"=> $sumRevenues, "label"=> "Revenues"),
            array("y"=> $sumExpenses, "label"=> "Expenses")
        );

        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getReveuesToCharCurrentMonthAction(){
        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        $revenues = Revenues::getGroupedRevenues($first_day_this_month, $last_day_this_month);;
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($revenues);$i++){
            array_push($dataPoints,array("y"=> $revenues[$i]['SUM(amount)'], "label"=> $revenues[$i]['income_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getExpensesToCharCurrentMonthAction(){
        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        $expenses = Expenses::getGroupedExpenses($first_day_this_month, $last_day_this_month);
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($expenses);$i++){
            array_push($dataPoints,array("y"=> $expenses[$i]['SUM(amount)'], "label"=> $expenses[$i]['expense_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getAction(){
        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        $arr = Revenues::getGroupedRevenues($first_day_this_month, $last_day_this_month);
        
        var_dump($arr);
    }
    
    public function deleteSingleRevenueCurrentMonthAction(){
        
        Delete::deleteSingleRevenue($_POST['id']);
        Flash::addMessage('Revenue has been deleted.');

        $this->indexAction();

    }

    public function deleteSingleExpenseCurrentMonthAction(){
        
        Delete::deleteSingleExpense($_POST['id']);
        Flash::addMessage('Expense has been deleted.');

        $this->indexAction();

    }

}