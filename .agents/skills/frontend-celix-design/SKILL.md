---
name: frontend-celix-design
description: >-
  Use this skill whenever the user requests frontend development, UI components, HTML, CSS, Blade templates, views, forms, buttons, tables, cards, navigation bars, or layout design for the CelIx project. Enforces the CelIx brand color palette, typography hierarchy, component styling rules, Bootstrap Icons, Bootstrap CSS utilities and JS components to reduce boilerplate, strict separation of CSS styles into global or component-specific CSS files, strict separation of JavaScript code into public JS files, mandatory responsive design across all devices (mobile, tablet, desktop), and modular architecture (layouts, reusable components, and views).
---

# CelIx Frontend & Design System Skill

Esta skill define las reglas obligatorias de diseño visual, interfaz de usuario (UI), componentes, tipografía, paleta de colores e iconografía para cualquier desarrollo de frontend en el proyecto CelIx.

---

## 1. Colorimetría y Variables CSS

Utilizar estas variables CSS (o sus valores hexadecimales correspondientes) para garantizar la consistencia visual y de marca:

```css
:root {
  /* Marca y Énfasis */
  --color-primary: #ED1C24;       /* Rojo CelIx: Botones principales, enlaces activos, elementos de interacción */
  --color-primary-dark: #C9141C;  /* Rojo Oscuro: Encabezados destacados, hover de elementos principales */

  /* Fondo y Superficie */
  --color-bg: #F5F5F5;            /* Fondo General: Fondo de página detrás de contenedores */
  --color-surface: #FFFFFF;       /* Fondo de Superficie: Tarjetas, formularios, tablas, contenedores */

  /* Texto */
  --color-text-main: #171717;     /* Texto Principal: Casi negro para títulos y cuerpo principal */
  --color-text-muted: #6B7280;    /* Texto Secundario: Gris medio para información secundaria y ayuda */

  /* Bordes y Divisores */
  --color-border: #E5E5E5;        /* Bordes: Líneas divisorias, bordes de inputs y tarjetas */

  /* Estados y Feedback */
  --color-success: #16A34A;       /* Éxito: Confirmación, badges "Disponible" / "Completado" */
  --color-warning: #D97706;       /* Advertencia: Alertas, stock bajo */
  --color-error: #DC2626;         /* Error / Peligro: Mensajes de error, validaciones fallidas, botones eliminar */
}
```

---

## 2. Tipografía y Jerarquía

* **Familia tipográfica recomendada:** Sans-serif del sistema:
  `font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;`

| Elemento | Tamaño | Peso (Weight) | Color |
| :--- | :--- | :--- | :--- |
| **Título de Página** | `24px` (`1.5rem`) | `700` (Bold) | `#171717` |
| **Subtítulo / Título de Sección** | `18px` (`1.125rem`) | `600` (Semi-bold) | `#171717` |
| **Texto Normal / Cuerpo** | `14px` (`0.875rem`) | `400` (Regular) | `#171717` |
| **Etiquetas de Formulario (Labels)** | `13px` (`0.8125rem`) | `500` (Medium) | `#171717` |
| **Encabezado de Tabla (`<th>`)** | `13px` (`0.8125rem`) | `600` (Semi-bold) | `#171717` (o `#6B7280`) |
| **Texto de Tabla (`<td>`)** | `13px` (`0.8125rem`) | `400` (Regular) | `#171717` |
| **Texto de Botones** | `14px` (`0.875rem`) | `500` (Medium) | Según botón |
| **Texto Secundario / Ayuda** | `12px` (`0.75rem`) | `400` (Regular) | `#6B7280` |

---

## 3. Estilos de Componentes

### A. Botones (`Buttons`)
* **General:** `border-radius: 4px` (`0.25rem`), `padding: 0.5rem 1rem`, tipografía `14px` peso `500`. Transición suave (`transition: all 0.2s ease-in-out`).

* **Botón Principal (Primary):**
  * Fondo: `#ED1C24` | Texto: `#FFFFFF` | Borde: `1px solid #ED1C24`
  * Hover: Fondo `#C9141C` | Borde: `#C9141C`
* **Botón Secundario (Secondary):**
  * Fondo: `#FFFFFF` | Texto: `#171717` | Borde: `1px solid #E5E5E5` (o `#D1D5DB`)
  * Hover: Fondo `#F5F5F5` | Borde: `#E5E5E5`
* **Botón de Peligro / Acción Crítica (Danger):**
  * Fondo: `#DC2626` | Texto: `#FFFFFF` | Borde: `1px solid #DC2626`
  * Hover: Fondo `#B91C1C` | Borde: `#B91C1C`
* **Botón de Éxito (Success):**
  * Fondo: `#16A34A` | Texto: `#FFFFFF` | Borde: `1px solid #16A34A`
  * Hover: Fondo `#15803D` | Borde: `#15803D`

### B. Entradas de Formulario (`Inputs / Textareas / Selects`)
* **General:** Fondo blanco, `border-radius: 4px`, padding `0.375rem 0.75rem`.
* **Estado Normal:**
  * Fondo: `#FFFFFF` | Borde: `1px solid #E5E5E5` | Texto: `#171717`
* **Estado de Foco (Focus):**
  * Borde: `1px solid #ED1C24`
  * Sombra: Pequeño anillo difuminado en rojo suave (`box-shadow: 0 0 0 3px rgba(237, 28, 36, 0.15);`)
  * `outline: none;`
* **Estado de Error:**
  * Borde: `1px solid #DC2626`
  * Texto de ayuda de error asociado: `#DC2626` (tamaño `12px`)
* **Placeholders:**
  * Color: `#9CA3AF`

### C. Barra de Navegación (`Navbar`)
* Fondo: `#FFFFFF`
* Sombra: Box-shadow inferior sutil (`0 2px 4px rgba(0, 0, 0, 0.05)`).
* Enlaces (Normal): Color `#171717`, tamaño `14px`, peso regular o medium.
* Enlaces (Hover / Activo): Color `#ED1C24`, peso `600` cuando esté activo.

### D. Tablas (`Data Tables`)
* Fondo de Tabla: `#FFFFFF`
* Encabezado (`<thead>` / `<th>`): Fondo `#F9FAFB` o `#F5F5F5`, texto `13px`, peso `600`, color `#171717`.
* Cuerpo (`<td>`): Texto `13px`, peso `400`, color `#171717`, borde inferior `1px solid #E5E5E5`.
* Filas alternas (Striped): Opcional fondo `#F9FAFB` en filas pares para mejorar legibilidad.

### E. Tarjetas (`Cards`)
* Fondo: `#FFFFFF`
* Borde: `1px solid #E5E5E5` (o sin borde si la elevación es suficiente).
* Sombra: `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);`
* Bordes: `border-radius: 8px` (`0.5rem`).
* Padding interno: `1rem` a `1.5rem` (`16px` a `24px`).

### F. Badges / Tags de Estado
* Estilo general: Texto pequeño (`11px` - `12px`), peso `500`, bordes redondeados tipo píldora (`border-radius: 9999px` o `50px`), padding `0.25rem 0.625rem`.
* **Disponible / Completado:** Fondo `#DCFCE7`, Texto `#166534`.
* **Advertencia / Stock Bajo:** Fondo `#FEF3C7`, Texto `#92400E`.
* **Crítico / Error:** Fondo `#FEE2E2`, Texto `#991B1B`.

---

## 4. Espaciado y Layout (Grid)

* **Unidad base de espaciado:** `4px` (`0.25rem`).
* **Padding / Margin entre elementos:** `16px` (`1rem`), `24px` (`1.5rem`), `32px` (`2rem`).
* **Padding de contenedores principales:** `16px` (`1rem`) o `24px` (`1.5rem`).

---

## 5. Iconografía y Reglas Visuales Críticas

* **PROHIBIDO EL USO DE EMOJIS** en interfaces de usuario, botones, títulos, tablas o alertas.
* **Íconos requeridos:** Usar exclusivamente **Bootstrap Icons** (ej: `<i class="bi bi-trash"></i>`, `<i class="bi bi-check-circle"></i>`, `<i class="bi bi-pencil"></i>`).
* **Casos no especificados:** Cuando se requiera un elemento o variante no descrita explícitamente en estas directrices, la IA deberá seleccionar estilos conservando estricta armonía visual con la paleta de colores (#ED1C24, escala de grises, bordes limpios y fuentes sans-serif).

---

## 6. Organización y Separación de Archivos CSS (Obligatorio)

* **Prohibido incrustar estilos en las vistas:** No utilizar bloques `<style>` embebidos extensos ni estilos inline dentro de los archivos de plantillas o vistas Blade (`.blade.php`).
* **Separación de estilos frontend en archivos `.css`:**
  * **Estilos Globales / Base:** Mantener en un archivo CSS global (ej. `resources/css/app.css` o archivos importados en él como `celix-theme.css`) las variables del sistema (`:root`), reseteos, tipografía base y componentes comunes transversales (navbar, botones globales, tablas base).
  * **Estilos Específicos por Componente o Vista:** Cada componente, módulo o vista con estilos particulares debe contar con su propio archivo CSS específico e independiente (ej. en `resources/css/` o vinculado a través de `public/css/`), manteniéndolo modular, desacoplado y fácil de mantener.

---

## 7. Diseño Responsivo Obligatorio (Mobile-First & Adaptabilidad)

Todo el contenido, componentes, vistas y plantillas Blade deben ser **completamente responsivos** y garantizar una experiencia de usuario óptima en teléfonos móviles, tablets y monitores de escritorio:

* **Puntos de Interrupción (Breakpoints) de Referencia:**
  * **Móvil (Mobile):** `< 640px` (`sm`)
  * **Tablet:** `640px` - `1023px` (`md`)
  * **Escritorio (Desktop):** `≥ 1024px` (`lg`, `xl`)

* **Directrices Obligatorias de Adaptabilidad:**
  1. **Contenedores y Layouts Fluidos:**
     * Nunca usar anchos fijos (`width: 500px`) que puedan desbordar la pantalla en dispositivos pequeños. Utilizar `width: 100%`, `max-width`, porcentajes o unidades relativas (`rem`, `vh`/`vw`).
     * Evitar cualquier desbordamiento horizontal (`overflow-x`) en la ventana del navegador.
     * Utilizar Flexbox responsivo (`flex-wrap: wrap`, cambio a `flex-direction: column` en móvil) o CSS Grid adaptativo (`repeat(auto-fit, minmax(...))`).
  2. **Tablas de Datos Responsivas:**
     * Las tablas (`<table>`) deben estar encapsuladas en un contenedor con desplazamiento horizontal suave (`overflow-x: auto; -webkit-overflow-scrolling: touch;`) o transformarse visualmente en tarjetas/bloques para pantallas móviles, garantizando que nunca rompan el layout.
  3. **Formularios, Botones y Zonas Táctiles (Tap Targets):**
     * En resoluciones móviles, los botones de acción principal y grupos de botones deben adaptarse al ancho completo (`width: 100%`) o apilarse verticalmente para facilitar la pulsación táctil con el pulgar (mínimo `44px` de altura recomendada).
     * Los campos de formulario (`input`, `select`, `textarea`) deben ocupar el 100% del ancho del contenedor en pantallas pequeñas.
  4. **Navegación y Cabeceras (Navbar):**
     * Las barras de navegación deben adaptarse a pantallas reducidas (mediante menú móvil colapsable o distribución compacta optimizada sin solapamiento de elementos).
  5. **Tipografía e Imágenes:**
     * Los tamaños de títulos y textos deben reducirse proporcionalmente en móviles usando media queries o unidades fluidas para evitar quiebres de línea agresivos.
     * Todas las imágenes, logotipos y gráficos SVG deben tener `max-width: 100%; height: auto; display: block;`.

---

## 8. Modularización y Arquitectura Blade (Layouts, Componentes y Vistas)

Para garantizar la mantenibilidad del código, evitar archivos excesivamente largos o monolíticos y maximizar la reutilización (DRY), el desarrollo de frontend en Blade debe estructurarse de manera modular:

* **Estructura de Directorios Obligatoria (`resources/views/`):**
  * **`resources/views/layouts/` (Plantillas Maestras):**
    * Contienen la estructura base del documento HTML (`<!DOCTYPE html>`, `<head>`, metaetiquetas, fuentes del sistema, enlaces a Bootstrap Icons, enlaces a CSS globales/específicos y directivas `@yield('content')` o `{{ $slot }}`).
    * Evitar duplicar la cabecera HTML, apertura del `<body>` o scripts en cada vista individual.
  * **`resources/views/components/` (Componentes Reutilizables):**
    * Bloques de interfaz autónomos y reciclables que pueden invocarse mediante `<x-nombre-componente />` o `@include('components.nombre-componente')`.
    * **Elementos requeridos para modularizar cuando aplique:**
      * **Barras de Navegación (`navbar.blade.php`):** Debe modularizarse como componente para reutilizarse en todas las vistas públicas o privadas correspondientes.
      * **Pies de página (`footer.blade.php`):** Extraer los copyrights y enlaces legales a su propio componente.
      * **Logos y Marca (`brand-logo.blade.php`):** Centralizar la representación visual del isotipo y logotipo corporativo de CelIx.
      * **Alertas y Feedback (`alert.blade.php`):** Manejo uniforme de mensajes de éxito, advertencia y error.
      * **Tarjetas o Envoltorios de Formulario:** Estructuras recurrentes (como cajas divididas o modales).
  * **`resources/views/<seccion>/` (Vistas de Negocio):**
    * Organizadas por dominio o función (ej. `resources/views/public/`, `resources/views/auth/`, `resources/views/admin/`).
    * Deben extender de un layout maestro (`@extends('layouts....')`) e incluir los componentes necesarios, manteniéndose concisas y enfocadas en la lógica propia de la pantalla.

* **Criterios para Aplicar Modularización:**
  * **Elemento repetido en 2 o más vistas:** Extraer inmediatamente a componente o layout.
  * **Vistas extensas o complejas:** Si un archivo supera un tamaño que dificulte su lectura o mantenimiento, descomponer las secciones visuales lógicas en subcomponentes o vistas parciales (`@include`).

---

## 9. Separación Estricta de Scripts JavaScript (Obligatorio)

Para asegurar la correcta separación de responsabilidades, el mantenimiento escalable y la limpieza del código:

* **Prohibido incrustar código JavaScript en las vistas:** Nunca incluir bloques `<script>` con lógica o código inline directamente dentro de las plantillas o vistas Blade (`.blade.php`).
* **Separación en archivos `.js` dedicados:**
  * Todo código JavaScript debe residir en archivos externos específicos dentro del directorio público de scripts (`public/js/`), debidamente estructurados por dominio o módulo funcional:
    * `public/js/auth/`: Scripts de autenticación y recuperación de cuenta (ej. `login.js`, `recover-password.js`).
    * `public/js/admin/`: Scripts de administración, dashboards y CRUDs (ej. `admin-navbar.js`, `users.js`).
    * `public/js/components/`: Scripts asociados a componentes reutilizables cuando aplique.
* **Vinculación en Blade:**
  * Cargar los scripts utilizando la directiva asset: `<script src="{{ asset('js/...') }}"></script>`.
  * En vistas que extienden de layouts maestros con pilas (`@stack('scripts')`), vincular el script específico mediante `@push('scripts') <script src="{{ asset('js/...') }}"></script> @endpush`.

---

## 10. Uso de Bootstrap para Reducción de Reglas CSS y Código JS

Se permite y promueve el uso de las clases de utilidad y componentes de **Bootstrap** para acelerar el desarrollo, reducir la cantidad de reglas CSS personalizadas en archivos externos y disminuir la necesidad de código JavaScript manual:

### A. Clases de Utilidad y Componentes CSS de Bootstrap
* **Grid y Flexbox:** Emplear las clases nativas de Bootstrap para alineación y maquetación (`container`, `row`, `col-*`, `d-flex`, `flex-column`, `flex-wrap`, `justify-content-*`, `align-items-*`, `gap-*`, etc.) en lugar de declarar reglas CSS repetitivas en archivos externos.
* **Espaciado y Tipografía:** Aprovechar las utilidades estándar de margen y padding (`m-*`, `p-*`, `mb-3`, `me-2`, etc.), alineación (`text-center`, `text-end`), y pesos tipográficos (`fw-bold`, `fw-semibold`, `fs-6`).
* **Componentes Base:** Se pueden utilizar clases estructurales de Bootstrap como `table`, `table-responsive`, `table-hover`, `card`, `card-body`, `badge`, `form-control`, `form-select`, `modal`, `modal-dialog`, `alert`, etc.

### B. Componentes Interactivos y JavaScript de Bootstrap
* **Menús, Modales y Desplegables:** Se puede aprovechar la API oficial de Bootstrap JS (ej. `new bootstrap.Modal(...)`, `bootstrap.Collapse`) y sus atributos de datos (`data-bs-toggle="modal"`, `data-bs-dismiss="modal"`, `data-bs-target="..."`) para evitar escribir lógica manual de manipulación de DOM, backdrop o listeners de teclado en JavaScript.
* **Ubicación del JS:** Cualquier script que inicialice o configure componentes de Bootstrap debe ubicarse en los archivos dedicados dentro de `public/js/`, respetando la separación estricta de scripts.

### C. REGLA INQUEBRANTABLE: Respeto Estricto a los Estilos Iniciales de CelIx
El uso de Bootstrap es una herramienta de optimización y reducción de código; **bajo ninguna circunstancia reemplaza ni degrada la identidad visual y directrices obligatorias de CelIx**:
1. **Paleta de Marca CelIx (Prohibido el azul por defecto de Bootstrap):**
   * El color primario del proyecto es **Rojo CelIx (`#ED1C24`)** con hover `#C9141C`. Las clases `btn-primary`, enlaces activos, bordes de foco (`focus-ring`), switches activos o badges primarios deben sobrescribirse o personalizarse para mantener el rojo corporativo (`#ED1C24`), jamás utilizar el azul estándar (`#0d6efd`) de Bootstrap.
   * Respetar rigurosamente los colores de fondo (`#F5F5F5`), superficie (`#FFFFFF`), texto principal (`#171717`), texto secundario (`#6B7280`) y bordes (`#E5E5E5`).
2. **Tipografía del Sistema:** Debe mantenerse la tipografía sans-serif del sistema (`system-ui, -apple-system, Segoe UI, Roboto...`).
3. **Cero Emojis y Uso Exclusivo de Bootstrap Icons:** Todo ícono debe ser de Bootstrap Icons (`bi bi-*`). Prohibido terminantemente el uso de emojis.
4. **Prohibición de Estilos Inline en HTML:** No utilizar `style="..."` en las etiquetas HTML/Blade. Si una clase de Bootstrap requiere personalización o ajuste fino, debe aplicarse mediante una clase CSS en su respectivo archivo `.css`.
5. **Separación de Archivos CSS y JS:** Toda regla de estilo personalizada o sobrescritura debe residir en sus respectivos archivos CSS (`public/css/`), y todo script en sus archivos JS (`public/js/`).
6. **Diseño Responsivo Obligatorio:** Garantizar que todo layout, tabla, modal y formulario sea 100% responsivo y táctil en móviles, tablets y escritorios.



