# Sicredi Monorepo

Monorepo contendo:

- `apps/api` — Backend Laravel 13 rodando em [Laravel Sail](https://laravel.com/docs/sail) (Docker)
- `apps/front` — Frontend Next.js 16 (React 19, TS)

## Dependências

- [Docker](https://www.docker.com/) + Docker Compose (para o Sail)
- [Node.js](https://nodejs.org/) `24.13` (gerenciado via [nvm](https://github.com/nvm-sh/nvm) — ver `.nvmrc`)
- npm (vem com Node)

```bash
nvm install
nvm use
```

## Setup

Instala deps de API e front, sobe o Sail, gera APP_KEY e roda migrations:

```bash
npm run setup
```

Ou individualmente: `npm run setup:api` / `npm run setup:front`.

## Dev

- API: `npm run dev:api` → http://localhost:8000
- Front: `npm run dev:front` → http://localhost:3000
- Ambos juntos: `npm run dev`
