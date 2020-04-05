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
        $sql = 'SELECT name FROM expenses_category_assigned_to_users WHERE user_id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id_user, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public static function getPayments($id_user)
    {
        $sql = 'SELECT name FROM payment_methods_assigned_to_users WHERE user_id=:id ';

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

            
            $sql = 'INSERT INTO expenses VALUES (:id, :id_user, :category, :payment, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $null = 'NULL';

            $stmt->bindValue(':id', $null, PDO::PARAM_STR);
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
}