<?php
//klasa odpowiadająca tabli account w bazie danych
namespace BankAPI;
//przestrzeń nazw - bez tego nie możemy użyć bazy mimo, że dostajemy ją jako argument

use Exception;
use mysqli;
/**
 * Class User
 *
 * This class provides functionalities to perform specific operations.
 * It includes methods to handle user authentication such as login.
 *
 */
class User {
    /**
     * This method is used to authenticate user. 
     * It checks if the user with given login and password 
     * exists in the database and returns user id if the user exists.
     * 
     * @param string $login - user login
     * @param string $password - user password
     * @param mysqli $db - database connection
     * 
     * @return int - user id
     */
    static function login(string $login, string $password, mysqli $db) : int {

        $sql  = "SELECT id, passwordHash FROM user WHERE email = ?";
        $query = $db->prepare($sql);
        $query->bind_param('s', $login);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows == 0)
        {
            throw new Exception('Invalid login or password');
        }else{
            $user = $result->fetch_assoc();
            $id = $user['id'];
            $hash = $user['passwordHash'];

            if(password_verify($password, $hash))
            {
                return $id;
            }else{
                throw new Exception('Invalid login or password');
            }


            return $user['id'];

        }
    }
}
?>