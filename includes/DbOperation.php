<?php

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
                    c.data_candidatura
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
    function candidatarVaga($firebase_uid, $vaga_id, $respostas = null) {
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

            // Inserir nova candidatura
            $stmt = $this->con->prepare("INSERT INTO candidaturas (vaga_id, user_id, respostas, status) VALUES (?, ?, ?, 'pendente')");
            $stmt->bind_param("iis", $vaga_id, $user_id, $respostas);

            if ($stmt->execute()) {
                // Atualizar contador de candidatos na vaga
                $this->con->query("UPDATE vagas SET id_candidato = id_candidato + 1 WHERE id_vagas = $vaga_id");

                return [
                    'error' => false,
                    'message' => 'Candidatura realizada com sucesso',
                    'id_candidatura' => $stmt->insert_id
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

    // Atualizar status de uma candidatura
    function atualizarStatusCandidatura($id_candidatura, $status) {
        try {
            $statusPermitidos = ['pendente', 'visualizada', 'aprovada', 'rejeitada'];
            if (!in_array($status, $statusPermitidos)) {
                throw new Exception('Status inválido');
            }

            $stmt = $this->con->prepare("UPDATE candidaturas SET status = ? WHERE id_candidatura = ?");
            $stmt->bind_param("si", $status, $id_candidatura);
            
            if ($stmt->execute()) {
                return [
                    'error' => false,
                    'message' => 'Status atualizado com sucesso'
                ];
            } else {
                throw new Exception('Erro ao atualizar status: ' . $stmt->error);
            }
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