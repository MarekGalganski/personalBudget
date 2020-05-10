<?php

namespace App\Models;

use PDO;
use DateTime;

class Expenses extends \Core\Model
{
    public $errors = [];
    
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getCategories($id_user)
    {
        $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id_user, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public static function getPayments($id_user)
    {
        $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id=:id ';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id_user, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public function validate()
    {
        $amount = preg_replace('/,/', '.', $this->amount);
        
        if (is_numeric($amount)) {
            $this->amount = abs($amount);
        }else{
            $this->errors['notDigit'] = 'Please provide the number.';
        }

        if(empty($this->amount)) {
            $this->errors['emptyDigit'] = 'Please provide the number.';
        }


        if(empty($this->date)) {
            $this->errors['emptyDate'] = 'Please provide the date.';
        }

        if(! static::validateDate($this->date)){
            $this->errors['wrongDate'] = 'Please provide the date in "YYYY-MM-dd" .';
        }

        if(empty($this->category)) {
            $this->errors['emptyCategory'] = 'Please choose the category.';
        }

        if(empty($this->payment)) {
            $this->errors['emptyPayment'] = 'Please choose the payment.';
        }

        if(! empty($this->comment)){
            if (strlen($this->comment) > 100) {
                $this->errors['char'] = 'Comment can have a maximum of 100 characters.';
            }
        }
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id,payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
            VALUES (:id_user, :category, :payment, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            
            $stmt->bindValue(':id_user', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':payment', $this->payment, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public static function getExpenses($id_user, $first_day, $last_day)
    {
        $sql = 'SELECT * FROM expenses WHERE date_of_expense>=:first_day AND date_of_expense<=:last_day AND user_id=:id_user ORDER BY date_of_expense ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':first_day', $first_day);
        $stmt->bindValue(':last_day', $last_day);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public static function getSumExpenses($id_user, $first_day, $last_day)
    {
        $sql = 'SELECT SUM(amount) FROM expenses WHERE date_of_expense>=:first_day AND date_of_expense<=:last_day AND user_id=:id_user';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':first_day', $first_day);
        $stmt->bindValue(':last_day', $last_day);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function selectLimit($categoryName){
        $sql = 'SELECT category_limit FROM expenses_category_assigned_to_users WHERE name = :name AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $categoryName, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();

    }

    public static function getSumExpensesFromOneCategory($categoryName){

        $first_day_this_month  = date('Y-m-d', strtotime('first day of this month'));
        $last_day_this_month = date('Y-m-d', strtotime('last day of this month'));

        $sql = 'SELECT SUM(amount) FROM expenses WHERE expense_category_assigned_to_user_id = :name AND user_id = :user_id
        AND date_of_expense>=:first_day AND date_of_expense<=:last_day';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':first_day', $first_day_this_month);
        $stmt->bindValue(':last_day', $last_day_this_month);

        $stmt->execute();

        return $stmt->fetchColumn();

    }

    public static function getGroupedExpenses($first_day, $last_day)
    {
        $sql = 'SELECT expense_category_assigned_to_user_id, SUM(amount) FROM `expenses`
        WHERE user_id = :id_user AND date_of_expense>=:first_day AND date_of_expense<= :last_day
        GROUP BY expense_category_assigned_to_user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_user', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':first_day', $first_day);
        $stmt->bindValue(':last_day', $last_day);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}