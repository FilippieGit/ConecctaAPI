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


            case 'getVagasByUserId':
    validateParameters(['user_id'], 'GET');
    $response['vagas'] = $db->getVagasByUserId((int)$_GET['user_id']);
    $response['count'] = count($response['vagas']);
    break;

        case 'cadastrarVaga':   
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                throw new Exception('Método POST requerido');
            }

            $requiredParams = [
                'titulo', 'localizacao', 'descricao', 'requisitos',
                'salario', 'tipo_contrato', 'area_atuacao', 'id_usuario',
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
                $_POST['id_usuario'],
                $_POST['beneficios'],
                $_POST['nivel_experiencia'],
                $_POST['habilidades_desejaveis'],
                $_POST['ramo']
            );
            break;


            ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



            

            case 'atualizarStatusCandidatura':
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Método POST requerido'
        ]);
        exit();
    }

    // Verificar parâmetros obrigatórios
    $requiredParams = ['candidatura_id', 'novo_status', 'vaga_id', 'recrutador_id'];
    $missingParams = array_diff($requiredParams, array_keys($_POST));
    
    if (!empty($missingParams)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Parâmetros obrigatórios faltando: ' . implode(', ', $missingParams)
        ]);x
        exit();
    }

    // Validar tipos de dados
    if (!is_numeric($_POST['candidatura_id']) || !is_numeric($_POST['vaga_id']) || !is_numeric($_POST['recrutador_id'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'IDs devem ser valores numéricos'
        ]);
        exit();
    }

    // Obter motivo (opcional)
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : null;

    // Processar a atualização
    try {
        $response = $db->atualizarStatusCandidaturaVaga(
            (int)$_POST['candidatura_id'],
            trim($_POST['novo_status']),
            (int)$_POST['vaga_id'],
            (int)$_POST['recrutador_id'],
            $motivo
        );

        // Definir cabeçalhos antes de enviar a resposta
        http_response_code($response['error'] ? 500 : 200);
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen(json_encode($response)));
        
        // Enviar resposta
        echo json_encode($response);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Erro no servidor: ' . $e->getMessage()
        ]);
        exit();
    }
    break;






        case 'verificarCandidatura':
            validateParameters(['user_id', 'vaga_id'], 'GET');
            $response = [
                'ja_candidatado' => $db->verificarCandidatura($_GET['user_id'], (int)$_GET['vaga_id'])
            ];
            break;

            case 'getUserByFirebaseUid':
            if (!isset($_GET['uid'])) {
                throw new Exception('Parâmetro uid é obrigatório');
            }

            $result = $db->getUserByFirebaseUid($_GET['uid']);
            if ($result['error']) {
                throw new Exception($result['message']);
            }

            $response['user'] = $result['user'];
            break;

        case 'syncFirebaseUser':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método POST requerido');
            }

            $required = ['uid', 'email', 'nome'];
            $missing = array_diff($required, array_keys($_POST));
            if (!empty($missing)) {
                throw new Exception('Parâmetros faltando: ' . implode(', ', $missing));
            }

            $result = $db->syncFirebaseUser(
                $_POST['uid'],
                $_POST['email'],
                $_POST['nome'],
                $_POST['tipo'] ?? 'Física',
                $_POST['documento'] ?? null
            );

            if ($result['error']) {
                throw new Exception($result['message']);
            }

            $response['message'] = $result['message'];
            $response['user_id'] = $result['user_id'];
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

            case 'notificarTodosAprovados':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => true, 'message' => 'Método POST requerido']);
        exit();
    }

    validateParameters(['vaga_id', 'recrutador_id', 'mensagem']);

    try {
        $response = $db->notificarTodosAprovados(
            (int)$_POST['vaga_id'],
            (int)$_POST['recrutador_id'],
            $_POST['mensagem']
        );

        error_log("Resposta JSON: " . json_encode($response)); // <-- agora no lugar certo

        if ($response['error']) {
            http_response_code(500);
        }

        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
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
    
    // Adiciona logs para depuração
    error_log("Resposta listarCandidaturas: " . json_encode($response));
    
    // Se houver erro, retorna código 500
    if ($response['error']) {
        http_response_code(500);
    }
    
    echo json_encode($response);
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
?>