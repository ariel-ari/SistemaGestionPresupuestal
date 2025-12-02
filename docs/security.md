# Documentación de Seguridad

## 🔐 Visión General

Este documento describe las medidas de seguridad implementadas en el sistema de gestión presupuestal, siguiendo el principio de **Defense in Depth** (seguridad en capas).

---

## 🛡️ Políticas de Autorización (Policies)

### Estructura de Policies

Todas las Policies siguen el mismo patrón:

```php
class ModelPolicy
{
    // Métodos CRUD estándar
    public function viewAny(User $user): bool
    public function view(User $user, Model $model): bool
    public function create(User $user): bool
    public function update(User $user, Model $model): bool
    public function delete(User $user, Model $model): bool

    // Métodos personalizados según necesidad
    public function restore(User $user, Model $model): bool
    public function forceDelete(User $user, Model $model): bool
}
```

### OfficePolicy

**Protecciones implementadas:**

1. **Permisos básicos con Spatie:**

```php
public function create(User $user): bool
{
    return $user->can('create offices');
}
```

2. **Validación de relaciones antes de eliminar:**

```php
public function delete(User $user, Office $office): bool
{
    if (!$user->can('delete offices')) {
        return false;
    }

    // No permitir eliminar si tiene presupuestos asignados
    $hasActiveBudgets = $office->officeBudgetAssignments()->exists();
    $hasSimulatedExpenses = $office->simulatedExpenses()->exists();

    return !$hasActiveBudgets && !$hasSimulatedExpenses;
}
```

### UserPolicy

**Protecciones críticas:**

1. **Prevenir auto-eliminación:**

```php
public function delete(User $user, User $model): bool
{
    // No puedes eliminarte a ti mismo
    if ($user->id === $model->id) {
        return false;
    }

    return $user->can('delete users');
}
```

2. **Proteger último Super Admin:**

```php
public function delete(User $user, User $model): bool
{
    if ($model->hasRole('Super Admin')) {
        $activeSuperAdmins = User::role('Super Admin')
            ->where('is_active', true)
            ->count();

        // No permitir eliminar si es el último
        if ($activeSuperAdmins <= 1) {
            return false;
        }
    }

    return true;
}
```

3. **Validar jerarquía de roles:**

```php
public function update(User $user, User $model): bool
{
    // No puedes asignar un rol superior al tuyo
    $userHighestRole = $user->roles->sortByDesc('id')->first();
    $targetRole = request()->input('role');

    if ($targetRole > $userHighestRole->id) {
        return false;
    }

    return $user->can('edit users');
}
```

### SubunitPolicy

**Protección de subunidades del sistema:**

```php
public function update(User $user, Subunit $subunit): bool
{
    // Las subunidades del sistema no se pueden editar manualmente
    if ($subunit->is_system) {
        return false;
    }

    return $user->can('edit subunits');
}

public function delete(User $user, Subunit $subunit): bool
{
    // Las subunidades del sistema no se pueden eliminar
    if ($subunit->is_system) {
        return false;
    }

    return $user->can('delete subunits');
}
```

### Gates Globales

**AuthServiceProvider:**

```php
// Super Admin puede hacer todo
Gate::before(function (User $user, string $ability) {
    if ($user->hasRole('Super Admin')) {
        return true;
    }
});

// Solo usuarios activos pueden hacer acciones
Gate::define('active-user', function (User $user) {
    return $user->is_active;
});
```

---

## ✅ Validación de Datos

### Niveles de Validación

#### 1. Validación de Formato

**Reglas básicas:**

```php
'email' => [
    'required',
    'string',
    'email:rfc,dns',  // ← Valida formato RFC y DNS
    'max:100',
]
```

**Validación de contraseñas:**

```php
'password' => [
    'required',
    'string',
    Password::min(8)
        ->letters()          // Requiere letras
        ->mixedCase()        // Mayúsculas y minúsculas
        ->numbers()          // Requiere números
        ->uncompromised(),   // Verifica en DB de contraseñas filtradas
]
```

**Validación con regex:**

```php
'code' => [
    'required',
    'regex:/^[A-Z0-9-]+$/',  // Solo mayúsculas, números y guiones
]
```

#### 2. Validación de Unicidad

**Ignorar soft deletes:**

```php
'name' => [
    'required',
    Rule::unique('offices', 'name')
        ->ignore($id)
        ->whereNull('deleted_at'),  // ← Importante
]
```

**Unicidad compuesta:**

```php
'name' => [
    'required',
    Rule::unique('subunits', 'name')
        ->ignore($id)
        ->where('office_id', $this->office_id),  // ← Único por office
]
```

#### 3. Validación de Relaciones

```php
'office_id' => [
    'required',
    'integer',
    'exists:offices,id',  // ← Verifica que existe
]
```

---

## 🧹 Sanitización XSS

### Sanitización Automática en BaseForm

**Método sanitize():**

```php
protected function sanitize(array $data): array
{
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            // 1. Trim de espacios
            $value = trim($value);

            // 2. Eliminar HTML peligroso
            $data[$key] = strip_tags($value);
        }
    }

    return $data;
}
```

**Flujo de sanitización:**

```
Input → Normalización → Validación → Sanitización → Database
```

### Ejemplos de Sanitización

**Antes:**

```php
$name = "<script>alert('XSS')</script>Producto";
```

**Después:**

```php
$name = "Producto";  // HTML eliminado
```

### Casos Especiales

**Si necesitas permitir HTML:**

```php
protected function sanitize(array $data): array
{
    foreach ($data as $key => $value) {
        if ($key === 'description' && is_string($value)) {
            // Permitir solo tags seguros
            $data[$key] = strip_tags($value, '<p><br><strong><em>');
        }
    }

    return $data;
}
```

---

## 🔑 Protección de Contraseñas

### Reglas de Contraseñas Fuertes

**Requisitos mínimos:**

-   ✅ 8 caracteres mínimo
-   ✅ Al menos una letra
-   ✅ Mayúsculas y minúsculas
-   ✅ Al menos un número
-   ✅ No estar en lista de contraseñas comprometidas

**Implementación:**

```php
Password::min(8)
    ->letters()
    ->mixedCase()
    ->numbers()
    ->uncompromised()
```

### Hashing de Contraseñas

**Siempre hashear antes de guardar:**

```php
if (!empty($data['password'])) {
    $data['password'] = Hash::make($data['password']);
}
```

**Nunca exponer contraseñas:**

```php
// En el modelo User
protected $hidden = [
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
];
```

**Limpiar del formulario:**

```php
public function setModel($model): void
{
    parent::setModel($model);
    $this->password = '';  // ← Limpiar contraseña
}
```

### Verificación de Contraseñas

```php
if (Hash::check($plainPassword, $user->password)) {
    // Contraseña correcta
}
```

---

## 📝 Logging de Seguridad

### Eventos Importantes a Loggear

#### 1. Creación de Registros

```php
Log::info('Registro creado exitosamente', [
    'model' => $this->modelClass(),
    'id' => $model->id,
    'user_id' => Auth::id(),  // ← Quién lo creó
]);
```

#### 2. Modificaciones

```php
Log::info('Registro actualizado exitosamente', [
    'model' => $this->modelClass(),
    'id' => $this->model->id,
    'user_id' => Auth::id(),
    'changes' => $this->model->getChanges(),  // ← Qué cambió
]);
```

#### 3. Eliminaciones

```php
Log::warning('Usuario eliminado', [
    'user_id' => $user->id,
    'email' => $user->email,
    'deleted_by' => $currentUser->id,  // ← Quién lo eliminó
]);
```

#### 4. Errores de Seguridad

```php
Log::error('Intento de eliminar último Super Admin', [
    'user_id' => $user->id,
    'attempted_by' => Auth::id(),
]);
```

### Niveles de Log

-   **Info:** Operaciones normales
-   **Warning:** Operaciones sensibles (eliminar, desactivar)
-   **Error:** Fallos de seguridad o validación

---

## 🚨 Manejo de Errores de Seguridad

### Excepciones Específicas

```php
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

try {
    $this->authorize('delete', $model);
} catch (AuthorizationException $e) {
    // Usuario no autorizado
    return redirect()->back()->with('error', 'No tienes permiso para esta acción.');
}
```

### Mensajes de Error Seguros

**❌ Malo (expone información):**

```php
throw new Exception("Usuario admin@example.com no encontrado");
```

**✅ Bueno (genérico):**

```php
throw new Exception("Usuario no encontrado");
```

---

## 🔒 Checklist de Seguridad

### Antes de Desplegar

-   [ ] Todas las rutas tienen middleware de autenticación
-   [ ] Todas las acciones verifican autorización con Policies
-   [ ] Todas las contraseñas se hashean
-   [ ] Todos los inputs se validan
-   [ ] Todos los inputs se sanitizan
-   [ ] Datos sensibles están en `$hidden`
-   [ ] Logging de operaciones críticas
-   [ ] Variables de entorno protegidas (`.env` en `.gitignore`)
-   [ ] HTTPS habilitado en producción
-   [ ] CSRF protection habilitado

### Revisión Periódica

-   [ ] Revisar logs de seguridad semanalmente
-   [ ] Actualizar dependencias mensualmente
-   [ ] Auditar permisos de usuarios
-   [ ] Revisar intentos de acceso no autorizado

---

## 🛠️ Herramientas de Seguridad

### Laravel Telescope (Desarrollo)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

**Monitorear:**

-   Queries lentas
-   Excepciones
-   Requests
-   Logs

### Laravel Sanctum (API)

Para autenticación de API:

```bash
composer require laravel/sanctum
```

### Spatie Permission

Ya instalado para gestión de roles:

```bash
composer require spatie/laravel-permission
```

---

## 📚 Mejores Prácticas

### 1. Principio de Menor Privilegio

**Dar solo los permisos necesarios:**

```php
// ❌ Malo
$user->givePermissionTo('*');

// ✅ Bueno
$user->givePermissionTo(['view offices', 'create offices']);
```

### 2. Validar en el Servidor

**Nunca confiar en validación del cliente:**

```php
// Siempre validar en el servidor
public function rules(): array
{
    return [
        'email' => 'required|email',
        // ...
    ];
}
```

### 3. Fail Secure

**En caso de duda, denegar:**

```php
public function delete(User $user, Model $model): bool
{
    // Si no estamos seguros, denegar
    if ($this->hasUncertainCondition()) {
        return false;  // ← Fail secure
    }

    return $user->can('delete');
}
```

### 4. Defense in Depth

**Múltiples capas de seguridad:**

```
1. Validación de formato
2. Sanitización
3. Autorización
4. Validación de negocio
5. Logging
```

---

## 🔮 Futuras Mejoras de Seguridad

### Corto Plazo

-   [ ] Implementar rate limiting
-   [ ] Agregar 2FA obligatorio para admins
-   [ ] Implementar password expiration

### Mediano Plazo

-   [ ] Auditoría completa de acciones
-   [ ] Detección de anomalías
-   [ ] Backup automático encriptado

### Largo Plazo

-   [ ] Penetration testing
-   [ ] Security headers (CSP, HSTS)
-   [ ] WAF (Web Application Firewall)
