# Sistema de Denuncias Anonimas

Projeto Laravel para a nova V1 do sistema de denuncias, com duas bases PostgreSQL locais:

- `disque_denuncia_legado`: espelho recarregavel do SQL Server 2008
- `disque_denuncia_novo`: base da aplicacao nova

Documentacao complementar:

- `STACK.md`
- `V1_ESCOPO.md`
- `MODELO_DOMINIO.md`
- `BANCO_V1.md`
- `IMPORTACAO_E_EVOLUCAO.md`

## Stack atual

- Backend: Laravel 13 + PHP 8.3
- Frontend: Vue 3 + Inertia
- Estilo: Tailwind + Vite
- Banco: PostgreSQL 16
- Cache e filas: Redis
- Infra local: Docker Compose

## Subir o ambiente

1. Copie o ambiente da aplicacao:

```bash
cp sistema/.env.example sistema/.env
```

2. Suba os containers:

```bash
docker compose up -d --build
```

3. Instale dependencias e prepare a aplicacao:

```bash
docker exec -it php_apache_sistema bash
cd /var/www/html/sistema
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate --seed
```

Servicos locais:

- App: `http://localhost:8080`
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`

Usuario padrao de desenvolvimento:

- Email: `admin@admin.com`
- Senha: `admin123`

## Testes

O suite usa SQLite para testes. A imagem Docker da aplicacao instala `pdo_sqlite`, entao o caminho recomendado e:

```bash
docker exec -it php_apache_sistema bash
cd /var/www/html/sistema
php artisan test
```

Se o PHP local nao tiver `pdo_sqlite`, os testes com banco serao ignorados.

## Fluxo do legado

1. Carregue o SQL Server 2008 no banco `disque_denuncia_legado` usando os scripts Python da raiz.
2. Projete o legado para o modelo novo com os comandos Artisan.

Comandos principais:

```bash
docker exec -it php_apache_sistema bash
cd /var/www/html/sistema
php artisan legado:importar-denuncias
php artisan legado:importar-veiculos
```

## Observacoes de arquitetura

- O espelho legado e descartavel e nao deve receber ajuste manual.
- O banco novo evolui por migrations, seeders e comandos de projecao controlados.
- Dados complementares da V1 devem ficar separados da carga legado para permitir reimportacao segura.
