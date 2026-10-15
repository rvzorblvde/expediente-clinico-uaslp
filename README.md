# Expediente Clínico Farmacéutico UASLP - Guía de Configuración Local

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/postgresql-%23316192.svg?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)

## Pasos para levantar el entorno de desarrollo

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/rvzorblvde/expediente-clinico-uaslp.git
   ```

2. **Acceder al proyecto**
   Abre una terminal y navega hasta encontrarte dentro de la carpeta raíz del proyecto clonado.

3. **Instalar dependencias de Node y PHP**
   ```bash
   npm ci
   composer install
   ```
   > **¿Por qué `npm ci` y no `npm install`?**
   > `npm install` recalcula las dependencias y puede actualizar versiones menores, modificando el archivo `package-lock.json` y generando cambios no deseados en Git. `npm ci` (Clean Install) lee estrictamente el lockfile e instala las versiones exactas definidas por el equipo, garantizando un entorno idéntico y limpio. Así evitamos hacer un commit innecesario.
   >
   > **¿Por qué `composer install`?**
   > Cumple la misma función para PHP: lee el archivo `composer.lock` para descargar las versiones exactas de las librerías necesarias.

4. **Auditoría de npm (Opcional)**
   Si la terminal muestra errores críticos de seguridad tras instalar los paquetes de Node, ejecuta la auditoría:
   ```bash
   npm audit
   ```
   *(Nota: Evita usar `npm audit fix` a menos que planees subir las actualizaciones de seguridad al repositorio, ya que modificará el `package-lock.json`).*

5. **Crear el archivo de entorno**
   Crea un nuevo archivo llamado `.env` en la raíz del proyecto.

6. **Copiar la configuración base**
   Copia todo el contenido del archivo `.env.example` y pégalo dentro del nuevo `.env` (alternativamente, ejecuta `cp .env.example .env`).
   Una vez copiado, genera la clave de encriptación de la aplicación:
   ```bash
   php artisan key:generate
   ```

7. **Configurar la base de datos**
   Abre el archivo `.env`, quita los comentarios de las líneas referentes a la base de datos (las que empiezan con `DB_`) y coloca las credenciales locales de tu proyecto.

   ```dotenv
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre
    DB_USERNAME=root
    DB_PASSWORD=
   ```

Las credenciales y nombre de `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` pueden llegar a cambiar. **Este archivo jamás debe subirse al repositorio**.


8. **Levantar los contenedores de Docker**
   ```bash
   docker compose up -d
   ```
   > **¿Para qué sirve esto?**
   > Este comando lee el archivo `docker-compose.yml` y levanta los servicios necesarios (como la base de datos PostgreSQL) en contenedores aislados. El parámetro `-d` (detached) ejecuta estos procesos en segundo plano, liberando la terminal para poder seguir escribiendo comandos.

9. **Migrar y poblar la base de datos**
   Asegúrate de esperar unos segundos después del paso anterior para que la base de datos termine de iniciar, y luego ejecuta:
   ```bash
   php artisan migrate:fresh --seed
   ```
   > **¿Para qué sirve esto?**
   > Realiza dos tareas fundamentales en el entorno de desarrollo:
   > 1. **`migrate:fresh`**: Borra todas las tablas existentes y ejecuta las migraciones desde cero para construir una estructura limpia.
   > 2. **`--seed`**: Inserta datos iniciales o de prueba (catálogos, usuarios por defecto, roles) para que la aplicación no esté vacía y pueda probarse de inmediato.

10. **Correr el proyecto**
    Para que el sistema funcione completamente, necesitas levantar tanto el servidor web de PHP como el servidor de desarrollo de los assets (JS/CSS). Puedes hacerlo de tres formas:

    * **Opción 1 (Múltiples terminales):**
      Abre una terminal y ejecuta:
      ```bash
      npm run dev
      ```
      Abre una segunda terminal y ejecuta:
      ```bash
      php artisan serve
      ```

    * **Opción 2 (Un solo comando en segundo plano):**
      Ejecuta ambos procesos en la misma ventana:
      ```bash
      npm run dev & php artisan serve
      ```

