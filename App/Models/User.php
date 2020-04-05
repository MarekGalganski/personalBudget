<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors['emptyName'] = 'Name is required';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['notEmail'] = 'Invalid email';
        }
        if (static::emailExists($this->email)) {
            $this->errors['repeatedEmail'] = 'Email already taken';
        }

        // Password
        if ($this->password != $this->password_confirmation) {
            $this->errors['notSame'] = 'Password must match confirmation';
        }

        if (strlen($this->password) < 6) {
            $this->errors['char'] = 'Please enter at least 6 characters for the password';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors['letter'] = 'Password needs at least one letter';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors['number'] = 'Password needs at least one number';
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

  
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

   
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        //$expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        //$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
    public static function assignDefaultExpensesToUser($email)
    {
        $user = static::findByEmail($email);
        
        $user_id = $user->id;

        static::addDefaultExpenses($user_id);

    }

    public static function addDefaultExpenses($user_id)
    {
        for( $i=1; $i<17; $i++){
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, (SELECT name FROM expenses_category_default WHERE id=:id))';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':id', $i, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public static function assignDefaultPaymentsToUser($email)
    {
        $user = static::findByEmail($email);
        
        $user_id = $user->id;

        static::addDefaultPayments($user_id);

    }

    public static function addDefaultPayments($user_id)
    {
        for( $i=1; $i<4; $i++){
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                    VALUES (:user_id, (SELECT name FROM payment_methods_default WHERE id=:id))';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':id', $i, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public static function assignDefaultIncomesToUser($email)
    {
        $user = static::findByEmail($email);
        
        $user_id = $user->id;

        static::addDefaultIncomes($user_id);

    }

    public static function addDefaultIncomes($user_id)
    {
        for( $i=1; $i<5; $i++){
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, (SELECT name FROM incomes_category_default WHERE id=:id))';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':id', $i, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}
