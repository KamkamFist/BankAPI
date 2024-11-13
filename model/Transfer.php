<?php
class Transfer
{
    public static function new(int $source, int $target, int $amount, mysqli $db): bool
    {
        //rozpocznij transakcje
        $db->begin_transaction();
        if ($amount > 0) {
            $zapytanie = "select * from account where accountNo = ?";
            $wow = $db->prepare($zapytanie);
            $wow->bind_param('i', $source);
            $wow->execute();
            $result = $wow->get_result();
            $test = $result->fetch_assoc();

            if ($test['amount'] < $amount) {
                return false;
            } else {
                
                try {
                    //sql - odjęcie kwoty z rachunku 1
                    $sql = "UPDATE account SET amount = amount - ? WHERE accountNo = ?";
                    //przygotuj zapytanie
                    $query = $db->prepare($sql);
                    //podmień znaki zapytania na zmienne
                    $query->bind_param('ii', $amount, $source);
                    //wykonaj zapytanie
                    $query->execute();
                    //dodaj kwotę do rachunku 2
                    $sql = "UPDATE account SET amount = amount + ? WHERE accountNo = ?";
                    //przygotuj zapytanie
                    $query = $db->prepare($sql);
                    //podmień znaki zapytania na zmienne
                    $query->bind_param('ii', $amount, $target);
                    //wykonaj zapytanie
                    $query->execute();
                    //zapisz informację o przelewie do bazy danych
                    $sql = "INSERT INTO transfer (source, target, amount) VALUES (?, ?, ?)";
                    //przygotuj zapytanie
                    $query = $db->prepare($sql);
                    //podmień znaki zapytania na zmienne
                    $query->bind_param('iii', $source, $target, $amount);
                    //wykonaj zapytanie
                    $query->execute();
                    //zakończ transakcje
                    $db->commit();
                    return true;
                } catch (mysqli_sql_exception $e) {
                    //jeżeli wystąpił błąd to wycofaj transakcje
                    $db->rollback();
                    //rzuć wyjątek
                    throw new Exception('Transfer failed');
                }
            }
        } else {
            throw new Exception('Invalid amount');
        }
    }
}
