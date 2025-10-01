<?php
// proxy_api.php - Hospedado no InfinityFree
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, User-Agent, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log para debug
$log_data = date('Y-m-d H:i:s') . " - Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_data .= "POST Data: " . print_r($_POST, true) . "\n";

// Dados recebidos do app Android
$api_key = $_POST['api_key'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$local_id = $_POST['local_id'] ?? '';
$metodo = $_POST['metodo'] ?? '';

// Verifica dados obrigatórios
if (empty($api_key) || empty($user_id) || empty($local_id) || empty($metodo)) {
    echo json_encode(['success' => false, 'message' => 'Dados insuficientes para processar a requisição']);
    exit;
}

// URL da API original
$api_url = 'https://sistema-acesso.page.gd/leitor_api.php';

// Configuração do cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

$log_data .= "HTTP Code: $http_code\n";
$log_data .= "cURL Error: $curl_error\n";
$log_data .= "Response: $response\n";
$log_data .= "------------------------\n";

// Salva log
file_put_contents('proxy_log.txt', $log_data, FILE_APPEND);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o servidor: ' . $curl_error]);
} else {
    // Retorna a resposta da API original
    http_response_code(200);
    echo $response;
}
?>