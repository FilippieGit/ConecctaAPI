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


            case 'verificarCandidatura':
        validateParameters(['user_id', 'vaga_id'], 'GET');
        $response = [
            'ja_candidatado' => $db->verificarCandidatura($_GET['user_id'], (int)$_GET['vaga_id'])
        ];
        break;

    case 'candidatarVaga':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => true, 'message' => 'Método POST requerido']);
        exit();
    }

    parse_str(file_get_contents("php://input"), $postData);

    if (!isset($postData['user_id']) || !isset($postData['vaga_id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Parâmetros user_id e vaga_id são obrigatórios']);
        exit();
    }

    $firebase_uid = $postData['user_id']; // <- esse é o UID vindo do Firebase
    $vaga_id = (int)$postData['vaga_id'];
    $respostas = isset($postData['respostas']) ? $postData['respostas'] : null;

    // Validar se respostas é JSON válido (opcional)
    if ($respostas !== null) {
        json_decode($respostas);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Formato das respostas inválido']);
            exit();
        }
    }

    // Chama a função passando o UID do Firebase
    $response = $db->candidatarVaga($firebase_uid, $vaga_id, $respostas);

    // Código de retorno
    http_response_code(isset($response['code']) ? $response['code'] : ($response['error'] ? 500 : 200));
    echo json_encode($response);
    break;



    case 'getUserByFirebaseUid':
    if (!isset($_GET['uid'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'UID do Firebase é obrigatório']);
        exit();
    }
    
    $stmt = $con->prepare("SELECT id FROM usuarios WHERE firebase_uid = ?");
    $stmt->bind_param("s", $_GET['uid']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['error' => false, 'id' => $user['id']]);
    } else {
        echo json_encode(['error' => true, 'message' => 'Usuário não encontrado']);
    }
    break;

    case 'listarCandidaturas':
    if (!isset($_GET['vaga_id'])) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Parâmetro vaga_id é obrigatório'
        ]);
        exit();
    }
    
    $vaga_id = (int)$_GET['vaga_id'];
    $response = $db->listarCandidatosPorVaga($vaga_id);
    
    // Se houver erro, retorna código 500
    if ($response['error']) {
        http_response_code(500);
    }
    
    echo json_encode($response);
    break;

    case 'listarCandidaturas':
    if (!isset($_GET['vaga_id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Parâmetro vaga_id é obrigatório']);
        exit();
    }
    
    $vaga_id = (int)$_GET['vaga_id'];
    $stmt = $con->prepare("
        SELECT 
            u.id, 
            u.nome, 
            u.email,
            u.setor as cargo,
            c.data_candidatura,
            c.status
        FROM 
            candidaturas c
        JOIN 
            usuarios u ON c.user_id = u.id
        WHERE 
            c.vaga_id = ?
        ORDER BY 
            c.data_candidatura DESC
    ");
    $stmt->bind_param("i", $vaga_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $candidatos = array();
    while ($row = $result->fetch_assoc()) {
        $candidatos[] = $row;
    }
    
    echo json_encode([
        'error' => false,
        'candidatos' => $candidatos,
        'count' => count($candidatos)
    ]);
    break;

    case 'atualizarStatusCandidatura':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            throw new Exception('Método POST requerido');
        }
        
        validateParameters(['id_candidatura', 'status']);
        $response = $db->atualizarStatusCandidatura(
            (int)$_POST['id_candidatura'],
            $_POST['status']
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