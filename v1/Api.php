<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Restante do seu código...

// Configurar exibição de erros (apenas para desenvolvimento)
ini_set('display_errors', 0); // Desativa a exibição de erros no HTML
error_reporting(E_ALL);       // Reporta todos os erros
ini_set('log_errors', 1);     // Ativa o log de erros
ini_set('error_log', 'php_errors.log'); // Arquivo de log

// Verificar se é uma requisição OPTIONS (para CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Iniciar buffer de saída para capturar possíveis erros
ob_start();
try {
    require_once '../includes/DbOperation.php';

    // Restante do seu código original...
    // ... (mantenha todo o conteúdo atual do Api.php aqui)

} catch (Exception $e) {
    // Capturar qualquer exceção não tratada
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erro interno no servidor',
        'debug' => (ENVIRONMENT === 'development') ? $e->getMessage() : null
    ]);
    exit;
}

// Limpar buffer e enviar resposta
ob_end_flush();


	require_once '../includes/DbOperation.php';

	function isTheseParametersAvailable($params){
	
		$available = true; 
		$missingparams = ""; 
		
		foreach($params as $param){
			if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
				$available = false; 
				$missingparams = $missingparams . ", " . $param; 
			}
		}
		
		
		if(!$available){
			$response = array(); 
			$response['error'] = true; 
			$response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';
			
		
			echo json_encode($response);
			
		
			die();
		}
	}
	
	
	$response = array();
	

	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
	
			case 'createhero':
				
				isTheseParametersAvailable(array('name','realname','rating','teamaffiliation'));
				
				$db = new DbOperation();
				
				$result = $db->createHero(
					$_POST['name'],
					$_POST['realname'],
					$_POST['rating'],
					$_POST['teamaffiliation']
				);
				

			
				if($result){
					
					$response['error'] = false; 

					
					$response['message'] = 'Herói adicionado com sucesso';

					
					$response['heroes'] = $db->getHeroes();
				}else{

					
					$response['error'] = true; 

				
					$response['message'] = 'Algum erro ocorreu por favor tente novamente';
				}
				
			break; 
			
		
			case 'getheroes':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Pedido concluído com sucesso';
				$response['heroes'] = $db->getHeroes();
			break; 

			case 'getcadastrarVagas':
	        $db = new DbOperation();
	        $response['error'] = false;
	        $response['message'] = 'Pedido concluído com sucesso';
	        $response['vagas'] = $db->getcadastrarVagas();
	        break;






	        case 'cadastrarVaga':
    // Verifica os parâmetros obrigatórios
    isTheseParametersAvailable(array(
        'titulo', 
        'localizacao', 
        'descricao', 
        'requisitos',
        'salario',
        'tipo_contrato',
        'area_atuacao',
        'id_empresa'
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
            $_POST['id_empresa']
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
            'debug' => (ENVIRONMENT === 'development') ? $e->getTraceAsString() : null
        ];
    }
    break;

    case 'getVagas':
    try {
        $db = new DbOperation();
        $vagas = $db->getcadastrarVagas();
        
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

		
			case 'updatehero':
				isTheseParametersAvailable(array('id','name','realname','rating','teamaffiliation'));
				$db = new DbOperation();
				$result = $db->updateHero(
					$_POST['id'],
					$_POST['name'],
					$_POST['realname'],
					$_POST['rating'],
					$_POST['teamaffiliation']
				);
				
				if($result){
					$response['error'] = false; 
					$response['message'] = 'Herói atualizado com sucesso';
					$response['heroes'] = $db->getHeroes();
				}else{
					$response['error'] = true; 
					$response['message'] = 'Algum erro ocorreu por favor tente novamente';
				}
			break; 
			
			
			case 'deletehero':

				
				if(isset($_GET['id'])){
					$db = new DbOperation();
					if($db->deleteHero($_GET['id'])){
						$response['error'] = false; 
						$response['message'] = 'Herói excluído com sucesso';
						$response['heroes'] = $db->getHeroes();
					}else{
						$response['error'] = true; 
						$response['message'] = 'Algum erro ocorreu por favor tente novamente';
					}
				}else{
					$response['error'] = true; 
					$response['message'] = 'Não foi possível deletar, forneça um id por favor';
				}
			break; 
		}
		
	}else{
		 
		$response['error'] = true; 
		$response['message'] = 'Chamada de API inválida';
	}
	

	echo json_encode($response);
	
	
