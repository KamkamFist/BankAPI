<?php
//klasa odpowiadająca tabli account w bazie danych
namespace BankAPI;
//przestrzeń nazw - bez tego nie możemy użyć bazy mimo, że dostajemy ją jako argument
use mysqli;
/**
 * Class Account
 * 
 * This class provides functionalities to perform specific operations regarding
 * accounts in our virtual bank.
 */
class Account {
     //hermetyzacja - ukrywamy zmienne przed innymi klasami
    /**
     * @var int $accountNo - account number
     */

    private $accountNo;
    /** 
     * @var int $amount - amount of money on the account *100 (to avoid float point errors)
     */
    private $amount;
     /**
     * @var string $name - name of the account
     */
    private $name;
    //tworzy nowy obiekt klasy account na podstawie danych podanych jako argumenty
    /**
     * Account constructor.
     * 
     * @param int $accountNo - account number
     * @param int $amount - amount of money on the account
     * @param string $name - name of the account
     * 
     * @return void
     */

    //tworzy nowy obiekt klasy account na podstawie danych podanych jako argumenty
    public function __construct($accountNo, $amount, $name) {
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->name = $name;
    }
    //funkcja zwraca numer rachunku na podstawie podanego ID użytkownika
    /**
     * This method is used to get account number based on user ID.
     * 
     * @param int $userId - user ID
     * @param mysqli $db - database connection
     * 
     * @return int - account number
     */
    public static function getAccountNo(int $userId, mysqli $db) : int {
        //zapytanie do bazy danych
        $sql = "SELECT accountNo FROM account WHERE user_id = ? LIMIT 1";
        //przygotuj zapytanie
        $query = $db->prepare($sql);
        //podmień znaki zapytania na zmienne
        $query->bind_param('i', $userId);
        //wykonaj zapytanie
        $query->execute();
        //pobierz wynik
        $result = $query->get_result();
        //nie robie pętli dlatego, że mamy LIMIT 1
        //skonwertuj na tablicę asocjacyjną
        $account = $result->fetch_assoc();
        //zwróć numer rachunku
        return $account['accountNo'];
    }
//tworzy nowy obiekt na podstawie numeru rachunku pobierając dane z bazy
    //metoda statyczna - nie trzeba tworzyć obiektu klasy żeby jej użyć
    //zwraca obiekt klasy account
    /**
     * This method is used to get account object based on account number.
     * 
     * @param int $accountNo - account number
     * @param mysqli $db - database connection
     * 
     * @return Account - account object
     */

    //tworzy nowy obiekt na podstawie numeru rachunku pobierając dane z bazy
    //metoda statyczna - nie trzeba tworzyć obiektu klasy żeby jej użyć
    //zwraca obiekt klasy account
    public static function getAccount(int $accountNo, mysqli $db) : Account {
        //pobierz informacje o rachunku
        $result = $db->query("SELECT * FROM account WHERE accountNo = $accountNo");
        //skonwertuj na tablicę asocjacyjną
        $account = $result->fetch_assoc();
        $account = new Account($account['AccountNo'], $account['amount'], $account['name']);
        return $account;
    }
     //zwraca tablicę z danymi obiektu to celów serializacji do JSON
    /**
     * This method is used to get array representation of the object.
     * 
     * @return array - array representation of the object
     */
    //zwraca tablicę z danymi obiektu to celów serializacji do JSON
    public function getArray() : array {
        $array = [
            'accountNo' => $this->accountNo,
            'amount' => $this->amount,
            'name' => $this->name
        ];
        return $array;
    }
}
?>