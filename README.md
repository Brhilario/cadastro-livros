# Sistema de Cadastro de Livros

Aplicação web desenvolvida em PHP 8.2 (sem frameworks) com arquitetura MVC / Domain-Driven, MySQL 8.0 e ambiente totalmente conteinerizado com Docker.

---

## 🏛️ Arquitetura do Projeto

```text
cadastro-livros/
├── database/
│   ├── migrations/      # Migrações SQL versionadas
│   ├── seeds/           # Cargas iniciais de dados
│   └── migrate.php      # Runner de migrações e seeds
├── docker/
│   ├── nginx/           # Configuração do servidor web Nginx
│   └── php/             # Dockerfile (PHP 8.2-FPM + extensões)
├── public/
│   └── index.php        # Front Controller e roteamento
├── src/
│   ├── Controllers/     # Controladores das requisições HTTP
│   ├── Core/            # Conexão PDO (Database) e Router
│   ├── Domain/
│   │   ├── Entities/    # Entidades de negócio com validações
│   │   └── Exceptions/  # Exceções customizadas de domínio
│   ├── Models/          # Acesso a dados e persistência (PDO)
│   └── Views/           # Templates de interface (HTML / Bootstrap 5)
├── tests/               # Testes automatizados (PHPUnit)
├── docker-compose.yml   # Orquestração dos containers (App, Nginx, DB)
└── .env.example         # Modelo de variáveis de ambiente
```

---

## 🚀 Comandos Básicos

### 1. Inicializar o Ambiente

```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Subir os containers
docker compose up -d --build

# Instalar dependências
docker compose exec app composer install

# Executar migrações e popular dados de teste
docker compose exec app php database/migrate.php --seed
```

Aplicação disponível em: **[http://localhost:8080](http://localhost:8080)**

---

### 2. Comandos do Dia a Dia

```bash
# Parar os containers
docker compose down

# Ver logs da aplicação
docker compose logs -f

# Acessar terminal do container PHP
docker compose exec app bash

# Executar testes automatizados
docker compose exec app ./vendor/bin/phpunit
```
