# Cafetería - Proyecto PHP (XAMPP)
Versión completa, lista para correr en XAMPP. Tema visual: color salmón.

## Pasos para ejecutar (Fácil)
1. Copia la carpeta descomprimida en `C:\xampp\htdocs\cafeteria_full_salmon` (o `htdocs` equivalente).
2. Abre XAMPP y arrancá **Apache** y **MySQL**.
3. Abre `http://localhost/phpmyadmin` y ejecuta el archivo `init.sql` (pestaña SQL -> pegar todo -> ejecutar).
4. Abre en el navegador: `http://localhost/cafeteria_full_salmon/` — tienda pública.
5. Panel admin: `http://localhost/cafeteria_full_salmon/admin/login.php`
   - Usuario por defecto: `admin`
   - Contraseña por defecto: `password`
   - Cambialo en `admin/login.php` para seguridad.

## Notas
- Las imágenes se suben a la carpeta `uploads/`. Asegúrate que el usuario que corre Apache tenga permisos de escritura.
- Cambia credenciales DB en `db.php` si tu XAMPP usa otra configuración.
- Proyecto básico: puedes extenderlo (usuarios en DB, thumbnails, validaciones, paginación).
