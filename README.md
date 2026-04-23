# Sicredi Monorepo

Teste técnico Sicredi em formato monorepo:

- `apps/api` - API Laravel 13 rodando em Laravel Sail (Docker).
- `apps/front` - cliente web em Next.js 16 (React 19, TS). O teste avalia só o backend; o front está aqui só pra eu conseguir dogfoodar a API.

## Antes de rodar

- Docker + Docker Compose (pra subir o Sail).
- Node 24.13 (tem `.nvmrc`).

```bash
nvm install
nvm use
```

## Setup

```bash
npm run setup
```

Isso instala as deps de API e front, sobe o Sail, gera `APP_KEY` e roda as migrations. Se quiser separar, `npm run setup:api` e `npm run setup:front`.

## Dev

| O quê | Comando | URL |
|---|---|---|
| Só a API | `npm run dev:api` | http://localhost:8000 |
| Só o front | `npm run dev:front` | http://localhost:3000 |
| Os dois juntos | `npm run dev` | ambos |

---

### Stack: Laravel em vez de Java/Spring

O requisito pedia Java + Spring Boot. Fui de Laravel por produtividade e conforto. Nenhum requisito funcional, técnico, de qualidade ou bônus ficou de fora por causa disso.

### Organização do código

- **Controllers single-action** (`__invoke`) agrupados por recurso em `app/Http/Controllers/Api/<Recurso>/`. Nomes verbais tipo `StoreMotionController`, `ShowMotionResultController` - cada classe cuida de uma ação só.
- **Repositories** em `app/Repositories/` com interfaces em `Contracts/`. Não é default Laravel, mas deixa fácil mockar dependências nos testes unitários.
- **Service só onde precisa.** Se a classe ia ser um "chama repo e loga", fica no controller mesmo. Sobraram `VoteService` (regra de voto) e `UserInfoClient` (HTTP externo).

### Versionamento (Bônus 3)

Usei **URI versioning** com prefixo `/api/v1/...`. O prefixo mora no `bootstrap/app.php` (`apiPrefix: 'api/v1'`). **Não** criei sufixo `\V1\` nos namespaces.

### Mensagens de tela (Anexo 1)

Tudo em pt-BR: `FORMULARIO`, `SELECAO`, `TEXTO`, `NUMERO`, `DATA`, e as chaves `tipo`, `titulo`, `itens`, `botoes`, `texto`. Os value objects estão em `app/ScreenMessages/`.

### URLs de callback configuráveis

O domínio precisa poder mudar. `APP_CALLBACK_DOMAIN` resolve - `App\ScreenMessages\CallbackUrl::to('/rota')` monta tudo a partir dele.

### Erros padronizados

Cada exception de domínio tem status HTTP + `code` estável:

| Exception | Status | Code |
|---|---|---|
| `DuplicateVoteException` | 409 | `DUPLICATE_VOTE` |
| `VotingSessionClosedException` | 409 | `SESSION_CLOSED` |
| `MemberNotEligibleException` | 403 | `MEMBER_NOT_ELIGIBLE` |
| `ExternalServiceUnavailableException` | 503 | `EXTERNAL_SERVICE_UNAVAILABLE` |
| `ValidationException` (handler global) | 422 | `VALIDATION_FAILED` |

Voto duplicado é barrado por **unique constraint no banco** em vez de `exists()` antes do insert.

### Documentação da API

Scramble gera OpenAPI sozinho a partir dos controllers + FormRequests + Resources. Ainda dei uma enriquecida com `#[Endpoint]`, `#[Group]`, `#[Response]`. Docs interativos em http://localhost:8000/docs/api.

### Performance (Bônus 2)

Não tem ferramenta de benchmark, o escopo fica mais enxuto sem. A parte que importa (`Vote::query()->groupBy('option')->selectRaw('COUNT(*) as total')`) aguenta centenas de milhares de votos graças ao índice composto `(voting_session_id, option)` em `votes`.

### Testes

- **Feature** em `tests/Feature/Api/` - 18 testes. Bateria HTTP → Eloquent → SQLite em memória. Cobre happy path e os erros principais (duplicado, sessão fechada, sessão expirando na hora, pauta inexistente, user-info retornando ABLE/UNABLE/404, payload inválido).
- **Unit** em `tests/Unit/Services/VoteServiceTest.php` - 5 testes com `UserInfoClient` e `VoteRepositoryInterface` mockados. Cobre todos os branches da regra sem encostar no banco.
- `Http::preventStrayRequests()` ligado em todos os feature tests - nenhum bate em rede real.

### Qualidade

- **PHPStan level 6** + Larastan
- **Pint** (PSR-12 extended) - código sempre formatado.
- **Pest** como runner.

---

### Front

O `apps/front` é Next.js 16 (App Router) + React 19 + TS strict + TailwindCSS 4 + shadcn/ui + TanStack Query v5 + Jest + RTL. É um **cliente "sem lógica"**: consome as mensagens JSON e renderiza genericamente.

#### Telas

| Rota | Endpoint                           | O que renderiza |
|---|------------------------------------|---|
| `/` | -                                  | redirect server-side → `/motions` |
| `/motions` | `GET /api/v1/ui/motions`           | tela `SELECAO` + botão "Nova pauta" |
| `/motions/new` | `GET /api/v1/ui/motions/new`       | tela `FORMULARIO` de cadastro |
| `/sessions/:id/vote` | `GET /api/v1/ui/sessions/:id/vote` | tela `FORMULARIO` de voto |

#### Como funciona

1. Cada página chama `useScreenMessage(path)` (React Query) e recebe `{tipo, titulo, itens, botoes…}`.
2. Um renderer genérico (`SelectionScreen` / `FormScreen`) monta a UI a partir do `tipo`.
3. Ao clicar num botão:
   - **padrão** → POST pra `button.url` com o estado do form no body.
   - **`metodo: 'GET'`** (extensão ao Anexo 1 para Cancelar) → extrai o pathname e navega via Next router.

#### Decisões rápidas

- **shadcn/ui + Tailwind puro** em vez de MUI. Bundle leve, são só 5 componentes.
- **`fetch` + wrapper fino** em `lib/api.ts` (sem axios — não tem auth pra justificar).
- **`useState` ad-hoc** pros forms (2-3 campos cada); sem react-hook-form/zod.
- **`@base-ui/react`** é o primitivo novo do shadcn no Next 16 - **não tem `asChild`**. Quando preciso de link com cara de botão, uso `<Link className={buttonVariants()}>`.

#### Mock do Bonus 1

O Heroku original do Bonus 1 (`user-info.herokuapp.com`) estava fora do ar. Pra não depender dele, o próprio backend serve um mock em `GET /api/v1/mock/users/{cpf}`: aleatório entre `ABLE_TO_VOTE` / `UNABLE_TO_VOTE`; retorna 404 quando o CPF termina em `000` (pra você exercitar esse branch). `USER_INFO_URL` no `.env` já aponta pra ele.

#### Testes

`npm -w apps/front test` → **23 testes** em Jest + RTL. `lib/api`, hook de query, e cada componente de tela. Mocka HTTP e `next/navigation`; nenhum teste bate em servidor real.
