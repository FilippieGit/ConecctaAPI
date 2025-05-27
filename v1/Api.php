<?php
// Configurações iniciais
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// Ativar erros durante o desenvolvimento
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Limpar buffer de saída
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Tratar requisições OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir arquivo necessário
require_once __DIR__ . '/../includes/DbOperation.php';

/**
 * Verifica se os parâmetros necessários estão disponíveis
 */
function validateParameters($requiredParams, $method = 'POST') {
    $input = ($method === 'POST') ? $_POST : $_GET;
    $missing = array_filter($requiredParams, function($param) use ($input) {
        return !isset($input[$param]) || (is_string($input[$param]) && trim($input[$param]) === '');
    });
    
    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Parâmetros obrigatórios faltando: ' . implode(', ', $missing)
        ]);
        exit();
    }
}

try {
    // Verificar método HTTP
    $allowedMethods = ['GET', 'POST'];
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        http_response_code(405);
        throw new Exception('Método não permitido. Use: ' . implode(', ', $allowedMethods));
    }

    // Verificar chamada de API
    if (!isset($_GET['apicall'])) {
        http_response_code(400);
        throw new Exception('Parâmetro apicall é obrigatório');
    }

    $db = new DbOperation();
    $response = ['error' => false];
    
    switch ($_GET['apicall']) {
        case 'getVagas':
            $response['vagas'] = $db->getVagas();
            $response['count'] = count($response['vagas']);
            break;

        case 'cadastrarVaga':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                throw new Exception('Método POST requerido');
            }
            
            $requiredParams = [
    'titulo', 'localizacao', 'descricao', 'requisitos',
    'salario', 'tipo_contrato', 'area_atuacao', 'id_empresa',
    'beneficios', 'nivel_experiencia', 'habilidades_desejaveis', 'ramo'
];
            validateParameters($requiredParams);
            
            $response = $db->cadastrarVagas(
                $_POST['titulo'],
                $_POST['localizacao'],
                $_POST['descricao'],
                $_POST['requisitos'],
                $_POST['salario'],
                $_POST['tipo_contrato'],
                $_POST['area_atuacao'],
                $_POST['id_empresa'],
                $_POST['beneficios'],
                $_POST['nivel_experiencia'],
                $_POST['habilidades_desejaveis'],
                $_POST['ramo']
            );
            break;

        case 'excluirVaga':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                throw new Exception('Método POST requerido');
            }
            
            validateParameters(['id_vaga']);
            $response = $db->excluirVagas((int)$_POST['id_vaga']);
            break;

        default:
            http_response_code(404);
            throw new Exception('Operação não implementada');
    }

} catch (Exception $e) {
    $response = [
        'error' => true,
        'message' => $e->getMessage(),
        'trace' => (ini_get('display_errors')) ? $e->getTrace() : null
    ];
    http_response_code(500);
}

// Enviar resposta final
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit();