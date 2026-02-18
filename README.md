# Сервис коротких ссылок + QR (Yii2 Basic)

Проект реализует сокращение URL с генерацией QR-кода и редиректом по короткой ссылке.
Стек: `Yii2 Basic`, `MariaDB`, `jQuery`, `Bootstrap`, `Docker Compose`, `Makefile`.

## Возможности

- Ввод URL на главной странице и отправка через Ajax (без перезагрузки).
- Валидация URL (только `http/https`).
- Проверка доступности ресурса через `cURL` (`HEAD`, fallback `GET`).
- Генерация короткой ссылки и QR-кода локально (без внешних API).
- CAPTCHA и обработка изображений через `ImageMagick` (`imagick` extension).
- Редирект по короткому коду на оригинальный URL.
- Логирование переходов: IP пользователя и счетчик переходов.

## Структура БД

### Таблица `short_link`

- `id` (PK)
- `original_url` (varchar(2048))
- `short_code` (varchar(16), unique)
- `visits_count` (int, default 0)
- `created_at` (int)
- `updated_at` (int)

### Таблица `click_log`

- `id` (PK)
- `short_link_id` (FK -> `short_link.id`)
- `ip_address` (varchar(45))
- `created_at` (int)

## Быстрый запуск

### Требования

- Docker
- Docker Compose (plugin `docker compose`)
- Make

### 1) Собрать и поднять контейнеры

```bash
make build
make up
```

Сервисы:

- `nginx` (порт `8080`, можно изменить переменной `APP_PORT`)
- `php-fpm`
- `mariadb` (порт `3307`, можно изменить переменной `DB_PORT_FORWARD`)

### 2) Установить зависимости в контейнере

```bash
make install
```

### 3) Применить миграции

```bash
make migrate
```

### 4) Открыть приложение

```text
http://localhost:8080
```

## Проверка сценария из ТЗ

1. Открыть главную страницу.
2. Вставить URL и нажать `OK`.
3. Проверить варианты:
   - невалидный URL -> ошибка валидации;
   - недоступный URL -> сообщение `Данный URL не доступен`;
   - валидный и доступный URL -> отображаются короткая ссылка и QR-код.
4. Перейти по короткой ссылке:
   - происходит редирект на оригинальный сайт;
   - в БД создается запись в `click_log`;
   - увеличивается `short_link.visits_count`.

## Полезные команды Makefile

```bash
make up        # поднять контейнеры
make down      # остановить контейнеры
make logs      # смотреть логи
make bash      # shell в php-контейнере
make composer ARGS="show"
make migrate
```

## Переменные окружения (docker-compose)

Можно переопределять при запуске через shell:

- `APP_PORT` (по умолчанию `8080`)
- `DB_PORT_FORWARD` (по умолчанию `3307`)
- `MYSQL_DATABASE` (по умолчанию `shortener`)
- `MYSQL_USER` (по умолчанию `shortener`)
- `MYSQL_PASSWORD` (по умолчанию `shortener`)
- `MYSQL_ROOT_PASSWORD` (по умолчанию `root`)

Пример:

```bash
APP_PORT=8090 MYSQL_PASSWORD=secret make up
```
