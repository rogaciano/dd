# Guia de Implantação em Produção (V1)

Este documento define a arquitetura e os passos cruciais para migrar o **Sistema de Denúncias** do ambiente local (Docker-compose clássico) para um ambiente Real de Produção de alta disponibilidade e segurança.

## 1. Topologia de Servidores Recomendada
Em produção, não é recomendado armazenar Banco de Dados dentro do mesmo servidor web usando volumes simples do Docker para evitar gargalos de I/O e assegurar backups granulares.

* **Servidor Web (Aplicação):** Uma ou mais instâncias (ex: VPS na Linode/AWS EC2) rodando apenas os containers da Aplicação Laravel + Apache/Nginx.
* **Database as a Service (PostgreSQL):** Um banco PostgreSQL Dedicado/Gerenciado.
* **Cache/Fila (Redis):** Gerenciado externamente (RedisLabs, AWS ElastiCache) ou uma VPS contendo apenas a instância do Redis otimizada.

## 2. Ajustes Severos no `.env` 
Jamais utilize os dados de ambiente "local" em Produção. No seu servidor da nuvem, você precisará isolar o `.env`:
```env
APP_ENV=production
APP_DEBUG=false       <-- CRÍTICO: Nunca deixe True no ar para não sofrer vazamento de chaves
APP_URL=https://disquedenuncia.org.br

# Apontamentos Externos de Banco (Jamais "postgres" local do docker)
DB_HOST=192.168.xxx.xxx 
DB_DATABASE=sistema_denuncias
DB_USERNAME=usuario_seguro
DB_PASSWORD=senha_segura
```

## 3. Comandos de Otimização (Laravel)
O Laravel "solto" no ambiente local compila arquivos toda vez que alguém dá F5. Em produção você precisa engessar e armazenar as rotas em Ram para velocidade máxima:

No servidor de produção, dentro do seu container da Aplicação, execute:
```bash
php artisan optimize        # Congela rotas e injeções
php artisan view:cache      # Minifica e junta os blades
php artisan config:cache    # Salva o .env de forma criptografada temporária
```

### 3.1. Front-end Compilado
O código do painel Vue.JS nunca roda o `npm run dev` em Produção. Você faz o *build* dos _assets_ que geram um HTML/JS puro extremamente veloz:
```bash
npm run build
```

## 4. Workers em Segundo Plano (Filas e Horizon)
Como criamos uma camada de filas no Redis (para não travar a denúncia enquanto o Laravel criptografa chaves ou anexa mídias pesadas), a produção exige um robô consumindo as filas o tempo todo. 
Você utilizará uma rotina para manter esse comando alerta 24/7 (Geralmente usando o pacote gerenciador `Supervisor` do Linux):
```bash
php artisan horizon
```

## 5. Como o Banco Antigo Chegará Lá? (A Frente B na Nuvem)
1. Antes do lançamento, você subirá no Servidor do Banco de Dados a sua tabela estática final `disque_denuncia_legado` (dump do seu SQL Server histórico atualizado na madrugada da virada do sistema).
2. Configurará no seu `.env` de produção a chave `DB_HOST_LEGADO` batendo no IP desse novo Dump.
3. Operará silenciosamente o nosso robô: `php artisan legado:importar-denuncias` de dentro do servidor em nuvem.
4. O Laravel transformará todos os milhões de registros legados nos dados polidos dentro do `sistema_denuncias` sem afetar a produção, convertendo os IPs e protocolos de forma perfeita.

---
> [!CAUTION] 
> Jamais deixe a pasta raiz (com `.env` e `/storage`) pública para a web (Apache/Nginx). Configure seu Web Server (ex: NGINX) apontando rigidamente para a pasta `/public` do projeto, caso contrário você sofrerá vazamento de chaves de API.
