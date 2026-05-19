# Official Third-Party Sportsbook Wallet Provider Integration Protocol

**Version:** 1.0
**Status:** FROZEN — Production Contract
**Platform:** VRB Casino (PHP)
**Last Updated:** 2026-05-11

---

## 1. System Overview

### 1.1 Architecture

The VRB Casino Platform is a PHP-based gaming host that supports third-party sportsbook operators as upstream wallet providers. The casino exposes a JWT-authenticated launch URL; the provider exposes a wallet HTTP API. All real-money state is owned by the provider. The casino acts as a stateless game-execution layer.

```
 ┌────────────────────────┐         ┌──────────────────────────┐
 │ Third-Party Sportsbook │         │     VRB Casino           │
 │  (Provider / Operator) │         │  (Game Execution Layer)  │
 ├────────────────────────┤         ├──────────────────────────┤
 │ - JWT Issuer           │  JWT →  │ - JWT Validator          │
 │ - Wallet Owner         │  ←─────  │ - Game Catalog          │
 │ - Transaction Ledger   │  HTTP   │ - Session Manager        │
 │ - Wallet HTTP API      │  POST   │ - Provider HTTP Client   │
 └────────────────────────┘         └──────────────────────────┘
```

### 1.2 Roles

| Role | Owner | Responsibilities |
|------|-------|------------------|
| Authentication | Provider | Issues signed JWT before launch |
| Wallet of Record | Provider | Persists real & freeplay balances |
| Transaction Ledger | Provider | Persists every bet / win |
| Game Execution | Casino | Renders games, computes outcomes |
| Session Lifecycle | Casino | Maintains PHP session post-launch |
| Player Provisioning | Casino | Auto-creates local player record on first launch |

### 1.3 Authentication Flow

```
1. Player clicks "Casino" in provider's sportsbook UI.
2. Provider mints HS256 JWT with player + agent + company claims.
3. Provider redirects player to:  https://<casino>/?token=<JWT>
4. Casino validates token signature & expiration.
5. Casino resolves company by `cid`; provisions player/agent if first launch.
6. Casino establishes PHP session, stores raw JWT for downstream wallet calls.
7. Every wallet operation (balance, bet, prize) is a server-to-server POST
   from casino to provider, carrying the same JWT as proof of session.
```

---

## 2. JWT Specification

| Property | Value |
|---|---|
| Algorithm | `HS256` (HMAC-SHA256) — **fixed, no negotiation** |
| Header | `{ "typ": "JWT", "alg": "HS256" }` |
| Secret distribution | Out-of-band, per-provider; stored in `config.php` under `jwt.secret` |
| Issuer (`iss`) | Provider-supplied or `casino.zytom-studios.com` |
| Default lifetime | `3600` seconds (1 hour) |
| Encoding library | `firebase/php-jwt` |
| Transport | HTTPS query parameter `?token=<JWT>` on launch URL |

### 2.1 Validation Rules

The casino rejects the launch with `Invalid or expired token.` if any of the following hold:

1. Token does not parse as a JWT (header.payload.signature, base64url).
2. Signature does not verify against the configured shared secret.
3. `exp` is in the past.
4. Token decoding throws any exception.
5. `cid` does not match a known company record.

Clock skew tolerance: **0 seconds** (no leeway). Providers must keep their clocks NTP-synced.

### 2.2 Sample Payload

```json
{
  "iss": "casino.zytom-studios.com",
  "iat": 1746979200,
  "exp": 1746982800,
  "cid": 12,
  "player_id": 884112,
  "username": "MACTEST",
  "agent_id": 4501,
  "agent_account": "AGENT01"
}
```

> **Note:** `currency` is intentionally absent from the JWT. See §3 and §6.3.

---

## 3. Required JWT Payload

| Claim | Type | Required | Description |
|---|---|---|---|
| `cid` | integer | **YES** | Company ID assigned by casino to the provider. |
| `player_id` | integer / string | **YES** | Provider-side unique player identifier. Stored as `external_id` in the casino DB. |
| `username` | string | **YES** | Player account. Case-insensitive; casino uppercases on store and lookup. |
| `iat` | integer (unix) | **YES** | Issued-at timestamp. |
| `exp` | integer (unix) | **YES** | Expiration timestamp. Recommended ≤ 1 hour from `iat`. |
| `agent_id` | integer | Optional | Affiliate/agent ID. `0` if not applicable. |
| `agent_account` | string | Optional | Affiliate/agent username (uppercased). `""` if not applicable. |
| `iss` | string | Optional | Issuer identifier (informational). |

**Notes:**
- `username` collisions across companies are allowed — uniqueness is scoped to `(account, company)`.
- On every launch, casino re-syncs `external_id` and `agent` from the JWT, so providers may rotate these without breaking the link.
- **`currency` is NOT a JWT claim.** Currency is owned by the casino's `company.currency` configuration and is communicated to the provider out-of-band at onboarding (per `cid`). Providers MUST NOT include `currency` in the JWT payload — if present, it is ignored by the casino.

---

## 4. Provider Wallet API

### 4.1 Endpoint

The provider MUST host a single endpoint, registered with the casino out-of-band:

```
POST https://<provider-host>/provider_wallet_api.php
```

The configured URL is stored on the casino in the per-company bridge connector
(`/utilities/api/bridge/connect.php`).

### 4.2 Request Format

- **Method:** `POST`
- **Content-Type:** `application/x-www-form-urlencoded`
- **Body parameters:**

| Field | Type | Required | Notes |
|---|---|---|---|
| `action` | string | YES | One of: `get_balance`, `place_bet`, `place_free_bet`, `credit_prize`. |
| `token` | string (JWT) | YES | Same JWT minted at launch. Provider re-validates signature. |
| `wallet` | string | conditional | `real` or `free`. Required for `credit_prize`. Defaults to `real`. |
| `bet_amount` | decimal | for bet actions | Positive, currency-precise. |
| `win_amount` | decimal | for `credit_prize` | Non-negative. |
| `game_id` | int/string | recommended | Casino game identifier (for ledger). |
| `game_name` | string | recommended | Human-readable game name (for ledger). |

### 4.3 Response Format

All responses MUST be a JSON object with the following canonical shape:

```json
{
  "error":    <integer error code, 0 = success>,
  "msg":      "<string, empty on success>",
  "balance":  <decimal, real wallet balance AFTER the operation>,
  "free":     <decimal, freeplay wallet balance AFTER the operation>,
  "currency": "<ISO 4217 currency code>",
  "account":  "<player username, uppercase>"
}
```

- On any non-zero `error`, casino aborts the game action and surfaces `msg` to the player.
- Even on success, both `balance` and `free` must be returned so the casino UI stays in sync.
- `currency` in the response is **informational** — it echoes the currency configured on the casino side for this `cid`. The provider MUST derive it from its own per-`cid` configuration (the value agreed at onboarding), NOT from any request field, and never from the JWT.

---

## 5. Supported Actions

### 5.1 `get_balance`

**Purpose:** Fetch the player's current real and freeplay balances.

**Request**
```
POST /provider_wallet_api.php
action=get_balance&token=<JWT>
```

**Response (success)**
```json
{
  "error": 0,
  "msg": "",
  "balance": 5500.00,
  "free": 200.00,
  "currency": "USD",
  "account": "MACTEST"
}
```

**Expected behavior:** Read-only. Must NOT mutate ledger or balance.

---

### 5.2 `place_bet`

**Purpose:** Debit the **real-money** wallet for a bet stake.

**Request**
```
POST /provider_wallet_api.php
action=place_bet&token=<JWT>&bet_amount=10.50&game_id=1&game_name=Blackjack
```

**Response (success)**
```json
{
  "error": 0,
  "balance": 5489.50,
  "free": 200.00,
  "currency": "USD",
  "account": "MACTEST"
}
```

**Expected behavior:**
- Atomically debit `bet_amount` from the real wallet.
- Reject if `bet_amount <= 0` → error `201`.
- Reject if `real_balance < bet_amount` → error `202`.
- Persist a transaction record (recommended fields: tx_id, type=`bet`, player, game_id, amount, balance_before, balance_after, timestamp).

---

### 5.3 `place_free_bet`

**Purpose:** Debit the **freeplay** wallet for a bet stake.

**Request**
```
POST /provider_wallet_api.php
action=place_free_bet&token=<JWT>&bet_amount=5.00&game_id=1&game_name=Blackjack
```

**Response (success)**
```json
{
  "error": 0,
  "balance": 5489.50,
  "free": 195.00,
  "currency": "USD",
  "account": "MACTEST"
}
```

**Expected behavior:**
- Atomically debit `bet_amount` from the freeplay wallet.
- Reject if `bet_amount <= 0` → error `211`.
- Reject if `free_balance < bet_amount` → error `212`.
- Persist transaction with type=`bet_free`.

---

### 5.4 `credit_prize`

**Purpose:** Credit winnings to the wallet indicated by the casino.

**Request**
```
POST /provider_wallet_api.php
action=credit_prize&token=<JWT>&win_amount=150.00&wallet=real&game_id=1&game_name=Blackjack
```

**Response (success)**
```json
{
  "error": 0,
  "balance": 5639.50,
  "free": 195.00,
  "currency": "USD",
  "account": "MACTEST"
}
```

**Expected behavior:**
- If `wallet=real` → credit `win_amount` to the real wallet.
- If `wallet=free` → credit `win_amount` to the freeplay wallet.
- Reject if `win_amount < 0` → error `301`.
- A `win_amount` of `0` is valid (no-op credit, used for "lose" rounds that still need a ledger row).
- Persist transaction with type=`win` or `win_free` accordingly.

---

## 6. Wallet Rules

### 6.1 Two Wallets, One Active

Every player has exactly two balances:
- **Real Balance** (`balance`) — fiat / cash-equivalent.
- **Freeplay Balance** (`free`) — bonus / promotional.

Exactly **one** wallet is "active" at any time, governed by the casino-side flag `using_free_play` on the player record.

### 6.2 Wallet Switching

- Switching is a **casino-side** decision. The provider does not select the wallet.
- For `place_bet` / `place_free_bet`, the casino picks the right action name based on `using_free_play`.
- For `credit_prize`, the casino sends the explicit `wallet` parameter so the provider does not have to track state.

### 6.3 Currency

- Currency is fixed at the **company** level (`company.currency` on the casino side, and the equivalent per-`cid` configuration on the provider side). The casino is the source of truth.
- Currency is **NOT** transmitted in the JWT and **NOT** transmitted in wallet API requests.
- The two sides MUST agree on the currency per `cid` at onboarding.
- The provider includes `currency` in its wallet API responses purely as an informational echo so the casino UI can render the correct symbol.
- If the provider detects a mismatch between its configured currency and any expectation it can verify, it MUST refuse the operation and log the incident.

### 6.4 Precision

- Amounts are decimal with two-digit precision. Provider MUST round half-up to 2 decimals.
- Always echo balances rounded to 2 decimals.

---

## 7. Error Handling

All errors are returned with HTTP `200 OK` and a JSON body. The `error` field carries the canonical code.

| Code | Category | Meaning |
|---|---|---|
| `0` | Success | Operation completed; balances reflect new state. |
| `100` | Auth | Missing token. |
| `101` | Auth | Invalid or unknown `cid` / company. |
| `201` | Bet (real) | Invalid bet amount (`<= 0`). |
| `202` | Bet (real) | Insufficient real balance. |
| `211` | Bet (free) | Invalid freeplay bet amount (`<= 0`). |
| `212` | Bet (free) | Insufficient freeplay balance. |
| `301` | Prize | Invalid win amount (`< 0`). |
| `999` | Routing | Unknown or missing `action`. |

**Error envelope:**
```json
{ "error": 202, "msg": "Insufficient balance" }
```

Providers MAY add additional vendor-specific codes in the `5xx` range, but MUST NOT reuse any code in the table above for a different meaning.

---

## 8. Security Requirements

### 8.1 Transport

- **HTTPS / TLS 1.2+ is MANDATORY** for the launch URL and the wallet API in production.
- Casino session cookies are set with `Secure`, `HttpOnly`, `SameSite=None` whenever HTTPS is detected.

### 8.2 Shared Secret

- A per-provider HS256 secret is exchanged out-of-band and stored under `jwt.secret` in `config.php`.
- Secrets MUST be at least 32 bytes of entropy.
- Secrets MUST NOT be transmitted in code repositories, logs, error messages, or URLs.
- Rotation policy: secrets SHOULD be rotated at least every 12 months or immediately on suspected compromise.

### 8.3 Token Discipline

- `exp` MUST be ≤ 1 hour from `iat`. Long-lived JWTs are forbidden.
- Tokens are **single-launch** in spirit: the provider SHOULD treat each minted JWT as bound to one player session.
- The casino does NOT maintain a blacklist; expiration is the sole revocation mechanism.

### 8.4 Server-Side Validation (Provider)

On every wallet API call, the provider MUST:

1. Verify HS256 signature against the shared secret.
2. Reject if `exp < now`.
3. Resolve `player_id` from the token; never trust the request body alone.
4. Use the JWT-derived player ID — not query string — to scope ledger writes.
5. Reject mismatched `cid`.

### 8.5 Optional API Key Channel

The casino also exposes administrative endpoints (e.g. `/api/create-session/`) protected by an `X-API-Key` header. This channel is orthogonal to JWT and is used for provisioning, not for in-game wallet calls.

---

## 9. Session Behavior

### 9.1 Standalone Mode Coexistence

The platform also runs in a **standalone** mode without a third-party provider. The two modes coexist on the same codebase and are selected per company via a `path` field (`bridge`, `sbo`, `standalone`). JWT mode always takes precedence over a pre-existing PHP session.

### 9.2 JWT Session Creation

On a valid JWT launch, the casino creates a PHP session containing:

| Key | Description |
|---|---|
| `player` | Internal player DB ID |
| `company` | Internal company DB ID |
| `player_token` | Raw JWT — replayed to provider on every wallet call |
| `cshcd` | Cashier code (optional) |
| `hash` | MD5 of encrypted `User-Agent`, for fixation defense |

### 9.3 Persistence

- Cookie lifetime: `86400` seconds (1 day).
- Cookie flags: `HttpOnly`; `Secure` and `SameSite=None` when HTTPS.
- Session outlives JWT `exp`: once a session is established, wallet calls continue using the stored token until session expires or logout. Providers MUST therefore tolerate JWTs presented after `exp` only if their policy allows; otherwise return error `100` and the player will be re-launched.

### 9.4 Logout

- `/utilities/process/logout.php` calls `session_destroy()`.
- No revocation message is sent to the provider — JWTs simply fall out of use.
- Providers SHOULD treat absence of activity past `exp` as session end.

---

## 10. Third-Party Responsibilities

The provider MUST implement and operate:

1. **Real Wallet Persistence** — Authoritative balance for `balance` and `free` per player, durable across restarts.
2. **Transaction Logging** — Every `place_bet`, `place_free_bet`, and `credit_prize` recorded with: provider tx_id, casino `game_id`/`game_name`, amount, balance_before, balance_after, timestamp, JWT `jti`/hash (optional).
3. **JWT Generation** — HS256 signing with the agreed secret, correct claim set, ≤ 1 hour `exp`.
4. **Wallet API Endpoint** — HTTPS POST endpoint honoring the response contract in §4–§5.
5. **Atomicity** — Bet debits and prize credits MUST be atomic against concurrent calls for the same player. Recommended: per-player row lock or optimistic concurrency.
6. **Idempotency** — While not required by the current protocol, the provider SHOULD detect duplicate game_id+timestamp pairs to avoid double-debit on retry.
7. **Wallet Security** — Hardened storage, separation of duties, audit trail of admin balance adjustments.

The casino is NOT a system of record for real money. It does not store, reconcile, or attempt to recover provider-side balances.

---

## 11. Best Practices

### 11.1 Transaction IDs
Generate a unique provider-side `tx_id` for every wallet mutation. Surface it in audit responses (extension field permitted in the JSON envelope).

### 11.2 Logging
Log at minimum: timestamp, action, player_id, company_id, amount, balance_before, balance_after, error code, latency. Log JWT `jti` or hash, never the full token.

### 11.3 Rollback Handling
If a casino-side game round fails after a successful bet debit but before `credit_prize`, the provider should expose an internal reconciliation procedure (out of band). The protocol does not currently define a `rollback_bet` action; treat orphan debits as ledger anomalies to investigate.

### 11.4 Wallet Auditing
Run a daily sum-of-ledger vs. wallet-balance reconciliation per player. Any drift indicates a missed or duplicated transaction.

### 11.5 Rate Limiting
Apply per-player and per-IP rate limits on the wallet endpoint (suggested: 30 rps per player, 1000 rps per company). Burst windows should be ≥ 60 seconds.

### 11.6 Clock Sync
Run NTP. Skew > 30 seconds will cause spurious `exp` failures.

### 11.7 Currency reconciliation
On each provider deploy, verify that the per-`cid` currency on the provider matches the casino's `company.currency`. A daily automated check is recommended; alert on drift.

### 11.8 Observability
Emit metrics on: signature failures, expired tokens, error-code distribution, P50/P99 wallet latency. Page on signature failure spikes.

---

## Appendix A — Casino Reference Implementation

| Component | File |
|---|---|
| JWT helpers (encode/decode) | `/utilities/jwt_helper.php` |
| JWT-aware launch + session bootstrap | `/utilities/process/game_login.php` |
| Bridge connector → Provider HTTP | `/utilities/api/bridge/connect.php` |
| Internal wallet reference impl | `/provider_wallet_api.php` |
| Standalone wallet (non-provider) | `/utilities/api/standalone/connect.php` |
| Session/User-Agent guard | `/utilities/process/security.php` |
| Config (secret, issuer, expiration, DB) | `/config.php` |
| API-key admin endpoint | `/api/create-session/index.php` |

---

## Appendix B — Change Control

This document is the FROZEN contract as of the version date above. Any change to:
- claim names or types in §3,
- request/response field names in §4–§5,
- error codes in §7,

constitutes a **breaking change** and requires a new major version of this document, with a deprecation window of no less than 90 days during which both versions are supported.

### Changelog

- **v1.1** — `currency` removed from JWT payload and from wallet API request bodies. `company.currency` is now the single source of truth on the casino side; providers must derive currency from per-`cid` configuration. The `currency` field remains in wallet API responses as an informational echo. JWTs that still carry a `currency` claim are accepted (claim ignored) for backwards compatibility.
