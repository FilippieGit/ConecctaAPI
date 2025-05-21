<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../includes/DbOperation.php';

function isTheseParametersAvailable($params){
    $available = true; 
    $missingparams = ""; 
    
    foreach($params as $param){
        if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
            $available = false; 
            $missingparams .= ", " . $param; 
        }
    }
    
    if(!$available){
        $response = array(); 
        $response['error'] = true; 
        $response['message'] = 'Parameters ' . substr($missingparams, 2) . ' missing';
        echo json_encode($response);
        exit(); // substitui die() por exit()
    }
}

$response = array();

if(isset($_GET['apicall'])){
    switch($_GET['apicall']){
        case 'getVagas':
            try {
                $db = new DbOperation();
                $vagas = $db->getVagas();
                
                $response = [
                    'error' => false,
                    'message' => 'Lista de vagas obtida com sucesso',
                    'vagas' => $vagas,
                    'count' => count($vagas)
                ];
            } catch (Exception $e) {
                $response = [
                    'error' => true,
                    'message' => 'Erro ao obter vagas: ' . $e->getMessage()
                ];
            }
            break;

        case 'cadastrarVaga':
            isTheseParametersAvailable(array(
                'titulo', 
                'localizacao', 
                'descricao', 
                'requisitos',
                'salario',
                'tipo_contrato',
                'area_atuacao',
                'id_empresa',
                'beneficios'  // obrigatório
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
                    $_POST['beneficios']
                );
                
                if($result['error']) {
                    $response = [
                        'error' => true,
                        'message' => $result['message'],
                        'sql_error' => $result['sql_error'] ?? null
                    ];
                } else {
                    $response = [
                        'error' => false,
                        'message' => $result['message'],
                        'id_vaga' => $result['id_vaga'],
                        'affected_rows' => $result['affected_rows']
                    ];
                }
                
            } catch (Exception $e) {
                $response = [
                    'error' => true,
                    'message' => 'Erro no processamento: ' . $e->getMessage(),
                    'debug' => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? $e->getTraceAsString() : null
                ];
            }
            break;

        default:
            $response['error'] = true; 
            $response['message'] = 'Chamada de API inválida';
            break;

            case 'excluirVaga':
    isTheseParametersAvailable(array('id_vaga'));
    
    try {
        $db = new DbOperation();
        
        $result = $db->excluirVagas($_POST['id_vaga']);
        
        if($result['error']) {
            $response = [
                'error' => true,
                'message' => $result['message'],
                'sql_error' => $result['sql_error'] ?? null
            ];
        } else {
            $response = [
                'error' => false,
                'message' => $result['message'],
                'affected_rows' => $result['affected_rows']
            ];
        }
        
    } catch (Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Erro no processamento: ' . $e->getMessage(),
            'debug' => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? $e->getTraceAsString() : null
        ];
    }
    break;





    }
} else {
    $response['error'] = true; 
    $response['message'] = 'Chamada de API não especificada';
}

echo json_encode($response);
