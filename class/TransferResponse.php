<?php
namespace BankAPI;

class TransfersResponse {
    private array $transfers = [];
    private string $error = "";

    public function setTransfers(array $transfers): void {
        $this->transfers = $transfers;
    }

    public function setError(string $error): void {
        $this->error = $error;
    }

    public function send(): void {
        header('Content-Type: application/json');
        echo json_encode([
            'transfers' => $this->transfers,
            'error' => $this->error
        ]);
    }
}
?>
