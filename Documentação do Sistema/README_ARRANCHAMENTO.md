
# Sistema  Arranchamento

Este projeto é um sistema web completo para **agendamento e controle de arranchamento (refeições militares)**. Ele permite aos usuários e administradores cadastrar, consultar e cancelar refeições (Café da manhã, Almoço, Jantar) em datas específicas, com funcionalidades visuais modernas, segurança reforçada e controle de duplicidade.

## 🧰 Tecnologias Utilizadas

- **Linguagem Principal:** PHP (versão 7.4 ou superior)
- **Banco de Dados:** MySQL
- **Front-end:**
  - HTML5
  - Bootstrap 5.3.3
  - Font Awesome (ícones visuais)
  - JavaScript (puro + SweetAlert2)
- **Bibliotecas:**
  - SweetAlert2 (alertas e confirmações visuais)
  - PDO (PHP Data Objects) para segurança em conexões com banco de dados

## 🎯 Funcionalidades Principais

### ✅ Agendamento de Refeições
- Seleção de militar com **nome + posto/graduação**
- Escolha da refeição (Café, Almoço ou Jantar)
- Escolha da data do agendamento
- Prevenção de agendamentos duplicados para o mesmo militar, refeição e data

### 🔍 Pesquisa e Filtros
- Campo de busca para pesquisar militares por nome
- Filtro automático em tempo real via GET

### 📋 Listagem de Arranchamentos
- Tabela organizada com data, refeição e nome/posto do militar
- Exibição dinâmica dos agendamentos cadastrados
- Scroll responsivo para dispositivos móveis

### ❌ Cancelamento de Arranchamento
- Botão moderno com ícone para cancelar
- Confirmação visual com SweetAlert2 antes de excluir
- Exclusão segura com proteção contra manipulação de dados

## 🔐 Recursos de Segurança Implementados

### 1. **Validação e Saneamento de Dados**
- Todos os dados de entrada são validados com `htmlspecialchars()` e `trim()` para prevenir **XSS (Cross-Site Scripting)**.
- Função personalizada `sanitize_input()` usada para limpar entradas de usuários.

### 2. **Proteção Contra SQL Injection**
- Todas as consultas ao banco de dados utilizam **prepared statements (PDO)** com bind de parâmetros.

### 3. **Proteção Contra Back Navigation**
- Uso de JavaScript `window.history.forward()` para evitar o uso do botão voltar após ações sensíveis.

### 4. **Mensagens e Alertas Seguros**
- Todas as mensagens usam SweetAlert2 para evitar `alert()` básicos e melhorar a UX com **confirmação visual** segura.

## 👤 Estrutura de Usuários

- Tabela `users`: contém `id`, `nome`, `posto`, e demais dados do militar
- Campo `posto`: permite identificar a graduação (ex: Cabo, Sargento, etc.)
- Os militares são exibidos em campos de seleção com `NOME - POSTO`

## ⚙️ Estrutura de Banco de Dados (Simplificada)

### Tabela `users`
| Campo      | Tipo         |
|------------|--------------|
| id         | INT (PK)     |
| nome       | VARCHAR(255) |
| posto      | VARCHAR(100) |

### Tabela `arranchamento`
| Campo      | Tipo         |
|------------|--------------|
| id         | INT (PK)     |
| user_id    | INT (FK)     |
| refeicao   | VARCHAR(50)  |
| data       | DATE         |

## 💡 Requisitos Mínimos

- PHP 7.4+
- MySQL/MariaDB
- Servidor Web (Apache, Nginx ou XAMPP/Laragon)
- Conexão com banco via PDO habilitada

## 📦 Como Instalar

1. Clone este repositório ou envie os arquivos para seu servidor:
   ```bash
   git clone https://github.com/seuusuario/arranchamento.git
   ```

2. Crie o banco de dados MySQL e execute as tabelas `users` e `arranchamento`.

3. Atualize seu arquivo `db.php` com as credenciais do banco:
   ```php
   $pdo = new PDO("mysql:host=localhost;dbname=nome_do_banco", "usuario", "senha");
   ```

4. Acesse `arranchar.php` via navegador para iniciar o sistema.

### 🔐 Acessar o sistema
    ✅Login:klein98@gmail.com
    ✅senha:123

## 👮‍♂️ Desenvolvedor
    Alessandro Klein
📞 51-992922878
🔗 https://github.com/Alessandro-Klein
✉️ Email: alessandroklein98@gmail.com
Este sistema foi criado para facilitar a **gestão de refeições em ambientes militares**, com foco em segurança, praticidade e design moderno.
