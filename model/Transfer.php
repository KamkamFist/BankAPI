<?php

namespace BankAPI;
use mysqli;
use Exception;
use mysqli_sql_exception;
/**
 * Class Transfer
 * 
 * This class provides functionalities to perform specific operations regarding
 * transfers in our virtual bank.
 */

class Transfer
{
    public static function new(int $source, int $target, int $amount, mysqli $db): bool
    {
        $db->begin_transaction();
        
        if ($amount > 0) {
            $zapytanie = "SELECT * FROM account WHERE accountNo = ?";
            $wow = $db->prepare($zapytanie);
            $wow->bind_param('i', $source);
            $wow->execute();
            $result = $wow->get_result();
            $test = $result->fetch_assoc();
    
            if ($test['amount'] < $amount) {
                return false; 
            } else {
                try {
                    $sql = "UPDATE account SET amount = amount - ? WHERE accountNo = ?";
                    $query = $db->prepare($sql);
                    $query->bind_param('ii', $amount, $source);
                    $query->execute();
    
                    $sql = "UPDATE account SET amount = amount + ? WHERE accountNo = ?";
                    $query = $db->prepare($sql);
                    $query->bind_param('ii', $amount, $target);
                    $query->execute();
    
                    $sql = "INSERT INTO transfer (source, target, amount) VALUES (?, ?, ?)";
                    $query = $db->prepare($sql);
                    $query->bind_param('iii', $source, $target, $amount);
                    $query->execute();
    
                    $db->commit();
                    return true;
                } catch (mysqli_sql_exception $e) {
                    $db->rollback();
                    throw new Exception('Transfer failed');
                }
            }
        } else {
            return false; 
        }
    }
    public static function getTransferHistory(int $accountNo, mysqli $db): array {
        $sql = "SELECT source, target, timestamp, amount FROM transfer WHERE source = ? OR target = ?";
        $query = $db->prepare($sql);
        $query->bind_param('ii', $accountNo, $AccountNo);
        $query->execute();
        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
