# Сервис коротких ссылок + QR (Yii2 + Vue 3)

Приложение сокращает URL, генерирует QR-код и ведет статистику переходов.

- Backend: `Yii2`, `MariaDB`
- Frontend: `Vue 3`, `Vue Router`, `Vite`, `Bootstrap 5`
- Инфраструктура: `Docker Compose`, `Makefile`

## Что реализовано

- SPA-интерфейс на Vue 3 для страниц `/`, `/login`, `/contact`, `/about`, `404`.
- JSON API в Yii2 для shortener/auth/contact.
- Редирект по короткой ссылке `/<code>` с учетом посещений.
- Валидация URL (`http/https`), проверка доступности через `cURL`.
- Генерация QR-кода локально (без внешних API).
- Contact форма с CAPTCHA и отправкой письма.

## Архитектура маршрутов

- SPA shell: `GET /`, `GET /login`, `GET /contact`, `GET /about` и другие клиентские маршруты.
- API:
  - `POST /api/link/create`
  - `POST /api/auth/login`
  - `POST /api/auth/logout`
  - `GET /api/auth/me`
  - `POST /api/contact/submit`
- Редирект короткой ссылки: `GET /<code>`

## Структура БД

### `short_link`

- `id` (PK)
- `original_url` (varchar(2048))
- `short_code` (varchar(16), unique)
- `visits_count` (int, default 0)
- `created_at` (int)
- `updated_at` (int)

### `click_log`

- `id` (PK)
- `short_link_id` (FK -> `short_link.id`)
- `ip_address` (varchar(45))
- `created_at` (int)

## Быстрый запуск

### Требования

- Docker
- Docker Compose plugin (`docker compose`)
- Make

### 1) Собрать и поднять контейнеры

```bash
make build
make up
```

Сервисы:

- `nginx` (порт `8080`, настраивается через `APP_PORT`)
- `php-fpm`
- `mariadb` (порт `3307`, настраивается через `DB_PORT_FORWARD`)
- `node` (служебный сервис для npm-команд)

### 2) Установить backend и frontend зависимости

```bash
make install
make npm-install
```

### 3) Применить миграции

```bash
make migrate
```

### 4) Собрать фронтенд

```bash
make npm-build
```

### 5) Открыть приложение

```text
http://localhost:8080
```

## Режимы фронтенда

### Production-like (рекомендуется по умолчанию)

- Сборка в `web/dist`
- Отдается через `nginx` + `php`

```bash
make npm-build
```

### Dev (Vite HMR)

Если в `web/dist` нет production manifest, Yii2 автоматически подключает Vite dev server.

```bash
make npm-dev
```

По умолчанию используется `http://localhost:5173`.
При необходимости можно переопределить:

```bash
VITE_DEV_SERVER_URL=http://localhost:5173 make npm-dev
```

## Проверка ключевого сценария

1. Открыть `/`.
2. Вставить URL и нажать `OK`.
3. Проверить:
   - невалидный URL -> сообщение валидации;
   - недоступный URL -> `Данный URL не доступен`;
   - валидный URL -> короткая ссылка + QR.
4. Перейти по короткой ссылке:
   - происходит редирект;
   - в БД пишется `click_log`;
   - увеличивается `short_link.visits_count`.

## Тесты

Обновлены тесты под новый SPA/API сценарий:

- Functional: API-auth, API-contact, API-link валидация.
- Acceptance: маршруты SPA и формы Vue.

Запуск (пример):

```bash
vendor/bin/codecept run functional
```

## Полезные команды Makefile

```bash
make up
make down
make logs
make bash
make migrate
make npm-install
make npm-dev
make npm-build
```

## Переменные окружения

- `APP_PORT` (default `8080`)
- `DB_PORT_FORWARD` (default `3307`)
- `MYSQL_DATABASE` (default `shortener`)
- `MYSQL_USER` (default `shortener`)
- `MYSQL_PASSWORD` (default `shortener`)
- `MYSQL_ROOT_PASSWORD` (default `root`)
- `VITE_DEV_SERVER_URL` (default `http://localhost:5173`)

Пример:

```bash
APP_PORT=8090 MYSQL_PASSWORD=secret make up
```
