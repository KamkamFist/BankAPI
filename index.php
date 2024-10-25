<?php
//klasa odpowiadająca za routing czyli przetwarzanie URL zapytania skierowanego do serwera API
//wszystko po localhost/bankAPI trafi dzieki temu do tego skryptu
require_once('Route.php');
//model odpowiadający za tabelę account w bazie danych - umożliwia operacje na rachunkach
require_once('model/account.php');
require_once('model/user.php');
require_once('model/token.php');
//połączenie do bazy danych
//TODO: wyodrębnić zmienne dotyczące środowiska do pliku konfiguracyjnego
$db = new mysqli('localhost', 'root', '', 'bankAPI');
$db->set_charset('utf8');
//ustawienie kodowania znaków na utf8 dla bazy danych
$db->set_charset('utf8');

//użyj przestrzeni nazw od klasy routingu i od naszej klasy od rachunków
use Steampixel\Route;
use BankAPI\Account;
use BankAPI\User;

//jeśli ktoś zapyta API bez żadnego parametru
//zwróć hello world
//TODO: to jest tylko do testów  - usunąć później
Route::add('/', function() {
  echo 'Hello world!';
});


Route::add('/login', function() use ($db) {

$data = file_get_contents("php://input");
$data = json_decode($data, true);
//var_dump($data);
$ip = $_SERVER['REMOTE_ADDR'];
try{
  $user_id = User::login($data['login'], $data['password'], $db);
  $token = Token::new($ip, $user_id, $db);
  header('Content-Type: application/json');
  echo json_encode(['token' => $token]);
} catch(Exception) {
  header('HTTP/1.1 401 Unauthorized');
  echo json_encode(['error' => 'Invalid login or password']);
  return;
}


  //return var_dump($_POST);

}, 'post');

Route::add('/account/details', function() use ($db) {

  $data = file_get_contents("php://input");
  $dataArray = json_decode($data, true);
  //var_dump($data);
  $ip = $_SERVER['REMOTE_ADDR'];

$token = $dataArray['token'];

if(!Token::check($token, $ip, $db)){
  header('HTTP/1.1 401 Unauthorized');
  echo json_encode(['error' => 'Invalid token']);
  return;
}

$user_id = Token::getUserData($token, $ip, $db);
  
  echo $user_id;



  }, 'post');



//ścieżka wyświetla dane dotyczące rachunku bankowego po jego numerze
//jeżeli ktoś zapyta API o /account/1234 to zwróci dane rachunku o numerze 1234
//klasa Route podstawia argumenty z URL (wyrażenie regularne) do funkcji
Route::add('/account/([0-9]*)', function($accountNo) use($db) {
    //$accountNo to numer rachunku, którego dane chcę pobrać z bazy
    //funkcja statyczna pobiera informacje o rachunku z bazy danych i tworzy obiekt rachunku
    $account = Account::getAccount($accountNo, $db);
    //ustaw nagłówek odpowiedzi na JSON żeby przeglądarka wiedziała jak interpretować dane
    header('Content-Type: application/json');
    //zwróć dane w formacie JSON korzystając z funkcji udostępniającej dane prywatne jako tablicę
    return json_encode($account->getArray());
});

//ta linijka musi być na końcu
//musi tu być nazwa folderu w którym "mieszka" API
Route::run('/bankAPI');

$db->close();
?>