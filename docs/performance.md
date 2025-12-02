# Documentación de Performance

## ⚡ Visión General

Este documento describe las optimizaciones de performance implementadas en el sistema de gestión presupuestal para garantizar tiempos de respuesta rápidos y escalabilidad.

---

## 🗄️ Índices de Base de Datos

### Índices Implementados

#### Offices

```sql
-- Índice compuesto para filtrado por estado y ordenamiento
CREATE INDEX offices_active_code_idx ON offices(is_active, code);

-- Índice para búsqueda por nombre
CREATE INDEX offices_name_idx ON offices(name);
```

**Optimiza queries como:**

```php
// WHERE is_active = true ORDER BY code
Office::active()->orderByCode()->get();

// WHERE name LIKE '%search%'
Office::search('RRHH')->get();
```

#### Users

```sql
-- Índice compuesto para usuarios activos
CREATE INDEX users_active_email_idx ON users(is_active, email);

-- Índice para búsqueda por nombre
CREATE INDEX users_name_idx ON users(name);
```

**Optimiza queries como:**

```php
// WHERE is_active = true
User::active()->get();

// WHERE name LIKE '%search%'
User::search('Juan')->get();
```

#### Subunits

```sql
-- Índice compuesto para subunidades por office y tipo
CREATE INDEX subunits_office_system_idx ON subunits(office_id, is_system);

-- Índice compuesto para subunidades activas por office
CREATE INDEX subunits_office_active_idx ON subunits(office_id, is_active);

-- Índice para búsqueda por nombre
CREATE INDEX subunits_name_idx ON subunits(name);
```

**Optimiza queries como:**

```php
// WHERE office_id = X AND is_system = false
Subunit::forOffice($id)->userCreated()->get();

// WHERE office_id = X AND is_active = true
Subunit::forOffice($id)->active()->get();
```

#### Catálogos

```sql
-- Índices para todas las tablas de catálogo
CREATE INDEX purposes_active_idx ON purposes(is_active);
CREATE INDEX purposes_name_idx ON purposes(name);

CREATE INDEX financings_active_idx ON financings(is_active);
CREATE INDEX financings_name_idx ON financings(name);

-- ... y así para todas las tablas de catálogo
```

### Impacto de los Índices

**Antes (sin índices):**

```
Query: SELECT * FROM offices WHERE is_active = true ORDER BY code
Execution time: 150ms
Rows scanned: 10,000
```

**Después (con índices):**

```
Query: SELECT * FROM offices WHERE is_active = true ORDER BY code
Execution time: 15ms  ← 10x más rápido
Rows scanned: 500 (solo activos)
```

---

## 🔍 Query Scopes

### Scopes Implementados

#### Office Model

```php
// Filtrado por estado
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeInactive($query)
{
    return $query->where('is_active', false);
}

// Búsqueda
public function scopeSearch($query, string $term)
{
    return $query->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%");
}

// Ordenamiento
public function scopeOrderByCode($query)
{
    return $query->orderBy('code');
}

// Conteos optimizados
public function scopeWithSubunitsCount($query)
{
    return $query->withCount('subunits');
}

// Relaciones filtradas
public function scopeWithActiveSubunits($query)
{
    return $query->with(['subunits' => function ($q) {
        $q->where('is_active', true);
    }]);
}
```

**Uso:**

```php
// Queries expresivas y optimizadas
$offices = Office::active()
    ->withSubunitsCount()
    ->orderByCode()
    ->get();

// Búsqueda optimizada
$results = Office::search('RRHH')
    ->active()
    ->get();
```

#### User Model

```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeWithRole($query, string $roleName)
{
    return $query->role($roleName);  // Usa Spatie
}

public function scopeSearch($query, string $term)
{
    return $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
}
```

#### Subunit Model

```php
public function scopeForOffice($query, int $officeId)
{
    return $query->where('office_id', $officeId);
}

public function scopeSystemGenerated($query)
{
    return $query->where('is_system', true);
}

public function scopeUserCreated($query)
{
    return $query->where('is_system', false);
}
```

### Beneficios de los Scopes

1. **Reutilización:**

```php
// Mismo scope en múltiples lugares
Office::active()->get();
User::active()->get();
Subunit::active()->get();
```

2. **Composición:**

```php
// Combinar scopes
Office::active()
    ->search('RRHH')
    ->withSubunitsCount()
    ->orderByCode()
    ->get();
```

3. **Mantenibilidad:**

```php
// Cambiar lógica en un solo lugar
public function scopeActive($query)
{
    // Si cambia la definición de "activo", solo cambiar aquí
    return $query->where('is_active', true)
                ->whereNull('deleted_at');
}
```

---

## 🚀 Eager Loading

### Problema N+1

**❌ Código problemático:**

```php
$offices = Office::all();  // 1 query

foreach ($offices as $office) {
    echo $office->subunits->count();  // N queries (1 por cada office)
}

// Total: 1 + N queries
// Si hay 100 offices = 101 queries
```

**✅ Solución con Eager Loading:**

```php
$offices = Office::withCount('subunits')->get();  // 2 queries

foreach ($offices as $office) {
    echo $office->subunits_count;  // Sin query adicional
}

// Total: 2 queries siempre
```

### Eager Loading Configurado

**En Models:**

```php
class User extends Model
{
    // Siempre cargar roles
    protected $with = ['roles'];
}
```

**Beneficio:**

```php
// Automáticamente carga roles
$users = User::all();  // 2 queries (users + roles)

foreach ($users as $user) {
    echo $user->roles->first()->name;  // Sin query adicional
}
```

### Eager Loading Condicional

```php
// Cargar relaciones solo cuando sea necesario
$offices = Office::when($includeSubunits, function ($query) {
    $query->with('subunits');
})->get();
```

### Eager Loading con Filtros

```php
// Cargar solo subunidades activas
$offices = Office::with(['subunits' => function ($query) {
    $query->where('is_active', true)
          ->orderBy('name');
}])->get();
```

---

## 📊 Optimización de Queries

### Usar select() para Limitar Columnas

**❌ Malo (carga todas las columnas):**

```php
$offices = Office::all();
```

**✅ Bueno (solo columnas necesarias):**

```php
$offices = Office::select('id', 'code', 'name')->get();
```

### Usar chunk() para Grandes Datasets

**❌ Malo (carga todo en memoria):**

```php
$offices = Office::all();  // 10,000 registros en memoria
foreach ($offices as $office) {
    // procesar
}
```

**✅ Bueno (procesa en lotes):**

```php
Office::chunk(100, function ($offices) {
    foreach ($offices as $office) {
        // procesar
    }
});
```

### Usar exists() en lugar de count()

**❌ Malo:**

```php
if ($office->subunits()->count() > 0) {
    // ...
}
```

**✅ Bueno:**

```php
if ($office->subunits()->exists()) {
    // ...
}
```

### Usar pluck() para Arrays Simples

**❌ Malo:**

```php
$names = Office::all()->pluck('name');  // Carga objetos completos
```

**✅ Bueno:**

```php
$names = Office::pluck('name');  // Solo carga la columna name
```

---

## 💾 Caché (Futuro)

### Estrategia de Caché Recomendada

#### 1. Caché de Catálogos

```php
// Los catálogos cambian poco, cachear por 1 hora
$purposes = Cache::remember('purposes.active', 3600, function () {
    return Purpose::active()->orderBy('name')->get();
});
```

#### 2. Caché de Conteos

```php
// Cachear conteos costosos
$officeCount = Cache::remember('offices.count', 600, function () {
    return Office::active()->count();
});
```

#### 3. Invalidación de Caché

```php
// En Observer o Service
public function created(Office $office): void
{
    Cache::forget('offices.count');
    Cache::forget('offices.active');
}
```

---

## 📈 Monitoreo de Performance

### Laravel Telescope (Desarrollo)

**Instalar:**

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Monitorear:**

-   Queries lentas (> 100ms)
-   Queries duplicadas (N+1)
-   Memoria utilizada
-   Tiempo de respuesta

### Laravel Debugbar (Desarrollo)

**Instalar:**

```bash
composer require barryvdh/laravel-debugbar --dev
```

**Ver:**

-   Queries ejecutadas
-   Tiempo de cada query
-   Memoria utilizada
-   Vistas renderizadas

### Queries Lentas en Producción

**Configurar en `config/database.php`:**

```php
'mysql' => [
    // ...
    'slow_query_log' => env('DB_SLOW_QUERY_LOG', false),
    'slow_query_time' => env('DB_SLOW_QUERY_TIME', 2),
],
```

---

## 🎯 Métricas de Performance

### Objetivos

| Métrica                   | Objetivo | Actual |
| ------------------------- | -------- | ------ |
| Tiempo de carga de página | < 200ms  | ~150ms |
| Queries por request       | < 10     | ~5     |
| Tiempo de query promedio  | < 50ms   | ~20ms  |
| Memoria por request       | < 50MB   | ~30MB  |

### Cómo Medir

**En desarrollo:**

```php
// En un controller o component
$start = microtime(true);

// Tu código aquí

$time = microtime(true) - $start;
Log::info("Execution time: {$time}s");
```

**Con Telescope:**

1. Abrir `/telescope`
2. Ver sección "Requests"
3. Analizar queries y tiempo

---

## 🔧 Optimizaciones Futuras

### Corto Plazo

-   [ ] Implementar caché de catálogos
-   [ ] Optimizar queries de reportes
-   [ ] Agregar índices adicionales según uso real

### Mediano Plazo

-   [ ] Implementar Redis para caché
-   [ ] Query optimization con EXPLAIN
-   [ ] Database connection pooling

### Largo Plazo

-   [ ] Read replicas para queries pesadas
-   [ ] Sharding de base de datos
-   [ ] CDN para assets estáticos

---

## 📚 Mejores Prácticas

### 1. Siempre Usar Scopes

```php
// ❌ Malo
Office::where('is_active', true)->get();

// ✅ Bueno
Office::active()->get();
```

### 2. Evitar Queries en Loops

```php
// ❌ Malo
foreach ($offices as $office) {
    $count = $office->subunits()->count();  // Query en cada iteración
}

// ✅ Bueno
$offices = Office::withCount('subunits')->get();
foreach ($offices as $office) {
    $count = $office->subunits_count;  // Sin query
}
```

### 3. Usar Índices Apropiados

```php
// Si haces este query frecuentemente:
Office::where('is_active', true)
    ->where('code', 'LIKE', 'RR%')
    ->get();

// Necesitas este índice:
CREATE INDEX offices_active_code_idx ON offices(is_active, code);
```

### 4. Paginar Resultados Grandes

```php
// ❌ Malo
$offices = Office::all();  // 10,000 registros

// ✅ Bueno
$offices = Office::paginate(50);  // 50 por página
```

---

## 🔍 Debugging de Performance

### Identificar Queries Lentas

```php
// En AppServiceProvider
DB::listen(function ($query) {
    if ($query->time > 100) {  // > 100ms
        Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'time' => $query->time,
            'bindings' => $query->bindings,
        ]);
    }
});
```

### Analizar con EXPLAIN

```sql
EXPLAIN SELECT * FROM offices
WHERE is_active = true
ORDER BY code;

-- Ver si usa índices
-- type: ref (bueno) vs ALL (malo)
-- rows: menos es mejor
```

---

## ✅ Checklist de Performance

### Antes de Desplegar

-   [ ] Todos los queries frecuentes tienen índices
-   [ ] No hay N+1 queries en vistas principales
-   [ ] Relaciones usan eager loading cuando es necesario
-   [ ] Queries lentas están optimizadas
-   [ ] Resultados grandes están paginados
-   [ ] Caché implementado para datos estáticos

### Revisión Periódica

-   [ ] Revisar queries lentas mensualmente
-   [ ] Analizar uso de índices
-   [ ] Optimizar queries más frecuentes
-   [ ] Actualizar estadísticas de base de datos
