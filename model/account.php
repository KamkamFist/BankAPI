<?php
//klasa odpowiadająca tabli account w bazie danych
namespace BankAPI;
//przestrzeń nazw - bez tego nie możemy użyć bazy mimo, że dostajemy ją jako argument
use mysqli;

class Account {
    //hermetyzacja - ukrywamy zmienne przed innymi klasami
    private $accountNo;
    private $amount;
    private $name;

    //tworzy nowy obiekt klasy account na podstawie danych podanych jako argumenty
    public function __construct($accountNo, $amount, $name) {
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->name = $name;
    }
    

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