<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    // Visualizar vagas (atualizado com novos campos e estrutura de usuários)
    function getVagas() {
        $stmt = $this->con->prepare("
            SELECT 
                v.id_vagas, 
                v.titulo_vagas, 
                v.local_vagas, 
                v.descricao_vagas, 
                v.requisitos_vagas, 
                v.salario_vagas, 
                v.vinculo_vagas, 
                v.ramo_vagas, 
                v.beneficios_vagas,
                v.nivel_experiencia,
                v.tipo_contrato,
                v.area_atuacao,
                v.habilidades_desejaveis,
                v.id_usuario,
                u.nome as nome_empresa,
                u.email as email_empresa,
                u.website as website_empresa,
                u.CNPJ as cnpj_empresa
            FROM 
                vagas v
            LEFT JOIN 
                usuarios u ON v.id_usuario = u.id
        ");

        if (!$stmt) {
            die("Erro na preparação da query: " . $this->con->error);
        }

        $stmt->execute();
        $stmt->bind_result(
            $id_vagas, 
            $titulo_vagas, 
            $local_vagas, 
            $descricao_vagas, 
            $requisitos_vagas, 
            $salario_vagas, 
            $vinculo_vagas, 
            $ramo_vagas, 
            $beneficios_vagas,
            $nivel_experiencia,
            $tipo_contrato,
            $area_atuacao,
            $habilidades_desejaveis,
            $id_usuario,
            $nome_empresa,
            $email_empresa,
            $website_empresa,
            $cnpj_empresa
        );

        $tbVagas = array();
        while ($stmt->fetch()) {
            $vaga = array();
            $vaga['id_vagas'] = $id_vagas;
            $vaga['titulo_vagas'] = $titulo_vagas;
            $vaga['local_vagas'] = $local_vagas;
            $vaga['descricao_vagas'] = $descricao_vagas;
            $vaga['requisitos_vagas'] = $requisitos_vagas;
            $vaga['salario_vagas'] = $salario_vagas;
            $vaga['vinculo_vagas'] = $vinculo_vagas;
            $vaga['ramo_vagas'] = $ramo_vagas;
            $vaga['beneficios_vagas'] = $beneficios_vagas;
            $vaga['nivel_experiencia'] = $nivel_experiencia;
            $vaga['tipo_contrato'] = $tipo_contrato;
            $vaga['area_atuacao'] = $area_atuacao;
            $vaga['habilidades_desejaveis'] = $habilidades_desejaveis;
            $vaga['id_usuario'] = $id_usuario;
            $vaga['nome_empresa'] = $nome_empresa;
            $vaga['email_empresa'] = $email_empresa;
            $vaga['website_empresa'] = $website_empresa;
            $vaga['cnpj_empresa'] = $cnpj_empresa;
            array_push($tbVagas, $vaga);
        }
        $stmt->close();
        return $tbVagas;
    }

    function getVagaById($vaga_id) {
    try {
        $stmt = $this->con->prepare("
            SELECT 
                v.id_vagas, 
                v.titulo_vagas,
                u.nome as nome_empresa
            FROM 
                vagas v
            LEFT JOIN 
                usuarios u ON v.id_usuario = u.id
            WHERE 
                v.id_vagas = ?
            LIMIT 1
        ");
        
        $stmt->bind_param("i", $vaga_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'error' => true,
                'message' => 'Vaga não encontrada'
            ];
        }
        
        $vaga = $result->fetch_assoc();
        
        return [
            'error' => false,
            'vaga' => $vaga
        ];
        
    } catch (Exception $e) {
        return [
            'error' => true,
            'message' => $e->getMessage()
        ];
    }
}



    function getUserByFirebaseUid($firebaseUid) {
        try {
            $stmt = $this->con->prepare("
                SELECT 
                    id,
                    firebase_uid,
                    nome,
                    username,
                    genero,
                    idade,
                    telefone,
                    email,
                    setor,
                    descricao,
                    experiencia_profissional,
                    formacao_academica,
                    certificados,
                    imagem_perfil,
                    tipo,
                    CNPJ,
                    website
                FROM 
                    usuarios
                WHERE 
                    firebase_uid = ?
                LIMIT 1
            ");

            if (!$stmt) {
                throw new Exception("Erro na preparação da query: " . $this->con->error);
            }

            $stmt->bind_param("s", $firebaseUid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [
                    'error' => true,
                    'message' => 'Usuário não encontrado'
                ];
            }

            $user = $result->fetch_assoc();
            
            return [
                'error' => false,
                'user' => $user
            ];

        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }




    /**
     * Cria ou atualiza um usuário com base no Firebase UID
     */
    function syncFirebaseUser($firebaseUid, $email, $nome, $tipo = 'Física', $documento = null) {
        try {
            // Verifica se o usuário já existe
            $checkStmt = $this->con->prepare("SELECT id FROM usuarios WHERE firebase_uid = ?");
            $checkStmt->bind_param("s", $firebaseUid);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Atualiza usuário existente
                $user = $checkResult->fetch_assoc();
                $updateStmt = $this->con->prepare("
                    UPDATE usuarios SET 
                        email = ?, 
                        nome = ?, 
                        tipo = ?,
                        documento = ?
                    WHERE id = ?
                ");
                $updateStmt->bind_param("ssssi", $email, $nome, $tipo, $documento, $user['id']);
                $updateStmt->execute();

                return [
                    'error' => false,
                    'message' => 'Usuário atualizado',
                    'user_id' => $user['id']
                ];
            } else {
                // Insere novo usuário
                $insertStmt = $this->con->prepare("
                    INSERT INTO usuarios (
                        firebase_uid, 
                        email, 
                        nome, 
                        tipo,
                        documento,
                        data_cadastro
                    ) VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $insertStmt->bind_param("sssss", $firebaseUid, $email, $nome, $tipo, $documento);
                $insertStmt->execute();

                return [
                    'error' => false,
                    'message' => 'Usuário criado',
                    'user_id' => $insertStmt->insert_id
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    // Na sua classe DbOperation
function getVagasByUserId($userId) {
    $stmt = $this->con->prepare("
        SELECT 
            v.id_vagas, 
            v.titulo_vagas, 
            v.local_vagas, 
            v.descricao_vagas, 
            v.requisitos_vagas, 
            v.salario_vagas, 
            v.vinculo_vagas, 
            v.ramo_vagas, 
            v.beneficios_vagas,
            v.nivel_experiencia,
            v.tipo_contrato,
            v.area_atuacao,
            v.habilidades_desejaveis,
            v.id_usuario,
            u.nome as nome_empresa,
            u.email as email_empresa,
            u.website as website_empresa,
            u.CNPJ as cnpj_empresa
        FROM 
            vagas v
        LEFT JOIN 
            usuarios u ON v.id_usuario = u.id
        WHERE 
            v.id_usuario = ?
    ");

    if (!$stmt) {
        die("Erro na preparação da query: " . $this->con->error);
    }

    $stmt->bind_param("i", $userId); // "i" para integer
    $stmt->execute();
    $stmt->bind_result(
        $id_vagas, 
        $titulo_vagas, 
        $local_vagas, 
        $descricao_vagas, 
        $requisitos_vagas, 
        $salario_vagas, 
        $vinculo_vagas, 
        $ramo_vagas, 
        $beneficios_vagas,
        $nivel_experiencia,
        $tipo_contrato,
        $area_atuacao,
        $habilidades_desejaveis,
        $id_usuario,
        $nome_empresa,
        $email_empresa,
        $website_empresa,
        $cnpj_empresa
    );

    $tbVagas = array();
    while ($stmt->fetch()) {
        $vaga = array();
        $vaga['id_vagas'] = $id_vagas;
        $vaga['titulo_vagas'] = $titulo_vagas;
        $vaga['local_vagas'] = $local_vagas;
        $vaga['descricao_vagas'] = $descricao_vagas;
        $vaga['requisitos_vagas'] = $requisitos_vagas;
        $vaga['salario_vagas'] = $salario_vagas;
        $vaga['vinculo_vagas'] = $vinculo_vagas;
        $vaga['ramo_vagas'] = $ramo_vagas;
        $vaga['beneficios_vagas'] = $beneficios_vagas;
        $vaga['nivel_experiencia'] = $nivel_experiencia;
        $vaga['tipo_contrato'] = $tipo_contrato;
        $vaga['area_atuacao'] = $area_atuacao;
        $vaga['habilidades_desejaveis'] = $habilidades_desejaveis;
        $vaga['id_usuario'] = $id_usuario;
        $vaga['nome_empresa'] = $nome_empresa;
        $vaga['email_empresa'] = $email_empresa;
        $vaga['website_empresa'] = $website_empresa;
        $vaga['cnpj_empresa'] = $cnpj_empresa;
        array_push($tbVagas, $vaga);
    }
    $stmt->close();
    return $tbVagas;
}

    // Cadastrar vagas (atualizado com novos campos e id_usuario)
    function cadastrarVagas($titulo, $localizacao, $descricao, $requisitos, $salario, 
                          $tipo_contrato, $area_atuacao, $id_usuario, $beneficios,
                          $nivel_experiencia, $habilidades_desejaveis, $ramo) {
        $response = ['error' => true, 'message' => 'Unknown error occurred'];
        try {
            if (empty($titulo) || empty($id_usuario)) {
                throw new Exception('Título e ID do usuário são obrigatórios');
            }

            $stmt = $this->con->prepare("INSERT INTO vagas (
                titulo_vagas, local_vagas, descricao_vagas, requisitos_vagas, salario_vagas, tipo_contrato, area_atuacao, id_usuario,
                beneficios_vagas, nivel_experiencia, habilidades_desejaveis, ramo_vagas, id_candidato
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");

            if (!$stmt) {
                throw new Exception('Erro na preparação: ' . $this->con->error);
            }

            $bindResult = $stmt->bind_param("ssssssssssss", 
                $titulo, $localizacao, $descricao, $requisitos, $salario, $tipo_contrato, $area_atuacao, $id_usuario,
                $beneficios, $nivel_experiencia, $habilidades_desejaveis, $ramo
            );

            if (!$bindResult) {
                throw new Exception('Erro ao vincular parâmetros: ' . $stmt->error);
            }

            $executeResult = $stmt->execute();
            if (!$executeResult) {
                throw new Exception('Erro na execução: ' . $stmt->error);
            }

            $response = [
                'error' => false,
                'message' => 'Vaga cadastrada com sucesso',
                'id_vaga' => $stmt->insert_id,
                'affected_rows' => $stmt->affected_rows
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                'sql_error' => $this->con->error ?? null
            ];
        }
        return $response;
    }

    // excluir vagas
    function excluirVagas($id_vaga) {
        try {
            // Verifica se a vaga existe antes de tentar excluir
            $stmt = $this->con->prepare("SELECT id_vagas FROM vagas WHERE id_vagas = ?");
            $stmt->bind_param("i", $id_vaga);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows == 0) {
                throw new Exception('Vaga não encontrada');
            }
            $stmt->close();

            // Prepara a query de exclusão
            $stmt = $this->con->prepare("DELETE FROM vagas WHERE id_vagas = ?");
            if (!$stmt) {
                throw new Exception('Erro na preparação: ' . $this->con->error);
            }

            $stmt->bind_param("i", $id_vaga);

            if (!$stmt->execute()) {
                throw new Exception('Erro na execução: ' . $stmt->error);
            }

            return [
                'error' => false,
                'message' => 'Vaga excluída com sucesso',
                'affected_rows' => $stmt->affected_rows
            ];

        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'sql_error' => $this->con->error ?? null
            ];
        }
    }




    public function atualizarStatusCandidaturaVaga(int $candidatura_id, string $novo_status, int $vaga_id): array {
    try {
        // Log básico de entrada
        file_put_contents('log.txt', "ID: $candidatura_id | Status: $novo_status | Vaga: $vaga_id\n", FILE_APPEND);

        // Verifica se a vaga existe
        $stmt = $this->con->prepare("SELECT 1 FROM vagas WHERE id_vagas = ? LIMIT 1");
        if (!$stmt) throw new Exception("Erro prepare SELECT: " . $this->con->error);
        $stmt->bind_param("i", $vaga_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            return ['error' => true, 'message' => 'Vaga não encontrada.'];
        }
        $stmt->close();

        // Atualiza o status
        $stmt = $this->con->prepare("UPDATE candidaturas SET status = ?, motivo_rejeicao = NULL WHERE id = ? AND vaga_id = ?");
        if (!$stmt) throw new Exception("Erro prepare UPDATE: " . $this->con->error);
        $stmt->bind_param("sii", $novo_status, $candidatura_id, $vaga_id);

        if ($stmt->execute()) {
            return ['error' => false, 'message' => 'Status atualizado com sucesso.'];
        } else {
            return ['error' => true, 'message' => 'Falha ao atualizar status.'];
        }
    } catch (Exception $e) {
        return ['error' => true, 'message' => 'Erro no servidor: ' . $e->getMessage()];
    }
}




    private function enviarNotificacaoAprovacao($candidatura_id, $vaga_id, $titulo_vaga, $descricao_vaga, $mensagem, $candidato) {
    try {
        // Enviar e-mail
        $assunto = "Parabéns! Você foi aprovado para a vaga de {$titulo_vaga}";
                
        $mensagem_email = $this->criarEmailAprovacao(
            $candidato['nome'],
            $titulo_vaga,
            $descricao_vaga,
            $mensagem,
            $candidato['data_candidatura']
        );

        if (!$this->enviarEmail($candidato['email'], $assunto, $mensagem_email)) {
            throw new Exception("Falha ao enviar e-mail para {$candidato['email']}");
        }

        // Enviar notificação push se houver token
        if (!empty($candidato['fcm_token'])) {
            if (!$this->enviarNotificacaoPush(
                $candidato['fcm_token'],
                "Parabéns! Você foi aprovado",
                "Você foi selecionado para a vaga de {$titulo_vaga}",
                ['vaga_id' => $vaga_id]
            )) {
                throw new Exception("Falha ao enviar notificação para {$candidato['nome']}");
            }
        }
    } catch (Exception $e) {
        error_log("[DbOperation] Erro ao enviar notificação para candidato {$candidato['nome']}: " . $e->getMessage());
        throw $e; // Re-lançar a exceção para ser tratada na função principal
    }
}










/**
 * Função para notificar todos os candidatos aprovados de uma vaga
 */
function notificarTodosAprovados($vaga_id, $recrutador_id, $mensagem_personalizada = '')
{
    $response = ['error' => false];
    $emails_enviados = 0;
    $erros = [];

    try {
        // Conectar ao banco
        $db = new DbConnect();
        $con = $db->connect();

        // 1. Buscar informações da vaga
        $stmtVaga = $con->prepare("SELECT titulo FROM vagas WHERE id = ?");
        $stmtVaga->bind_param("i", $vaga_id);
        $stmtVaga->execute();
        $vaga = $stmtVaga->get_result()->fetch_assoc();
        
        if (!$vaga) {
            throw new Exception("Vaga não encontrada");
        }

        // 2. Buscar candidaturas aprovadas com dados do usuário
        $stmtCandidatos = $con->prepare("
            SELECT c.id_candidatura, u.id, u.nome, u.email 
            FROM candidaturas c
            JOIN users u ON c.user_id = u.id
            WHERE c.vaga_id = ? AND c.status = 'aprovada'
        ");
        $stmtCandidatos->bind_param("i", $vaga_id);
        $stmtCandidatos->execute();
        $candidatos = $stmtCandidatos->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($candidatos)) {
            $response['message'] = "Nenhum candidato aprovado para esta vaga";
            return $response;
        }

        // 3. Configurar PHPMailer (substitua com suas configurações)
        require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.seuprovedor.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'contato@empresa.com';
        $mail->Password = 'sua_senha';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom('recrutamento@empresa.com', 'Recrutamento');
        $mail->isHTML(true);

        // 4. Enviar emails para cada candidato
        foreach ($candidatos as $candidato) {
            try {
                $mail->clearAddresses();
                $mail->addAddress($candidato['email'], $candidato['nome']);
                
                $mail->Subject = 'Parabéns! Sua candidatura foi aprovada - ' . $vaga['titulo'];
                
                // Corpo do email (HTML e versão texto)
                $bodyHTML = "
                    <h1>Olá {$candidato['nome']}!</h1>
                    <p>Estamos felizes em informar que sua candidatura para a vaga <strong>{$vaga['titulo']}</strong> foi aprovada!</p>
                    " . (!empty($mensagem_personalizada) ? "<p>Mensagem do recrutador: {$mensagem_personalizada}</p>" : "") . "
                    <p>Em breve nosso time entrará em contato com você para os próximos passos.</p>
                    <p>Atenciosamente,<br>Equipe de Recrutamento</p>
                ";
                
                $bodyText = strip_tags($bodyHTML);
                
                $mail->Body = $bodyHTML;
                $mail->AltBody = $bodyText;
                
                // Enviar email
                if ($mail->send()) {
                    $emails_enviados++;
                    
                    // Registrar no log que o email foi enviado
                    $stmtUpdate = $con->prepare("
                        UPDATE candidaturas 
                        SET data_atualizacao = NOW() 
                        WHERE id_candidatura = ?
                    ");
                    $stmtUpdate->bind_param("i", $candidato['id_candidatura']);
                    $stmtUpdate->execute();
                } else {
                    $erros[] = "Falha ao enviar para {$candidato['email']}: " . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                $erros[] = "Erro ao enviar para {$candidato['email']}: " . $e->getMessage();
            }
        }

        // 5. Montar resposta
        $response['emails_enviados'] = $emails_enviados;
        $response['total_candidatos'] = count($candidatos);
        $response['vaga_id'] = $vaga_id;
        $response['vaga_titulo'] = $vaga['titulo'];
        
        if (!empty($erros)) {
            $response['erros'] = $erros;
            $response['message'] = "Emails enviados com alguns erros";
        } else {
            $response['message'] = "Todos os emails foram enviados com sucesso!";
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    return $response;
}



private function criarEmailAprovacao($nome, $tituloVaga, $descricaoVaga, $mensagemRecrutador, $dataCandidatura) {
    $dataFormatada = date('d/m/Y', strtotime($dataCandidatura));
    
    return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 10px; text-align: center; }
                .content { padding: 20px; }
                .footer { margin-top: 20px; font-size: 0.8em; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Parabéns, $nome!</h1>
                </div>
                
                <div class='content'>
                    <p>Você foi <strong>aprovado</strong> para a vaga:</p>
                    <h2>$tituloVaga</h2>
                    <p><em>$descricaoVaga</em></p>
                    
                    <h3>Mensagem do recrutador:</h3>
                    <blockquote>$mensagemRecrutador</blockquote>
                    
                    <p>Você se candidatou em: $dataFormatada</p>
                    
                    <p>Em breve o recrutador entrará em contato com você para os próximos passos.</p>
                </div>
                
                <div class='footer'>
                    <p>Atenciosamente,<br>Equipe de Recrutamento</p>
                </div>
            </div>
        </body>
        </html>
    ";
}

private function enviarEmail($para, $assunto, $mensagem) {
    // Configurações do e-mail
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: recrutamento@seusite.com\r\n";
    $headers .= "Reply-To: recrutamento@seusite.com\r\n";
    
    // Usar PHPMailer ou função mail() nativa
    return mail($para, $assunto, $mensagem, $headers);
}

private function enviarNotificacaoPush($token, $titulo, $mensagem, $dados = []) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    
    $fields = [
        'to' => $token,
        'notification' => [
            'title' => $titulo,
            'body' => $mensagem,
            'sound' => 'default'
        ],
        'data' => $dados,
        'priority' => 'high'
    ];

    $headers = [
        'Authorization: key=' . $this->fcmServerKey,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Apenas para desenvolvimento
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $response = json_decode($result, true);
        return $response['success'] == 1;
    }
    
    return false;
}




public function getCandidaturasRecusadas($recrutador_id)
    {
        try {
            // Primeiro, obtemos todas as vagas desse recrutador
            $stmtVagas = $this->con->prepare("SELECT id_vagas FROM vagas WHERE id_usuario = ?");
            $stmtVagas->bind_param("i", $recrutador_id);
            $stmtVagas->execute();
            $resultVagas = $stmtVagas->get_result();
            
            $vagaIds = [];
            while ($row = $resultVagas->fetch_assoc()) {
                $vagaIds[] = $row['id_vagas'];
            }
            
            if (empty($vagaIds)) {
                return [];
            }
            
            // Agora buscamos as candidaturas recusadas para essas vagas
            $placeholders = implode(',', array_fill(0, count($vagaIds), '?'));
            $query = "SELECT 
                        c.id_candidatura,
                        c.vaga_id,
                        c.user_id,
                        c.respostas,
                        c.data_candidatura,
                        c.status,
                        c.data_atualizacao,
                        c.motivo_rejeicao,
                        c.recrutador_id,
                        u.nome AS nome_candidato,
                        u.email AS email_candidato,
                        u.telefone AS telefone_candidato,
                        v.titulo AS titulo_vaga
                      FROM candidaturas c
                      JOIN usuarios u ON c.user_id = u.id
                      JOIN vagas v ON c.vaga_id = v.id_vagas
                      WHERE c.vaga_id IN ($placeholders) AND c.status = 'rejeitada'
                      ORDER BY c.data_atualizacao DESC";
            
            $stmt = $this->con->prepare($query);
            
            // Bind dos parâmetros dinamicamente
            $types = str_repeat('i', count($vagaIds));
            $stmt->bind_param($types, ...$vagaIds);
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $candidaturas = [];
            while ($row = $result->fetch_assoc()) {
                // Decodifica as respostas JSON se existirem
                if (!empty($row['respostas'])) {
                    $row['respostas'] = json_decode($row['respostas'], true);
                }
                
                // Formata as datas
                if (!empty($row['data_candidatura'])) {
                    $row['data_candidatura_formatada'] = date('d/m/Y H:i', strtotime($row['data_candidatura']));
                }
                
                if (!empty($row['data_atualizacao'])) {
                    $row['data_atualizacao_formatada'] = date('d/m/Y H:i', strtotime($row['data_atualizacao']));
                }
                
                $candidaturas[] = $row;
            }
            
            return $candidaturas;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar candidaturas recusadas: " . $e->getMessage());
            return [];
        }
    }




    function listarCandidatosPorVaga($vaga_id) {
    try {
        $stmt = $this->con->prepare("
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.telefone,
                u.setor as cargo,
                u.descricao,
                u.experiencia_profissional,
                u.formacao_academica,
                u.certificados,
                u.imagem_perfil,
                c.id_candidatura,
                c.respostas,
                c.status,
                c.data_candidatura,
                c.motivo_rejeicao,
                c.recrutador_id
            FROM 
                candidaturas c
            JOIN 
                usuarios u ON c.user_id = u.id
            WHERE 
                c.vaga_id = ?
            ORDER BY 
                c.data_candidatura DESC
        ");
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $this->con->error);
        }

        $stmt->bind_param("i", $vaga_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $candidatos = array();
        while ($row = $result->fetch_assoc()) {
            // Decodifica as respostas JSON se existirem
            if (!empty($row['respostas'])) {
                $row['respostas'] = json_decode($row['respostas'], true);
            }
            $candidatos[] = $row;
        }
        
        return array(
            'error' => false,
            'candidatos' => $candidatos,
            'count' => count($candidatos)
        );
    } catch (Exception $e) {
        error_log("Erro em listarCandidatosPorVaga: " . $e->getMessage());
        return array(
            'error' => true,
            'message' => 'Erro ao listar candidatos: ' . $e->getMessage()
        );
    }
}






    // Verificar se usuário já se candidatou a uma vaga
    function verificarCandidatura($user_id, $vaga_id) {
        $stmt = $this->con->prepare("SELECT id_candidatura FROM candidaturas WHERE user_id = ? AND vaga_id = ?");
        $stmt->bind_param("si", $user_id, $vaga_id);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows > 0;
    }

    // Candidatar-se a uma vaga
function candidatarVaga($firebase_uid, $vaga_id, $respostas = null, $recrutador_id = null) {
    try {
        // Buscar o ID real do usuário com base no Firebase UID
        $stmt = $this->con->prepare("SELECT id FROM usuarios WHERE firebase_uid = ?");
        $stmt->bind_param("s", $firebase_uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'error' => true,
                'message' => 'Usuário não encontrado',
                'code' => 404
            ];
        }

        $row = $result->fetch_assoc();
        $user_id = $row['id'];

        // Verificar se já está candidatado
        $stmt = $this->con->prepare("SELECT id_candidatura FROM candidaturas WHERE user_id = ? AND vaga_id = ?");
        $stmt->bind_param("ii", $user_id, $vaga_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return [
                'error' => true,
                'message' => 'Você já se candidatou a esta vaga',
                'code' => 409 // Conflict
            ];
        }

        // Converter respostas para string JSON se for um objeto/array
        if ($respostas !== null && (is_array($respostas) || is_object($respostas))) {
            $respostas = json_encode($respostas, JSON_UNESCAPED_UNICODE);
        }

        // Tratar recrutador_id NULL
        if ($recrutador_id === null || $recrutador_id === 'NULL') {
            // Inserir sem recrutador_id (será NULL no banco)
            $stmt = $this->con->prepare("INSERT INTO candidaturas 
                                       (vaga_id, user_id, respostas, status) 
                                       VALUES (?, ?, ?, 'pendente')");
            $stmt->bind_param("iis", $vaga_id, $user_id, $respostas);
        } else {
            // Inserir com recrutador_id
            $stmt = $this->con->prepare("INSERT INTO candidaturas 
                                       (vaga_id, user_id, respostas, status, recrutador_id) 
                                       VALUES (?, ?, ?, 'pendente', ?)");
            $stmt->bind_param("iisi", $vaga_id, $user_id, $respostas, $recrutador_id);
        }

        if ($stmt->execute()) {
            // Atualizar contador de candidatos na vaga
            $this->con->query("UPDATE vagas SET id_candidato = id_candidato + 1 WHERE id_vagas = $vaga_id");

            return [
                'error' => false,
                'message' => 'Candidatura realizada com sucesso',
                'id_candidatura' => $stmt->insert_id,
                'user_id' => $user_id,
                'vaga_id' => $vaga_id,
                'recrutador_id' => $recrutador_id
            ];
        } else {
            throw new Exception('Erro ao registrar candidatura: ' . $stmt->error);
        }

    } catch (Exception $e) {
        return [
            'error' => true,
            'message' => $e->getMessage(),
            'code' => 500
        ];
    }
}


public function listarRejeitadas($user_id) {
        try {
            $query = "SELECT 
                        c.id_candidatura,
                        c.vaga_id,
                        c.status,
                        c.motivo_rejeicao,
                        c.data_atualizacao,
                        v.titulo AS titulo_vaga,
                        e.nome AS nome_empresa
                      FROM 
                        candidaturas c
                      JOIN 
                        vagas v ON c.vaga_id = v.id_vagas
                      JOIN 
                        empresas e ON v.empresa_id = e.id_empresa
                      WHERE 
                        c.user_id = ? AND c.status = 'rejeitada'
                      ORDER BY 
                        c.data_atualizacao DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            
            $candidaturas = array();
            
            while ($row = $result->fetch_assoc()) {
                $candidatura = array(
                    "id_candidatura" => $row['id_candidatura'],
                    "vaga_id" => $row['vaga_id'],
                    "titulo_vaga" => $row['titulo_vaga'],
                    "nome_empresa" => $row['nome_empresa'],
                    "status" => $row['status'],
                    "motivo_rejeicao" => $row['motivo_rejeicao'],
                    "data_atualizacao" => $row['data_atualizacao']
                );
                array_push($candidaturas, $candidatura);
            }
            
            return array(
                "error" => false,
                "candidaturas" => $candidaturas
            );
            
        } catch (Exception $e) {
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }



    // Listar candidaturas de uma vaga (para a empresa)
    function listarCandidaturas($vaga_id) {
        try {
            $stmt = $this->con->prepare("
                SELECT 
                    c.id_candidatura,
                    c.user_id,
                    c.respostas,
                    c.data_candidatura,
                    c.status,
                    u.nome,
                    u.email,
                    u.telefone
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
            
            $candidaturas = array();
            while ($row = $result->fetch_assoc()) {
                // Decodificar as respostas JSON se existirem
                if (!empty($row['respostas'])) {
                    $row['respostas'] = json_decode($row['respostas'], true);
                }
                $candidaturas[] = $row;
            }
            
            return [
                'error' => false,
                'candidaturas' => $candidaturas,
                'count' => count($candidaturas)
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    
    
    // Cadastro Currículo
    function cadastrarCurriculo($descricao_curriculo, $exper_profissional_curriculo, $exper_academico_curriculo, $certificados_curriculo, $endereco_curriculo){
        $stmt = $this->con->prepare("INSERT INTO tbCurriculo (descricao_curriculo, exper_profissional_curriculo, exper_academico_curriculo, certificados_curriculo, endereco_curriculo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $descricao_curriculo, $exper_profissional_curriculo, $exper_academico_curriculo, $certificados_curriculo, $endereco_curriculo);
        return $stmt->execute();
    }

    // Exemplo BD
    function createHero($name, $realname, $rating, $teamaffiliation){
        $stmt = $this->con->prepare("INSERT INTO heroes (name, realname, rating, teamaffiliation) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $realname, $rating, $teamaffiliation);
        return $stmt->execute();
    }

    public function getConnection() {
    return $this->con; // Ou qualquer que seja o nome da sua variável de conexão
}

    // Listar Currículo
    function getcadastrarCurriculo(){
        $stmt = $this->con->prepare("SELECT id_curriculo, descricao_curriculo, exper_profissional_curriculo, exper_academico_curriculo, certificados_curriculo, endereco_curriculo FROM tbCurriculo");
        $stmt->execute();
        $stmt->bind_result($id_curriculo, $descricao_curriculo, $exper_profissional_curriculo, $exper_academico_curriculo, $certificados_curriculo, $endereco_curriculo);
        $curriculos = array();
        while($stmt->fetch()){
            $curriculo = array();
            $curriculo['id_curriculo'] = $id_curriculo;
            $curriculo['descricao_curriculo'] = $descricao_curriculo;
            $curriculo['exper_profissional_curriculo'] = $exper_profissional_curriculo;
            $curriculo['exper_academico_curriculo'] = $exper_academico_curriculo;
            $curriculo['certificados_curriculo'] = $certificados_curriculo;
            $curriculo['endereco_curriculo'] = $endereco_curriculo;
            array_push($curriculos, $curriculo);
        }
        return $curriculos;
    }

    // Listar Candidato
    function getcadastrarCandidato(){
        $stmt = $this->con->prepare("SELECT id_candidato, cpf_candidato, nome_candidato, telefone_candidato, email_candidato, local_candidato, data_nasc_candidato, estado_civil_candidato FROM tbCandidatos");
        $stmt->execute();
        $stmt->bind_result($id_candidato, $cpf_candidato, $nome_candidato, $telefone_candidato, $email_candidato, $local_candidato, $data_nasc_candidato, $estado_civil_candidato);
        $candidatos = array();
        while($stmt->fetch()){
            $candidato = array();
            $candidato['id_candidato'] = $id_candidato;
            $candidato['cpf_candidato'] = $cpf_candidato;
            $candidato['nome_candidato'] = $nome_candidato;
            $candidato['telefone_candidato'] = $telefone_candidato;
            $candidato['email_candidato'] = $email_candidato;
            $candidato['local_candidato'] = $local_candidato;
            $candidato['data_nasc_candidato'] = $data_nasc_candidato;
            $candidato['estado_civil_candidato'] = $estado_civil_candidato;
            array_push($candidatos, $candidato);
        }
        return $candidatos;
    }

    // Atualizar Vagas
    function updatecadastrarVagas($id_vagas, $nome_vagas, $local_vagas, $descricao_vagas, $requisitos_vagas, $salario_vagas, $vinculo_vagas, $ramo_vagas){
        $stmt = $this->con->prepare("UPDATE vagas SET nome_vagas = ?, local_vagas = ?, descricao_vagas = ?, requisitos_vagas = ?, salario_vagas = ?, vinculo_vagas = ?, ramo_vagas = ? WHERE id_vagas = ?");
        $stmt->bind_param("sssssssi", $nome_vagas, $local_vagas, $descricao_vagas, $requisitos_vagas, $salario_vagas, $vinculo_vagas, $ramo_vagas, $id_vagas);
        return $stmt->execute();
    }

    // Atualizar Currículo
    function updatecadastrarCurriculo($id_curriculo, $descricao_curriculo, $exper_profissional_curriculo, $exper_academico_curriculo, $certificados_curriculo, $endereco_curriculo){
        $stmt = $this->con->prepare("UPDATE tbCurriculo SET descricao_curriculo = ?, exper_profissional_curriculo = ?, exper_academico_curriculo = ?, certificados_curriculo = ?, endereco_curriculo = ? WHERE id_curriculo = ?");
        $stmt->bind_param("sssssi", $descricao_curriculo, $exper_profissional_curriculo, $exper_academico_curriculo, $certificados_curriculo, $endereco_curriculo, $id_curriculo);
        return $stmt->execute();
    }

    // Atualizar Candidato
    function updatecadastrarCandidato($id_candidato, $cpf_candidato, $nome_candidato, $telefone_candidato, $email_candidato, $local_candidato, $data_nasc_candidato, $estado_civil_candidato){
        $stmt = $this->con->prepare("UPDATE tbCandidatos SET cpf_candidato = ?, nome_candidato = ?, telefone_candidato = ?, email_candidato = ?, local_candidato = ?, data_nasc_candidato = ?, estado_civil_candidato = ? WHERE id_candidato = ?");
        $stmt->bind_param("sssssssi", $cpf_candidato, $nome_candidato, $telefone_candidato, $email_candidato, $local_candidato, $data_nasc_candidato, $estado_civil_candidato, $id_candidato);
        return $stmt->execute();
    }

    // Deletar Vagas
    function deletecadastrarVagas($id_vagas){
        $stmt = $this->con->prepare("DELETE FROM vagas WHERE id_vagas = ?");
        $stmt->bind_param("i", $id_vagas);
        return $stmt->execute();
    }

    // Deletar Currículo
    function deletecadastrarCurriculo($id_curriculo){
        $stmt = $this->con->prepare("DELETE FROM tbCurriculo WHERE id_curriculo = ?");
        $stmt->bind_param("i", $id_curriculo);
        return $stmt->execute();
    }

    // Deletar Candidato
    function deletecadastrarCandidato($id_candidato){
        $stmt = $this->con->prepare("DELETE FROM tbCandidatos WHERE id_candidato = ?");
        $stmt->bind_param("i", $id_candidato);
        return $stmt->execute();
    }
}
?>