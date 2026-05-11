# API Документация - Система путевых листов

API построен на **API Platform** и предоставляет полный CRUD для управления путевыми листами, автомобилями, водителями, поездками и заправками.

## Базовый URL
```
http://localhost/api
```

## Доступные сущности

### 1. Car (Автомобиль) - `/api/cars`

**Поля:**
- `id` (int, readonly) - ID автомобиля
- `brand` (string) - Марка автомобиля
- `model` (string) - Модель автомобиля
- `licensePlate` (string) - Номерной знак
- `createdAt` (datetime, readonly) - Дата создания
- `closedAt` (datetime, nullable) - Дата закрытия
- `updatedAt` (datetime, readonly) - Дата обновления
- `tripLists` (array, readonly) - Связанные путевые листы
- `drivers` (array, readonly) - Связанные водители

**Endpoints:**
- `GET /api/cars` - Получить список всех автомобилей
- `GET /api/cars/{id}` - Получить автомобиль по ID
- `POST /api/cars` - Создать новый автомобиль
- `PUT /api/cars/{id}` - Обновить автомобиль
- `DELETE /api/cars/{id}` - Удалить автомобиль

**Пример создания:**
```json
{
  "brand": "Toyota",
  "model": "Camry",
  "licensePlate": "А123ВС777"
}
```

---

### 2. Driver (Водитель) - `/api/drivers`

**Поля:**
- `id` (int, readonly) - ID водителя
- `name` (string) - Имя водителя
- `snils` (string, nullable) - СНИЛС
- `licenseDriver` (string) - Номер водительского удостоверения
- `licenseDriverDate` (date, nullable) - Дата выдачи водительского удостоверения
- `car` (object) - Связанный автомобиль (IRI)
- `tripLists` (array, readonly) - Связанные путевые листы

**Endpoints:**
- `GET /api/drivers` - Получить список всех водителей
- `GET /api/drivers/{id}` - Получить водителя по ID
- `POST /api/drivers` - Создать нового водителя
- `PUT /api/drivers/{id}` - Обновить водителя
- `DELETE /api/drivers/{id}` - Удалить водителя

**Пример создания:**
```json
{
  "name": "Иванов Иван Иванович",
  "snils": "12345678901",
  "licenseDriver": "1234567890",
  "licenseDriverDate": "2020-01-15",
  "car": "/api/cars/1"
}
```

---

### 3. Trip (Поездка) - `/api/trips`

**Поля:**
- `id` (int, readonly) - ID поездки
- `orderNumber` (string) - Номер заказа
- `address` (string) - Адрес назначения
- `distance` (float, nullable) - Расстояние в км
- `createdAt` (datetime, readonly) - Дата создания
- `closedAt` (datetime, nullable) - Дата закрытия
- `updatedAt` (datetime, readonly) - Дата обновления
- `tripLists` (array, readonly) - Связанные путевые листы

**Endpoints:**
- `GET /api/trips` - Получить список всех поездок
- `GET /api/trips/{id}` - Получить поездку по ID
- `POST /api/trips` - Создать новую поездку
- `PUT /api/trips/{id}` - Обновить поездку
- `DELETE /api/trips/{id}` - Удалить поездку

**Пример создания:**
```json
{
  "orderNumber": "ORD-2025-001",
  "address": "г. Москва, ул. Ленина, д. 10",
  "distance": 45.5
}
```

---

### 4. TripList (Путевой лист) - `/api/trip_lists`

**Поля:**
- `id` (int, readonly) - ID путевого листа
- `userId` (int, nullable) - ID пользователя
- `startPoint` (string, nullable) - Точка начала маршрута
- `typeMessage` (string, nullable) - Тип сообщения
- `fuelStart` (float, nullable) - Топливо на старте (л)
- `fuelEnd` (float, nullable) - Топливо на финише (л)
- `fuelUsed` (float, nullable) - Использовано топлива (л)
- `kmStart` (float, nullable) - Километраж на старте
- `kmEnd` (float, nullable) - Километраж на финише
- `fuelStartFact` (float, nullable) - Фактическое топливо на старте (л)
- `fuelEndFact` (float, nullable) - Фактическое топливо на финише (л)
- `createdAt` (datetime, readonly) - Дата создания
- `closedAt` (datetime, nullable) - Дата закрытия
- `updatedAt` (datetime, readonly) - Дата обновления
- `car` (object) - Связанный автомобиль (IRI)
- `trip` (object) - Связанная поездка (IRI)
- `driver` (object) - Связанный водитель (IRI)
- `fuelCards` (array, readonly) - Связанные заправки

**Endpoints:**
- `GET /api/trip_lists` - Получить список всех путевых листов
- `GET /api/trip_lists/{id}` - Получить путевой лист по ID
- `POST /api/trip_lists` - Создать новый путевой лист
- `PUT /api/trip_lists/{id}` - Обновить путевой лист
- `DELETE /api/trip_lists/{id}` - Удалить путевой лист

**Пример создания:**
```json
{
  "userId": 1,
  "startPoint": "Гараж №5",
  "typeMessage": "Доставка",
  "fuelStart": 50.0,
  "kmStart": 12500.0,
  "car": "/api/cars/1",
  "trip": "/api/trips/1",
  "driver": "/api/drivers/1"
}
```

---

### 5. FuelCard (Заправка) - `/api/fuel_cards`

**Поля:**
- `id` (int, readonly) - ID заправки
- `liters` (float, nullable) - Количество литров
- `createdAt` (datetime, readonly) - Дата создания
- `closedAt` (datetime, nullable) - Дата закрытия
- `updatedAt` (datetime, readonly) - Дата обновления
- `tripList` (object) - Связанный путевой лист (IRI)

**Endpoints:**
- `GET /api/fuel_cards` - Получить список всех заправок
- `GET /api/fuel_cards/{id}` - Получить заправку по ID
- `POST /api/fuel_cards` - Создать новую заправку
- `PUT /api/fuel_cards/{id}` - Обновить заправку
- `DELETE /api/fuel_cards/{id}` - Удалить заправку

**Пример создания:**
```json
{
  "liters": 25.5,
  "tripList": "/api/trip_lists/1"
}
```

---

## Форматы данных

API Platform поддерживает несколько форматов:
- **JSON-LD** (по умолчанию): `/api/cars.jsonld`
- **JSON**: `/api/cars.json`
- **HTML**: `/api/cars.html`

## Документация API

Встроенная документация доступна по адресу:
```
http://localhost/api/docs
```

Здесь вы найдете интерактивную документацию Swagger/OpenAPI с возможностью тестирования API прямо в браузере.

## Фильтрация и поиск

API Platform автоматически поддерживает фильтрацию. Примеры:
- `/api/cars?brand=Toyota` - фильтр по марке
- `/api/trip_lists?driver=/api/drivers/1` - фильтр по водителю

## Пагинация

По умолчанию результаты разбиты на страницы (30 записей на страницу):
- `/api/cars?page=1`
- `/api/cars?page=2`

## Структура связей

```
TripList (центральная сущность)
├── Car (Many-to-One)
├── Driver (Many-to-One)
├── Trip (Many-to-One)
└── FuelCards (One-to-Many)

Car
├── TripLists (One-to-Many)
└── Drivers (One-to-Many)

Driver
├── Car (Many-to-One)
└── TripLists (One-to-Many)

Trip
└── TripLists (One-to-Many)

FuelCard
└── TripList (Many-to-One)
```

## Примеры использования

### Создание полного путевого листа

1. Создать автомобиль:
```bash
curl -X POST http://localhost/api/cars \
  -H "Content-Type: application/json" \
  -d '{"brand":"Toyota","model":"Camry","licensePlate":"А123ВС777"}'
```

2. Создать водителя:
```bash
curl -X POST http://localhost/api/drivers \
  -H "Content-Type: application/json" \
  -d '{"name":"Иванов И.И.","licenseDriver":"1234567890","car":"/api/cars/1"}'
```

3. Создать поездку:
```bash
curl -X POST http://localhost/api/trips \
  -H "Content-Type: application/json" \
  -d '{"orderNumber":"ORD-001","address":"Москва","distance":45.5}'
```

4. Создать путевой лист:
```bash
curl -X POST http://localhost/api/trip_lists \
  -H "Content-Type: application/json" \
  -d '{
    "userId":1,
    "startPoint":"Гараж",
    "fuelStart":50,
    "kmStart":12500,
    "car":"/api/cars/1",
    "trip":"/api/trips/1",
    "driver":"/api/drivers/1"
  }'
```

5. Добавить заправку:
```bash
curl -X POST http://localhost/api/fuel_cards \
  -H "Content-Type: application/json" \
  -d '{"liters":25.5,"tripList":"/api/trip_lists/1"}'
```

## Автоматические поля

Следующие поля устанавливаются автоматически:
- `createdAt` - при создании записи
- `updatedAt` - при каждом обновлении записи
- `id` - генерируется автоматически

---

**Готово!** Ваш API для управления путевыми листами полностью функционален.
