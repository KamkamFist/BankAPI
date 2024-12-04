<?php
namespace BankAPI;

class TransfersRequest {
    private string $token;

    public function __construct() {
        $data = file_get_contents('php://input');
        $dataArray = json_decode($data, true);
        
        $this->token = $dataArray['token'] ?? '';
    }

    public function getToken(): string {
        return $this->token;
    }
}
?>