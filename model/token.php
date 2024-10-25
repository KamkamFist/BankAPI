<?php


class Token {
    static function new(string $ip, int $user_id, mysqli $db) : string {
        $hash = hash('sha256', $ip . $user_id . time());
        $sql = "INSERT INTO token (token, ip, user_id) VALUES (?, ?, ?)";
        $query = $db->prepare($sql);
        $query->bind_param('ssi', $hash, $ip, $user_id);

        if(!$query->execute()){
            throw new Exception('Cannot create token'); 
        }else{
            return $hash;
        }
    }
    //funkcja sprawdajaca poprawnosc tokenu
    static function check(string $token, string $ip, mysqli $db) : bool {
        $sql = "SELECT * FROM token WHERE token = ? AND ip = ?";
        $query = $db->prepare($sql);
        $query->bind_param('ss', $token, $ip);
        $query->execute();
        $result = $query->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows == 0){
            return false;
        }else{
            return true;
        }
    }

    static function getUserData($token, $db) : int{
        $sql = "SELECT * FROM token WHERE token = ? ORDER BY id DESC LIMIT 1";
        $query = $db->prepare($sql);
        $query->bind_param('s', $token);
        $query->execute();
        $result = $query->get_result();
  if($result->num_rows == 0){
            throw new Exception('Invalid token');   
        }else{
            $data = $result->fetch_assoc();
            return $data['user_id'];
    }
}
}


?>