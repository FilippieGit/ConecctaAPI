<?php
// includes/EmailService.php

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $db;
    private $smtpConfig;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        
        // Configurações SMTP (substitua com suas credenciais)
        $this->smtpConfig = [
            'host' => 'smtp.seuprovedor.com',
            'username' => 'seu@email.com',
            'password' => 'suasenha',
            'port' => 587,
            'secure' => 'tls'
        ];
    }

    public function notificarCandidatosAprovados($vaga_id, $recrutador_id, $mensagem_personalizada = '') {
        $response = ['error' => false];
        $emails_enviados = 0;
        $erros = [];

        try {
            // 1. Buscar informações da vaga
            $stmtVaga = $this->db->prepare("SELECT titulo FROM vagas WHERE id = ?");
            $stmtVaga->bind_param("i", $vaga_id);
            $stmtVaga->execute();
            $vaga = $stmtVaga->get_result()->fetch_assoc();
            
            if (!$vaga) {
                throw new Exception("Vaga não encontrada");
            }

            // 2. Buscar candidaturas aprovadas
            $stmtCandidatos = $this->db->prepare("
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

            // 3. Configurar e enviar emails
            foreach ($candidatos as $candidato) {
                $resultadoEnvio = $this->enviarEmailAprovacao(
                    $candidato,
                    $vaga,
                    $mensagem_personalizada
                );

                if ($resultadoEnvio['sucesso']) {
                    $emails_enviados++;
                } else {
                    $erros[] = $resultadoEnvio['erro'];
                }
            }

            // 4. Montar resposta
            $response['emails_enviados'] = $emails_enviados;
            $response['total_candidatos'] = count($candidatos);
            
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

    private function enviarEmailAprovacao($candidato, $vaga, $mensagem_personalizada) {
        $mail = new PHPMailer(true);
        $resultado = ['sucesso' => false];

        try {
            // Configurações SMTP
            $mail->isSMTP();
            $mail->Host = $this->smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpConfig['username'];
            $mail->Password = $this->smtpConfig['password'];
            $mail->SMTPSecure = $this->smtpConfig['secure'];
            $mail->Port = $this->smtpConfig['port'];
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom($this->smtpConfig['username'], 'Recrutamento');
            $mail->addAddress($candidato['email'], $candidato['nome']);
            
            $mail->isHTML(true);
            $mail->Subject = 'Parabéns! Sua candidatura foi aprovada - ' . $vaga['titulo'];
            
            $bodyHTML = "<h1>Olá {$candidato['nome']}!</h1>
                        <p>Sua candidatura para a vaga <strong>{$vaga['titulo']}</strong> foi aprovada!</p>
                        " . (!empty($mensagem_personalizada) ? "<p>Mensagem do recrutador: {$mensagem_personalizada}</p>" : "" . "
                        <p>Em breve entraremos em contato.</p>
                        <p>Atenciosamente,<br>Equipe de Recrutamento</p>";
            
            $mail->Body = $bodyHTML;
            $mail->AltBody = strip_tags($bodyHTML);
            
            $mail->send();
            $resultado['sucesso'] = true;
            
        } catch (Exception $e) {
            $resultado['erro'] = "Erro ao enviar para {$candidato['email']}: " . $e->getMessage();
        }

        return $resultado;
    }
}