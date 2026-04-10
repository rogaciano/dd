# Sistema de Denúncias Anônimas (Nova V1)

Bem-vindo ao repositório do novo Sistema de Denúncias Anônimas! Este projeto foi modernizado utilizando a arquitetura de **Monolito Moderno** com foco em segurança, anonimato e alta performance. 

*Para a visão arquitetural completa de bancos, stack e modelagem, consulte as documentações contidas na pasta principal: `BANCO_V1.md`, `MODELO_DOMINIO.md`, `STACK.md` e `IMPORTACAO_E_EVOLUCAO.md`.*

---

## 🚀 Tecnologias

- **Backend:** Laravel 11.x (PHP 8.3)
- **Frontend:** Vue.js 3 + Inertia.js
- **Estilização:** Tailwind CSS v4 (Design system Glassmorphism)
- **Banco de Dados:** PostgreSQL 16
- **Cache/Filas:** Redis
- **Infraestrutura Local:** Docker & Docker Compose (`php:8.3-apache`)

---

## 💻 Onboarding: Como Rodar Localmente

Todo o ambiente está empacotado no `docker-compose.yml`. Siga os passos abaixo, em sua máquina, para subir a aplicação em 5 minutos:

### 1. Clonar Repositório e Configurar Variáveis
```bash
git clone https://github.com/rogaciano/dd.git
cd dd
cp sistema/.env.example sistema/.env
```
*(As credenciais padrões do PostgreSQL e Redis do Docker já estão configuradas por padrão no `.env` original)*

### 2. Subir os Containers
```bash
docker-compose up -d --build
```
Isso criará 3 serviços:
1. `php_apache_sistema` (A aplicação exposta na porta HTTP **8080**)
2. `postgres` (Bancos locais na porta **5432**)
3. `redis` (Filas e Cache na porta **6379**)

### 3. Instalar as Dependências no Container
Acesse o container principal do PHP para rodar os pacotes de backend e frontend:
```bash
docker exec -it php_apache_sistema bash

# Dentro do container:
cd /var/www/html/sistema
composer install
npm install
npm run build
```

### 4. Preparar Regras Iniciais e Banco de Dados
Ainda dentro do container:
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```
Isso criará a estrutura limpa e já injetará a árvore de papéis, assuntos padrões e os tipos de resultados oficiais. 
**Usuário de Acesso Padrão criado:** `admin@admin.com` | **Senha:** `admin123`

---

## 🏛 Lidando com a Base Legada (Frente B)
Como lidamos com anos de dados históricos do SQL Server, adotamos uma estratégia de 2 vias. Existe um espelho do BD Antigo nomeado de `disque_denuncia_legado`. 

Para projetar os dados do sistema legado para a nossa modelagem da V1 localmente, existe um comando Artisan pronto com Upsert que constrói de forma inteligente o Protocolo (respeitando o layout `seq.mês.ano`) e insere na nova modelagem sem causar duplicação:

```bash
docker exec php_apache_sistema php artisan legado:importar-denuncias
```

---

## Estrutura de Rotas e Endpoints
- **`/`**: Portal Anônimo de Denúncias Público (Vue/Inertia).
- **`/dashboard`**: Painel Administrativo de Controle e Triagem.
- **`/horizon`**: Painel de Monitoramento de Filas e Jobs assíncronos. 

Em caso de dúvidas na modelagem do domínio, sinta-se livre para debater nas PRs embasando suas ideias no `MODELO_DOMINIO.md`!
