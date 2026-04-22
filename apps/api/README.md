# Sicredi API

API REST pra gerenciar pautas (motions) e sessões de votação — teste técnico Sicredi.

Stack: **Laravel 13 + PHP 8.3+ + MySQL 8.4 + Sail**. OpenAPI via [Scramble](https://scramble.dedoc.co).

> O enunciado pedia Java + Spring Boot; resolvi ir de Laravel. As decisões de
> arquitetura (e por que cada uma) estão no README da raiz do monorepo.

> Código, tabelas, classes e payloads JSON estão em inglês. `motion` é o equivalente
> técnico de "pauta" em assembleias (Robert's Rules of Order). O README tá em
> pt-BR pra facilitar leitura.

## Rodar

Na raiz do monorepo:

```bash
npm run setup:api   # .env, deps, containers, migrations
npm run dev:api     # Sail em primeiro plano
```

A API sobe em `http://localhost:8000`.

## Endpoints (v1)

Docs interativos em http://localhost:8000/docs/api.

| Método | URL | O que faz |
| ------ | --- | --------- |
| POST   | `/api/v1/motions` | Cria pauta |
| GET    | `/api/v1/motions` | Lista pautas (paginado) |
| POST   | `/api/v1/motions/{motion}/sessions` | Abre sessão (default 60s, `duration_seconds` opcional) |
| POST   | `/api/v1/sessions/{session}/votes` | Registra voto (`member_id`, `option` = `Yes` ou `No`) |
| GET    | `/api/v1/motions/{motion}/result` | Contagem Yes/No/total |
| GET    | `/api/v1/ui/motions` | Tela SELECAO (Anexo 1) |
| GET    | `/api/v1/ui/sessions/{session}/vote` | Tela FORMULARIO (Anexo 1) |

### Erros

| Status | Code | Quando rola |
| ------ | ---- | ----------- |
| 403 | `MEMBER_NOT_ELIGIBLE` | user-info disse `UNABLE_TO_VOTE` ou 404 |
| 404 | — | Pauta ou sessão não existe |
| 409 | `SESSION_CLOSED` | Tentou votar depois do `closes_at` |
| 409 | `DUPLICATE_VOTE` | Membro já votou nessa sessão |
| 422 | `VALIDATION_FAILED` | Payload inválido |
| 503 | `EXTERNAL_SERVICE_UNAVAILABLE` | user-info fora do ar |

## Variáveis de ambiente

| Env | Default | O que é |
| --- | ------- | ------- |
| `APP_PORT` | `8000` | Porta do Sail |
| `APP_CALLBACK_DOMAIN` | `http://localhost:8000` | Domínio das URLs que voltam nas mensagens do Anexo 1. **Troque pelo IP da máquina ao testar em celular.** |
| `USER_INFO_URL` | `https://user-info.herokuapp.com` | Serviço de elegibilidade (Bônus 1) |
| `USER_INFO_ENABLED` | `true` | Desliga a integração (útil em testes manuais) |
| `USER_INFO_TIMEOUT` | `5` | Timeout em segundos |
| `API_VERSION` | `1.0.0` | Versão exposta no OpenAPI |

## Testes e qualidade

```bash
./vendor/bin/sail pest                                          # 23 testes
./vendor/bin/sail php ./vendor/bin/phpstan analyse              # Larastan level 6
./vendor/bin/sail php ./vendor/bin/pint                         # Pint
```

## Como o código está organizado

```
app/
├── Enums/                  → VoteOption, MemberStatus
├── Exceptions/             → exceções de domínio + render() pra HTTP
├── Http/
│   ├── Controllers/Api/    → single-action por recurso
│   ├── Requests/           → validação (FormRequest)
│   └── Resources/          → serialização JSON
├── Models/                 → Motion, VotingSession, Vote
├── Providers/              → bindings das interfaces de repositório
├── Repositories/           → acesso a dados (Contracts/ + impls Eloquent)
├── ScreenMessages/         → DTOs do Anexo 1 (FORMULARIO / SELECAO)
└── Services/               → VoteService, UserInfoClient
```
