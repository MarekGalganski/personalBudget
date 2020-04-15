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
class Delete extends \Core\Model
{
    
    public static function deleteRevenueCategory($id) {

        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->execute();
    
    }

    public static function deleteExpenseCategory($id)
    {

        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->execute();
    
    }

    public static function deletePaymentMethod($id)
    {
        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->execute();
    }

}