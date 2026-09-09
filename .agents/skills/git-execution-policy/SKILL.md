---
name: git-execution-policy
description: >-
  Use this skill on various requests, coding tasks, feature implementations, bug fixes, refactoring, or whenever code is generated or modified. Strictly prohibits running git commands to push local changes to GitHub or cloud remotes, creating commits, switching branches, or modifying repository version control history automatically.
---

# Política de Restricción de Comandos Git y Control de Versiones

Esta skill establece la política de seguridad obligatoria para el manejo de Git y repositorios remotos (como GitHub). Se activa en peticiones varias y tareas de generación, modificación o refactorización de código.

---

## 1. Regla Principal

**El agente tiene ESTRICTAMENTE PROHIBIDO ejecutar de forma automática comandos de Git que modifiquen el historial de versiones o sincronicen cambios con la nube.**

Toda acción de versionado, confirmación de cambios (commits), gestión de ramas y publicación remota (push) queda bajo control y ejecución exclusiva y manual del usuario.

---

## 2. Comandos y Acciones Prohibidas

Siempre que se genere, actualice o elimine código, el agente **NO DEBE**:

1. **Pushear a la nube (`git push`):**
   * Queda terminantemente prohibido ejecutar `git push`, ya sea con o sin banderas (`--force`, `-u`, `--tags`, etc.).
   * Nunca enviar código local a GitHub ni a ningún repositorio remoto.

2. **Crear commits (`git commit`):**
   * No ejecutar `git commit` (ni variantes como `git commit -m`, `git commit -am`, etc.).
   * El agente no debe registrar commits en el árbol de Git automáticamente.

3. **Cambiar o manipular ramas (`git checkout`, `git switch`, `git branch`):**
   * No alternar de rama de trabajo automáticamente.
   * No crear, renombrar o eliminar ramas a menos que el usuario lo solicite explícitamente en su mensaje.

4. **Operaciones destructivas o de sincronización remota no solicitadas:**
   * No ejecutar `git merge`, `git rebase`, `git reset --hard` ni `git pull` de forma autónoma tras generar código.

---

## 3. Alcance de Trabajo Permitido

* **Modificaciones Locales:** Crear, editar y eliminar archivos dentro del workspace local.
* **Verificación y Pruebas Locales:** Ejecutar pruebas unitarias o de integración locales (ej. `php artisan test`), verificadores de sintaxis y linter.
* **Comandos de Lectura de Git (Opcional solo si es necesario para contextualizar):** Comandos informativos como `git status` o `git diff` únicamente si aportan contexto indispensable para resolver una tarea, sin aplicar mutaciones al repositorio.

---

## 4. Comunicación con el Usuario

Al concluir una tarea de generación o edición de código:
* Resumir claramente los archivos creados o modificados en el entorno local.
* Recordar que los cambios permanecen en local para que el usuario pueda revisarlos y realizar sus propios comandos de Git (`git add`, `git commit`, `git push`) cuando lo considere conveniente.
