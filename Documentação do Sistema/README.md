# Sistema de Agendamento de Arranchamento

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

---

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

---

## 🔐 Recursos de Segurança Implementados

### 1. **Validação e Saneamento de Dados**
- Todos os dados de entrada são validados com `htmlspecialchars()` e `trim()` para prevenir **XSS (Cross-Site Scripting)**.
- Função personalizada `sanitize_input()` usada para limpar entradas de usuários.

### 2. **Proteção Contra SQL Injection**
- Todas as consultas ao banco de dados utilizam **prepared statements (PDO)** com bind de parâmetros.
- Exemplo:
  ```php
  $stmt = $pdo->prepare("SELECT * FROM arranchamento WHERE user_id = :id");
  $stmt->bindParam(':id', $id);


### 🔐 Acessar o sistema
    ✅Login:klein98@gmail.com
    ✅senha:123
    
## 👮‍♂️ Desenvolvedor
📞 51-992922878
🔗 https://github.com/Alessandro-Klein
✉️ Email: alessandroklein98@gmail.com

Este sistema foi criado para facilitar a **gestão de refeições em ambientes militares**, com foco em segurança, praticidade e design moderno.
