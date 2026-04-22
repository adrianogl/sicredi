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

## Dev

- API: `npm run dev:api`
- Front: `npm run dev:front`
- Ambos juntos: `npm run dev`
