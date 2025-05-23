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

    // Visualizar vagas (atualizado com novos campos)
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
                v.id_empresa,
                e.nome_empresa
            FROM 
                vagas v
            LEFT JOIN 
                empresa e ON v.id_empresa = e.id_empresa
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
            $id_empresa,
            $nome_empresa
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
            $vaga['id_empresa'] = $id_empresa;
            $vaga['nome_empresa'] = $nome_empresa;
            array_push($tbVagas, $vaga);
        }
        $stmt->close();
        return $tbVagas;
    }

// Cadastrar vagas (atualizado com novos campos)
    function cadastrarVagas($titulo, $localizacao, $descricao, $requisitos, $salario, 
                      $tipo_contrato, $area_atuacao, $id_empresa, $beneficios,
                      $nivel_experiencia, $habilidades_desejaveis, $ramo) {
    
    // Initialize response array
    $response = ['error' => true, 'message' => 'Unknown error occurred'];
    
    try {
        // Validate required fields
        if (empty($titulo) || empty($id_empresa)) {
            throw new Exception('Título e ID da empresa são obrigatórios');
        }

        $stmt = $this->con->prepare("INSERT INTO vagas (
            titulo_vagas, 
            local_vagas, 
            descricao_vagas, 
            requisitos_vagas, 
            salario_vagas, 
            tipo_contrato, 
            area_atuacao, 
            id_empresa,
            beneficios_vagas,
            nivel_experiencia,
            habilidades_desejaveis,
            ramo_vagas,
            id_candidato
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");

        if (!$stmt) {
            throw new Exception('Erro na preparação: ' . $this->con->error);
        }

        $bindResult = $stmt->bind_param("ssssssssssss", 
            $titulo,
            $localizacao,
            $descricao,
            $requisitos,
            $salario,
            $tipo_contrato,
            $area_atuacao,
            $id_empresa,
            $beneficios,
            $nivel_experiencia,
            $habilidades_desejaveis,
            $ramo
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
    
    // Ensure no output before this
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
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

    // Cadastro Empresa
    function cadastrarEmpresa($cnpj_empresa, $nome_empresa, $email_empresa, $local_empresa, $porte_empresa){
        $stmt = $this->con->prepare("INSERT INTO tbEmpresa (cnpj_empresa, nome_empresa, email_empresa, local_empresa, porte_empresa) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $cnpj_empresa, $nome_empresa, $email_empresa, $local_empresa, $porte_empresa);
        return $stmt->execute();
    }


    // Listar Empresa
    function getcadastrarEmpresa(){
        $stmt = $this->con->prepare("SELECT id_empresa, cnpj_empresa, nome_empresa, email_empresa, local_empresa, porte_empresa FROM tbEmpresa");
        $stmt->execute();
        $stmt->bind_result($id_empresa, $cnpj_empresa, $nome_empresa, $email_empresa, $local_empresa, $porte_empresa);
        $empresas = array();
        while($stmt->fetch()){
            $empresa = array();
            $empresa['id_empresa'] = $id_empresa;
            $empresa['cnpj_empresa'] = $cnpj_empresa;
            $empresa['nome_empresa'] = $nome_empresa;
            $empresa['email_empresa'] = $email_empresa;
            $empresa['local_empresa'] = $local_empresa;
            $empresa['porte_empresa'] = $porte_empresa;
            array_push($empresas, $empresa);
        }
        return $empresas;
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

    // Atualizar Empresa
    function updatecadastrarEmpresa($id_empresa, $cnpj_empresa, $nome_empresa, $email_empresa, $local_empresa, $porte_empresa){
        $stmt = $this->con->prepare("UPDATE tbEmpresa SET cnpj_empresa = ?, nome_empresa = ?, email_empresa = ?, local_empresa = ?, porte_empresa = ? WHERE id_empresa = ?");
        $stmt->bind_param("sssssi", $cnpj_empresa, $nome_empresa, $email_empresa, $local_empresa, $porte_empresa, $id_empresa);
        return $stmt->execute();
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

    // Deletar Empresa
    function deletecadastrarEmpresa($id_empresa){
        $stmt = $this->con->prepare("DELETE FROM tbEmpresa WHERE id_empresa = ?");
        $stmt->bind_param("i", $id_empresa);
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