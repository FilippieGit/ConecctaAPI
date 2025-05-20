-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS DBconeccta;
USE DBconeccta;

-- Remover tabela pessoajuridica caso exista
DROP TABLE IF EXISTS pessoajuridica;

-- Tabela Empresa
CREATE TABLE IF NOT EXISTS empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    cnpj_empresa CHAR(14) NOT NULL,
    nome_empresa VARCHAR(50) NOT NULL,
    email_empresa VARCHAR(100),
    local_empresa VARCHAR(100),
    porte_empresa VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Tabela Candidato
CREATE TABLE IF NOT EXISTS candidato (
    id_candidato INT AUTO_INCREMENT PRIMARY KEY,
    cpf_candidato CHAR(14) NOT NULL,
    nome_candidato VARCHAR(50) NOT NULL,
    telefone_candidato CHAR(12),
    email_candidato VARCHAR(100),
    local_candidato VARCHAR(100),
    data_nasc_candidato DATE,
    estado_civil_candidato VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela Vagas
CREATE TABLE IF NOT EXISTS vagas (
    id_vagas INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_candidato INT NOT NULL,
    local_vagas VARCHAR(100),
    requisitos_vagas TEXT,
    salario_vagas DECIMAL(10,2),
    vinculo_vagas VARCHAR(50),
    beneficios_vagas TEXT,
    ramo_vagas VARCHAR(50),
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa) ON DELETE CASCADE,
    FOREIGN KEY (id_candidato) REFERENCES candidato(id_candidato) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela Curriculo
CREATE TABLE IF NOT EXISTS curriculo (
    id_curriculo INT AUTO_INCREMENT PRIMARY KEY,
    id_candidato INT NOT NULL,
    descricao_curriculo VARCHAR(250),
    exper_profissional_curriculo TEXT,
    exper_academico_curriculo TEXT,
    certificados_curriculo TEXT,
    endereco_curriculo VARCHAR(50),
    linkedln_curriculo VARCHAR(50),
    FOREIGN KEY (id_candidato) REFERENCES candidato(id_candidato) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
