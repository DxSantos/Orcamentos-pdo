-- Criação das tabelas
CREATE TABLE IF NOT EXISTS fornecedores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    razao_social TEXT NOT NULL,
    nome_fantasia TEXT NOT NULL,
    endereco TEXT,
    numero TEXT,
    bairro TEXT,
    cep TEXT,
    cidade TEXT,
    uf TEXT,
    email TEXT,
    condicao_pagamento TEXT,
    cnpj TEXT,
    telefone TEXT,
    created_at TEXT DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS materiaPri (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    preco_unitario REAL NOT NULL DEFAULT 0,
    unidade_medida TEXT NOT NULL DEFAULT 'un',
    updated_at TEXT DEFAULT (datetime('now','localtime'))
);

CREATE TABLE IF NOT EXISTS orcamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fornecedor_id INTEGER NOT NULL,
    data_hora TEXT NOT NULL DEFAULT (datetime('now','localtime')),
    observacoes TEXT,
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orcamento_itens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    orcamento_id INTEGER NOT NULL,
    materiaPri_id INTEGER,
    descricao TEXT,
    unidade_medida TEXT NOT NULL,
    quantidade REAL NOT NULL DEFAULT 1,
    preco_unitario REAL NOT NULL DEFAULT 0,
    total REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (materiaPri_id) REFERENCES materiaPri(id) ON DELETE SET NULL
);

-- Índices úteis
CREATE INDEX IF NOT EXISTS idx_fornecedores_nome ON fornecedores (nome_fantasia);
CREATE INDEX IF NOT EXISTS idx_materiaPri_nome ON materiaPri (nome);
CREATE INDEX IF NOT EXISTS idx_orcamentos_fornecedor ON orcamentos (fornecedor_id);
