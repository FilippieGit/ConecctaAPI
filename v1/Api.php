<?php
// Configurações iniciais
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
ini_set('display_errors', 0);
error_reporting(0);

// Limpar buffer de saída
ob_clean();

// Tratar requisições OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir arquivo necessário
require_once '../includes/DbOperation.php';

/**
 * Verifica se os parâmetros necessários estão disponíveis
 */
function isTheseParametersAvailable($params) {
    $missing = array();
    
    foreach($params as $param) {
        if(!isset($_POST[$param])) {
            $missing[] = $param;
        } elseif(is_string($_POST[$param]) && trim($_POST[$param]) === '') {
            $missing[] = $param;
        }
    }
    
    if(!empty($missing)) {
        $response = array(
            'error' => true,
            'message' => 'Parâmetros faltando: ' . implode(', ', $missing)
        );
        echo json_encode($response);
        exit();
    }
}

// Inicializar array de resposta
$response = array('error' => true, 'message' => 'Nenhuma operação especificada');

// Verificar se é uma chamada de API válida
if(isset($_GET['apicall'])) {
    switch($_GET['apicall']) {
        case 'getVagas':
            try {
                $db = new DbOperation();
                $vagas = $db->getVagas();
                
                $response = array(
                    'error' => false,
                    'message' => 'Lista de vagas obtida com sucesso',
                    'vagas' => $vagas,
                    'count' => count($vagas)
                );
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => 'Erro ao obter vagas: ' . $e->getMessage(),
                    'trace' => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? $e->getTrace() : null
                );
            }
            break;

        case 'cadastrarVaga':
            // Verificar parâmetros obrigatórios
            isTheseParametersAvailable(array(
                'titulo', 
                'localizacao', 
                'descricao', 
                'requisitos',
                'salario',
                'tipo_contrato',
                'area_atuacao',
                'id_empresa',
                'beneficios',
                'nivel_experiencia',
                'habilidades_desejaveis',
                'ramo'
            ));
            
            try {
                $db = new DbOperation();
                
                $result = $db->cadastrarVagas(
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
                
                $response = $result;
                
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => 'Erro no processamento: ' . $e->getMessage(),
                    'debug' => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? $e->getTraceAsString() : null
                );
            }
            break;

        case 'excluirVaga':
            if (!isset($_POST['id_vaga']) || empty($_POST['id_vaga'])) {
                $response = array(
                    'error' => true,
                    'message' => 'Parâmetro id_vaga é obrigatório'
                );
                break;
            }

            try {
                $db = new DbOperation();
                $result = $db->excluirVagas(intval($_POST['id_vaga']));
                $response = $result;
                
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => 'Erro no processamento: ' . $e->getMessage()
                );
            }
            break;

        default:
            $response = array(
                'error' => true,
                'message' => 'Operação não implementada'
            );
    }
}

// Enviar resposta
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>