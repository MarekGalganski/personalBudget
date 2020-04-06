<?php

namespace App\Models;

use PDO;
use DateTime;

class Revenues extends \Core\Model
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
        $sql = 'SELECT name FROM incomes_category_assigned_to_users WHERE user_id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id_user, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public function validate()
    {
        // Amount
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

            
            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                     VALUES (:id_user,:category,:amount,:date,:comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_user', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public static function getRevenues($id_user, $first_day, $last_day)
    {
        $sql = 'SELECT * FROM incomes WHERE date_of_income>=:first_day AND date_of_income<=:last_day AND user_id=:id_user';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':first_day', $first_day);
        $stmt->bindValue(':last_day', $last_day);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    public static function getSumRevenues($id_user, $first_day, $last_day)
    {
        $sql = 'SELECT SUM(amount) FROM incomes WHERE date_of_income>=:first_day AND date_of_income<=:last_day AND user_id=:id_user';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':first_day', $first_day);
        $stmt->bindValue(':last_day', $last_day);

        $stmt->execute();

        return $stmt->fetchColumn();
    }
}