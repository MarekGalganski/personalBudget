<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Models\User;

/**
 * User model
 *
 * PHP version 7.0
 */
class ChangeData extends \Core\Model
{
     
    public static function changeName($name)
    {        
            $sql = 'UPDATE users SET name=:name WHERE id=:id_user';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_user', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);

            $stmt->execute();
       
    }

    public static function addRevenueCategory($newCategory)
    {
        
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $newCategory, PDO::PARAM_STR);

            return $stmt->execute();
        
    }

    public static function revenueCategoryExists($category)
    {
        return static::findByRevenueCategory($category) !== false;
    }
  
    public static function findByRevenueCategory($category)
    {
        $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE name = :category AND user_id=:user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function addExpenseCategory($newCategory)
    {
        
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $newCategory, PDO::PARAM_STR);

            return $stmt->execute();
        
    }

    public static function addExpenseCategoryWithLimit($newCategory, $limit)
    {
        
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name, category_limit)
            VALUES (:user_id, :name, :category_limit)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $newCategory, PDO::PARAM_STR);
            $stmt->bindValue(':category_limit', $limit, PDO::PARAM_INT);

            return $stmt->execute();
        
    }

    public static function editExpenseCategoryWithLimit($id, $name, $limit)
    {
            $sql = 'UPDATE expenses_category_assigned_to_users SET name=:name, category_limit=:limit WHERE id=:id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

            return $stmt->execute();
    }
    public static function editExpenseCategoryWithoutLimit( $id, $name)
    {
            $sql = 'UPDATE expenses_category_assigned_to_users SET name=:name WHERE id=:id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);

            return $stmt->execute();
    }

    public static function expenseCategoryExists($category)
    {
        return static::findByExpenseCategory($category) !== false;
    }
  
    public static function findByExpenseCategory($category)
    {
        $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE name = :category AND user_id=:user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function changeEmail($email)
    {        
            $sql = 'UPDATE users SET email=:email WHERE id=:id_user';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_user', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);

            $stmt->execute();
       
    }

    public static function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        }
        if (User::emailExists($email)) {
            return true;
        }

        return false;
    }

}