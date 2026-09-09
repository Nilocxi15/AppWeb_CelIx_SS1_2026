---
name: frontend-celix-design
description: >-
  Use this skill whenever the user requests frontend development, UI components, HTML, CSS, Blade templates, views, forms, buttons, tables, cards, navigation bars, or layout design for the CelIx project. Enforces the CelIx brand color palette, typography hierarchy, component styling rules, and Bootstrap Icons.
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
