# Credenciales de Acceso — Jacket Store POS

## Usuario Administrador

**Email:** `admin@gmail.com`
**Contraseña:** `12345678`
**Nombre:** Admin
**Rol:** Administrador (acceso completo)

---

## Usuario Ventas (por defecto en el login)

**Email:** `ventas@gmail.com`
**Contraseña:** `12345678`

---

## URL de Acceso

**Local:** http://127.0.0.1:8000

---

## Importante

### Cambiar Contraseña
Por seguridad, cambia la contraseña después del primer inicio de sesión:
1. Inicia sesión → Dropdown usuario → Configuraciones
2. Cambia la contraseña desde el perfil

### Roles Disponibles
- **Administrador:** Acceso total al sistema
- Roles personalizados desde `/admin/roles`

---

## Datos Iniciales Cargados

✅ **Documentos** — Tipos de documentos (CC, NIT, Pasaporte)
✅ **Comprobantes** — Boleta y Factura
✅ **Permisos** — 45 permisos granulares
✅ **Usuario Admin** — Usuario administrador principal
✅ **Ubicaciones** — Estantes de inventario
✅ **Monedas** — COP (Peso Colombiano)
✅ **Empresa** — Jacket Store
✅ **Catálogo** — Categorías, marcas y tallas de chaquetas

---

## Seguridad

- Contraseñas encriptadas con bcrypt
- Laravel Sanctum para autenticación
- Spatie Permission para control de acceso por roles
- Activity Log habilitado

---

## Notas Técnicas

- **Base de Datos:** SQLite local (`database/database.sqlite`)
- **Stack:** Laravel 12, Blade, Bootstrap 5, SQLite
- **Modo:** Offline (no requiere internet)
- **Sync Cloud:** Opcional (configurar variables `CLOUD_DB_*` en `.env`)

---

**Sistema:** Jacket Store POS
**País:** Colombia
**Moneda:** COP (Peso Colombiano)
