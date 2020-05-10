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
   public $first_date;
   public $second_date;

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

    public function previousMonthAction()
    {
        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        $groupedRevenues = Revenues::getGroupedRevenues($first_day_last_month, $last_day_last_month);
        $groupedExpenses = Expenses::getGroupedExpenses($first_day_last_month, $last_day_last_month);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balancePreviousMonth.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance,
            'groupedRevenues' => $groupedRevenues,
            'groupedExpenses' => $groupedExpenses
        ]);
    }

    public function currentYearAction()
    {
        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);

        $groupedRevenues = Revenues::getGroupedRevenues($first_day_year, $last_day_year);
        $groupedExpenses = Expenses::getGroupedExpenses($first_day_year, $last_day_year);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balanceCurrentYear.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance,
            'groupedRevenues' => $groupedRevenues,
            'groupedExpenses' => $groupedExpenses
        ]);
    }

    public function nonStandardAction()
    {
        $this->first_date = $_POST['firstDate'];
        $this->second_date = $_POST['secondDate'];

        $first_date = $_POST['firstDate'];
        $second_date = $_POST['secondDate'];

        if((! Revenues::validateDate($first_date)) || (! Revenues::validateDate($second_date))){
            
            Flash::addMessage('Provide the correct date.',Flash::WARNING);
            
            $this->indexAction();
            
        }else{
 
            $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_date, $second_date);
            $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_date, $second_date);

            $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_date, $second_date);
            $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_date, $second_date);

            $groupedRevenues = Revenues::getGroupedRevenues($first_date, $second_date);
            $groupedExpenses = Expenses::getGroupedExpenses($first_date, $second_date);

            $balance = $sumRevenues-$sumExpenses;

            View::renderTemplate('Balance/balanceNonStandard.html', [
                'revenues' => $revenues,
                'expenses' => $expenses,
                'sumRevenues' => $sumRevenues,
                'sumExpenses' => $sumExpenses,
                'balance' => $balance,
                'groupedRevenues' => $groupedRevenues,
                'groupedExpenses' => $groupedExpenses,
                'firstDate' => $first_date,
                'secondDate' => $second_date
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

    public function getAllRevenuesCurrentMonthAction(){

        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);

        header('Content-Type: application/json');
        echo json_encode($revenues, JSON_NUMERIC_CHECK);

    }

    public function getAllExpensesCurrentMonthAction(){

        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));
        
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_this_month, $last_day_this_month);

        header('Content-Type: application/json');
        echo json_encode($expenses, JSON_NUMERIC_CHECK);

    }

    public function getBalanceToCharPreviousMonthAction(){
        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        
        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);
        
        $dataPoints = array(
            array("y"=> $sumRevenues, "label"=> "Revenues"),
            array("y"=> $sumExpenses, "label"=> "Expenses")
        );

        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getReveuesToCharPreviousMonthAction(){
        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $revenues = Revenues::getGroupedRevenues($first_day_last_month, $last_day_last_month);;
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($revenues);$i++){
            array_push($dataPoints,array("y"=> $revenues[$i]['SUM(amount)'], "label"=> $revenues[$i]['income_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getExpensesToCharPreviousMonthAction(){
        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $expenses = Expenses::getGroupedExpenses($first_day_last_month, $last_day_last_month);
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($expenses);$i++){
            array_push($dataPoints,array("y"=> $expenses[$i]['SUM(amount)'], "label"=> $expenses[$i]['expense_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getAllRevenuesPreviousMonthAction(){

        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        header('Content-Type: application/json');
        echo json_encode($revenues, JSON_NUMERIC_CHECK);

    }

    public function getAllExpensesPreviousMonthAction(){

        $first_day_last_month  = date('Y-m-d', strtotime('first day of last month'));
        $last_day_last_month = date('Y-m-d', strtotime('last day of last month'));
        
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_last_month, $last_day_last_month);

        header('Content-Type: application/json');
        echo json_encode($expenses, JSON_NUMERIC_CHECK);

    }

    public function deleteSingleRevenuePreviousMonthAction(){
        
        Delete::deleteSingleRevenue($_POST['id']);
        Flash::addMessage('Revenue has been deleted.');

        $this->previousMonthAction();

    }

    public function deleteSingleExpensePreviousMonthAction(){
        
        Delete::deleteSingleExpense($_POST['id']);
        Flash::addMessage('Expense has been deleted.');

        $this->previousMonthAction();

    }

    public function getBalanceToCharCurrentYearAction(){
        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        
        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);
        
        $dataPoints = array(
            array("y"=> $sumRevenues, "label"=> "Revenues"),
            array("y"=> $sumExpenses, "label"=> "Expenses")
        );

        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getReveuesToCharCurrentYearAction(){
        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $revenues = Revenues::getGroupedRevenues($first_day_year, $last_day_year);;
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($revenues);$i++){
            array_push($dataPoints,array("y"=> $revenues[$i]['SUM(amount)'], "label"=> $revenues[$i]['income_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getExpensesToCharCurrentYearAction(){
        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $expenses = Expenses::getGroupedExpenses($first_day_year, $last_day_year);
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($expenses);$i++){
            array_push($dataPoints,array("y"=> $expenses[$i]['SUM(amount)'], "label"=> $expenses[$i]['expense_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getAllRevenuesCurrentYearAction(){

        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_day_year, $last_day_year);

        header('Content-Type: application/json');
        echo json_encode($revenues, JSON_NUMERIC_CHECK);

    }

    public function getAllExpensesCurrentYearAction(){

        $first_day_year  = date('Y-01-01', strtotime('this year'));
        $last_day_year = date('Y-12-31', strtotime('this year'));
        
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_day_year, $last_day_year);

        header('Content-Type: application/json');
        echo json_encode($expenses, JSON_NUMERIC_CHECK);

    }

    public function deleteSingleRevenueCurrentYearAction(){
        
        Delete::deleteSingleRevenue($_POST['id']);
        Flash::addMessage('Revenue has been deleted.');

        $this->currentYearAction();

    }

    public function deleteSingleExpenseCurrentYearAction(){
        
        Delete::deleteSingleExpense($_POST['id']);
        Flash::addMessage('Expense has been deleted.');

        $this->currentYearAction();

    }

    public function getBalanceToCharNonStandardAction(){
        $first_date = $_REQUEST['firstDate'];
        $second_date = $_REQUEST['secondDate'];

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_date, $second_date);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_date, $second_date);
        
        $dataPoints = array(
            array("y"=> $sumRevenues, "label"=> "Revenues"),
            array("y"=> $sumExpenses, "label"=> "Expenses")
        );

        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getReveuesToCharNonStandardAction(){
        $first_date = $_REQUEST['firstDate'];
        $second_date = $_REQUEST['secondDate'];
        
        $revenues = Revenues::getGroupedRevenues($first_date, $second_date);;
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($revenues);$i++){
            array_push($dataPoints,array("y"=> $revenues[$i]['SUM(amount)'], "label"=> $revenues[$i]['income_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getExpensesToCharNonStandardAction(){
        $first_date = $_REQUEST['firstDate'];
        $second_date = $_REQUEST['secondDate'];
        
        $expenses = Expenses::getGroupedExpenses($first_date, $second_date);
        
        $dataPoints = [];
        for($i = 0;$i < sizeof($expenses);$i++){
            array_push($dataPoints,array("y"=> $expenses[$i]['SUM(amount)'], "label"=> $expenses[$i]['expense_category_assigned_to_user_id']));
        }
        header('Content-Type: application/json');
        echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
    }

    public function getAllRevenuesNonStandardAction(){
        $first_date = $_REQUEST['firstDate'];
        $second_date = $_REQUEST['secondDate'];
        
        
        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_date, $second_date);

        header('Content-Type: application/json');
        echo json_encode($revenues, JSON_NUMERIC_CHECK);

    }

    public function getAllExpensesNonStandardAction(){
        $first_date = $_REQUEST['firstDate'];
        $second_date = $_REQUEST['secondDate'];
        
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_date, $second_date);

        header('Content-Type: application/json');
        echo json_encode($expenses, JSON_NUMERIC_CHECK);

    }

    public function deleteSingleRevenueNonStandardAction(){

        $first_date = $_POST['firstDate'];
        $second_date = $_POST['secondDate'];
        
        Delete::deleteSingleRevenue($_POST['id']);

        Flash::addMessage('Revenue has been deleted.');

        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_date, $second_date);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_date, $second_date);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_date, $second_date);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_date, $second_date);

        $groupedRevenues = Revenues::getGroupedRevenues($first_date, $second_date);
        $groupedExpenses = Expenses::getGroupedExpenses($first_date, $second_date);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balanceNonStandard.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance,
            'groupedRevenues' => $groupedRevenues,
            'groupedExpenses' => $groupedExpenses,
            'firstDate' => $first_date,
            'secondDate' => $second_date
        ]);

        

    }

    public function deleteSingleExpenseNonStandardAction(){
        
        $first_date = $_POST['firstDate'];
        $second_date = $_POST['secondDate'];

        Delete::deleteSingleExpense($_POST['id']);
        
        Flash::addMessage('Expense has been deleted.');

        $revenues = Revenues::getRevenues($_SESSION['user_id'], $first_date, $second_date);
        $expenses = Expenses::getExpenses($_SESSION['user_id'], $first_date, $second_date);

        $sumRevenues = Revenues::getSumRevenues($_SESSION['user_id'], $first_date, $second_date);
        $sumExpenses = Expenses::getSumExpenses($_SESSION['user_id'], $first_date, $second_date);

        $groupedRevenues = Revenues::getGroupedRevenues($first_date, $second_date);
        $groupedExpenses = Expenses::getGroupedExpenses($first_date, $second_date);

        $balance = $sumRevenues-$sumExpenses;

        View::renderTemplate('Balance/balanceNonStandard.html', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'sumRevenues' => $sumRevenues,
            'sumExpenses' => $sumExpenses,
            'balance' => $balance,
            'groupedRevenues' => $groupedRevenues,
            'groupedExpenses' => $groupedExpenses,
            'firstDate' => $first_date,
            'secondDate' => $second_date
        ]);

    }

}