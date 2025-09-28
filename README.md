# Orçamentos PHP/PDO

Mini-aplicação em PHP/PDO para cadastro de **fornecedores**, **materiaPri** e **montagem de orçamentos** com:
- Auto-preenchimento por CEP (ViaCEP) para endereço do fornecedor
- Itens dinâmicos com materiaPri cadastrados (adicionar/remover linhas)
- Atualização automática do preço/unidade do materiaPri se alterado no orçamento
- Geração de PDF (usa dompdf) com nome `NOME_FANTASIA_YYYY-MM-DD_HHMMSS.pdf` salvo em `storage/pdfs`
- Tela para buscar orçamentos por fornecedor e data, com opção de editar

## Requisitos
- PHP 8.1+ com PDO (SQLite habilitado por padrão ou MySQL)
- Composer para instalar `dompdf/dompdf` (apenas para gerar PDF)
- Opcional: Servidor web (Apache/Nginx) ou `php -S localhost:8000 -t public`

## Instalação rápida (SQLite)
```bash
cd orcamentos-pdo
php init.php      # cria database.sqlite e todas as tabelas
cd public
php -S localhost:8000
# Acesse http://localhost:8000
```

## PDF (dompdf)
Instale uma vez na raiz do projeto:
```bash
composer require dompdf/dompdf
```
Depois, para visualizar ou salvar PDF:
- Visualizar: `public/orcamento_pdf.php?id=1`
- Salvar: `public/orcamento_pdf.php?id=1&save=1` (salva em `storage/pdfs`)

## Mudar para MySQL
Edite `config.php` e ajuste o DSN/usuário/senha (comente o SQLite e descomente o bloco MySQL). Execute o conteúdo de `schema.sql` no seu MySQL.

## Estrutura
- `config.php`, `schema.sql`, `init.php`
- `public/`
  - `_header.php`, `_footer.php`, `index.php`
  - `fornecedores.php` (CRUD + ViaCEP)
  - `materiaPri.php` (CRUD)
  - `orcamento_novo.php` (montagem/edição do orçamento com itens dinâmicos)
  - `orcamentos.php` (busca/lista/atalhos para PDF e edição)
  - `orcamento_pdf.php` (gera/baixa/salva PDF)
  - `assets/api_materiaPri.php`, `assets/api_fornecedores.php`
- `storage/pdfs/` (saída dos PDFs)
