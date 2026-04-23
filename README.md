#  Sistema de Autenticación PHP + MySQL

Sistema web seguro para gestión de usuarios con autenticación, perfil privado y actualización de datos.

##  Características

-  Registro de usuarios con validación de correo único
-  Login con verificación de credenciales y `password_hash`
-  Zona privada de perfil protegida por sesión
-  Actualización de nombre y correo con validación en servidor
-  Cambio de contraseña seguro con verificación de contraseña actual
-  Cierre de sesión que destruye la sesión correctamente

##  Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB
- Servidor web (Apache/Nginx)
- Extensiones PDO y OpenSSL habilitadas

##  Instalación Local

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/auth-system.git