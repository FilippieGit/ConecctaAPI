<?php
// Configurações iniciais
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// Ativar erros durante o desenvolvimento
ini_set('display_errors', 1);
error_reporting(0);

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

function getRequestData()
{
    $method = $_SERVER['REQUEST_METHOD'];

    // Verifica o Content-Type
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        // Se for JSON, lê o input raw e decodifica
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    } else if ($method === 'POST') {
        return $_POST;
    } else if ($method === 'GET') {
        return $_GET;
    } else {
        return [];
    }
}
/**
 * Verifica se os parâmetros necessários estão disponíveis
 */
function validateParameters($requiredParams)
{
    $input = getRequestData();

    $missing = [];
    foreach ($requiredParams as $param) {
        if (!isset($input[$param]) || (is_string($input[$param]) && trim($input[$param]) === '')) {
            $missing[] = $param;
        }
    }

    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Parâmetros obrigatórios faltando: ' . implode(', ', $missing)
        ]);
        exit();
    }

    return $input;  // Retorna os dados já validados para uso posterior
}

// Recupera os dados da requisição
$inputData = getRequestData();

try {
    // Verificar método HTTP
    $allowedMethods = ['GET', 'POST'];
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        http_response_code(405);
        throw new Exception('Método não permitido. Use: ' . implode(', ', $allowedMethods));
    }

    // Verificar chamada de API
    if (!isset($inputData['apicall'])) {
        http_response_code(400);
        throw new Exception('Parâmetro apicall é obrigatório');
    }

    $db = new DbOperation();
    $response = ['error' => false];

    switch ($inputData['apicall']) {
        case 'getVagas':
            $response['vagas'] = $db->getVagas();
            $response['count'] = count($response['vagas']);
            break;

        case 'getVagasByUserId':
            validateParameters(['user_id']);
            $response['vagas'] = $db->getVagasByUserId((int)$inputData['user_id']);
            $response['count'] = count($response['vagas']);
            break;

            case 'getVagaById':
    validateParameters(['vaga_id']);
    $vaga_id = (int)$inputData['vaga_id'];
    $response = $db->getVagaById($vaga_id);
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
            $postData = validateParameters($requiredParams);

            $response = $db->cadastrarVagas(
                $postData['titulo'],
                $postData['localizacao'],
                $postData['descricao'],
                $postData['requisitos'],
                $postData['salario'],
                $postData['tipo_contrato'],
                $postData['area_atuacao'],
                $postData['id_usuario'],
                $postData['beneficios'],
                $postData['nivel_experiencia'],
                $postData['habilidades_desejaveis'],
                $postData['ramo']
            );
            break;


              case 'getCandidaturasRecusadas':
            validateParameters(['recrutador_id']);
            $recrutador_id = (int)$inputData['recrutador_id'];
            $response['candidaturas'] = $db->getCandidaturasRecusadas($recrutador_id);
            $response['count'] = count($response['candidaturas']);
            break;

        case 'atualizarStatusCandidatura':
            header('Content-Type: application/json');

            try {
                // Caminho correto para DbConnect.php (um diretório acima)
                $dbConnectPath = __DIR__ . '/../includes/DbConnect.php';
                if (!file_exists($dbConnectPath)) {
                    throw new Exception("Arquivo DbConnect.php não encontrado em: " . $dbConnectPath);
                }

                require_once $dbConnectPath;
                $db = new DbConnect();
                $con = $db->connect();

                // Validar parâmetros
                if (!isset($inputData['candidatura_id'], $inputData['novo_status'], $inputData['vaga_id'])) {
                    throw new Exception("Parâmetros obrigatórios faltando");
                }

                $candidatura_id = (int)$inputData['candidatura_id'];
                $novo_status = strtolower(trim($inputData['novo_status']));
                $vaga_id = (int)$inputData['vaga_id'];

                // Validar status
                $statusPermitidos = ['aprovada', 'rejeitada', 'pendente', 'visualizada'];
                if (!in_array($novo_status, $statusPermitidos)) {
                    throw new Exception("Status inválido: " . $novo_status);
                }

                // Atualizar no banco
                $stmt = $con->prepare("UPDATE candidaturas SET status = ?, data_atualizacao = NOW() WHERE id_candidatura = ? AND vaga_id = ?");
                $stmt->bind_param("sii", $novo_status, $candidatura_id, $vaga_id);

                if ($stmt->execute()) {
                    echo json_encode([
                        'error' => false,
                        'message' => "Status atualizado com sucesso",
                        'novo_status' => $novo_status
                    ]);
                } else {
                    throw new Exception("Erro ao executar query: " . $stmt->error);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'error' => true,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'verificarCandidatura':
            validateParameters(['user_id', 'vaga_id']);
            $response = [
                'ja_candidatado' => $db->verificarCandidatura($inputData['user_id'], (int)$inputData['vaga_id'])
            ];
            break;

        case 'getUserByFirebaseUid':
            validateParameters(['uid']);
            $result = $db->getUserByFirebaseUid($inputData['uid']);
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
            $syncData = validateParameters($required);

            $result = $db->syncFirebaseUser(
                $syncData['uid'],
                $syncData['email'],
                $syncData['nome'],
                $syncData['tipo'] ?? 'Física',
                $syncData['documento'] ?? null
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

    // Aqui estamos pegando os dados do corpo da requisição
    $postData = getRequestData();

    if (!isset($postData['user_id']) || !isset($postData['vaga_id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Parâmetros user_id e vaga_id são obrigatórios']);
        exit();
    }

    $firebase_uid = $postData['user_id'];
    $vaga_id = (int)$postData['vaga_id'];
    $respostas = isset($postData['respostas']) ? $postData['respostas'] : null;
    $recrutador_id = isset($postData['recrutador_id']) ? $postData['recrutador_id'] : null;

    // Validar se respostas é JSON válido (opcional)
    if ($respostas !== null) {
        json_decode($respostas);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Formato das respostas inválido']);
            exit();
        }
    }

    // Chama a função passando todos os parâmetros
    $response = $db->candidatarVaga($firebase_uid, $vaga_id, $respostas, $recrutador_id);

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
    try {
        // Validar parâmetros
        $requiredParams = ['vaga_id', 'recrutador_id', 'mensagem'];
        $input = validateParameters($requiredParams);

        // Obter conexão do DbOperation (assumindo que $db já existe)
        $dbConnection = $db->getConnection(); // Você precisará adicionar este método ao DbOperation se não existir
        
        // Criar serviço de email
        require_once __DIR__ . '/../includes/EmailService.php';
        $emailService = new EmailService($dbConnection);
        
        // Chamar função de notificação
        $response = $emailService->notificarCandidatosAprovados(
            (int)$input['vaga_id'],
            (int)$input['recrutador_id'],
            $input['mensagem']
        );

        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    break;




case 'listarCandidaturasRejeitadas':
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Parâmetro user_id é obrigatório'
        ]);
        exit();
    }

    $user_id = (int)$_GET['user_id'];
    $response = $db->listarCandidaturasRejeitadas($user_id);

    // Adiciona logs para depuração
    error_log("Resposta listarCandidaturasRejeitadas: " . json_encode($response));

    // Se houver erro, retorna código 500
    if ($response['error']) {
        http_response_code(500);
    }

    echo json_encode($response);
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

            $exclusaoData = validateParameters(['id_vaga']);
            $response = $db->excluirVagas((int)$exclusaoData['id_vaga']);
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
