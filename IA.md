# Uso de IA en este proyecto

## 1. Herramientas utilizadas

| Herramienta | Version / Modelo | Modo de uso | Aprox. % del trabajo |
|---|---|---|---|
| Gemini Pro | Terminal / chat asistido | Arquitectura inicial, generacion de boilerplate, consultas sobre PrestaShop y depuracion guiada. | 60% |
| Codex | Agente en el IDE / repositorio local | Revision final de entrega, auditoria de seguridad, correcciones de documentacion y endurecimiento de codigo. | 20% |
| GitHub Copilot | Extension VS Code | Autocompletado puntual y sugerencias de codigo repetitivo. | 10% |
| Ninguna | - | Pruebas manuales en Docker, validacion funcional en Back Office y decisiones finales de alcance. | 10% |

## 2. Configuracion del proyecto

### CLAUDE.md / AGENTS.md

Si. Se incluye `AGENTS.md` en la raiz del repositorio. Ese archivo define las restricciones usadas al trabajar con IA:

- PrestaShop 1.7.8.11 como version objetivo.
- PHP 7.4, evitando sintaxis exclusiva de PHP 8.
- Uso de `ObjectModel`, `ModuleAdminController`, hooks y APIs nativas de PrestaShop.
- Escapado obligatorio en Smarty.
- Instalacion y desinstalacion limpias.

Durante la revision final con Codex, este archivo se uso como checklist de contexto para detectar desviaciones.

### settings.json u otra configuracion equivalente

No se anadio ningun `settings.json` especifico al repositorio. Se usaron configuraciones locales del editor y extensiones instaladas, sin reglas privadas necesarias para ejecutar el proyecto.

## 3. Skills personalizadas

Ninguna.

## 4. Slash commands personalizados

Ninguno.

## 5. Sub-agentes invocados

No se usaron sub-agentes personalizados ni definiciones guardadas en `.claude/agents/` o equivalente.

El flujo fue interactivo: prompts concretos, revision manual del output, prueba en Docker y correccion.

## 6. MCPs (Model Context Protocol)

| MCP | Para que lo usaste | Que te aporto |
|---|---|---|
| Ninguno | - | No se conectaron servidores MCP durante el desarrollo. |

Con mas tiempo, podria haber sido util un MCP de filesystem o logs para leer trazas de PrestaShop/Apache directamente desde la sesion de IA. En este caso se uso Docker y terminal local.

## 7. Prompts importantes

### Prompt 1
- **Herramienta:** Gemini Pro
- **Prompt:** "Necesito crear un modulo nativo para PrestaShop 1.7 llamado productbadges para gestionar etiquetas personalizadas en productos. Proporcioname la estructura del archivo principal productbadges.php con los metodos de instalacion, desinstalacion, configuracion basica y el registro de los hooks administrativos y de frontend necesarios."
- **Que genero (resumen):** Estructura inicial de `productbadges.php`, constructor, `install()`, `uninstall()` y registro de hooks.
- **Que hice con el output:** Lo use como base, ajustando configuraciones por defecto y revisando compatibilidad con PrestaShop 1.7.8.11.

### Prompt 2
- **Herramienta:** Gemini Pro
- **Prompt:** "Genera la clase ProductBadge heredando de ObjectModel. Debe soportar tabla principal productbadges, tabla multilenguaje productbadges_lang y relacion muchos a muchos con productos."
- **Que genero (resumen):** Clase `ProductBadge`, definicion multilenguaje y metodos auxiliares para obtener badges y asignaciones.
- **Que hice con el output:** Revise los campos, valide colores con `isColor` y posteriormente anadi validacion explicita de `position`.

### Prompt 3
- **Herramienta:** Gemini Pro
- **Prompt:** "Como puedo inyectar las insignias personalizadas de un producto en frontend de PrestaShop 1.7 usando displayProductPriceBlock y limitar la cantidad segun configuracion?"
- **Que genero (resumen):** Logica inicial del hook de frontend y asignacion de variables a Smarty.
- **Que hice con el output:** Ajuste la deteccion de `id_product` para soportar arrays y objetos segun el contexto del hook.

### Prompt 4
- **Herramienta:** Gemini Pro
- **Prompt:** "Ayudame a crear la pantalla de configuracion del modulo con switches para activar globalmente, mostrar en listados, mostrar en ficha y definir maximo de badges."
- **Que genero (resumen):** Formulario `HelperForm` y lectura/escritura de valores en `Configuration`.
- **Que hice con el output:** Lo integre y despues limite el maximo permitido para evitar valores absurdos.

### Prompt 5
- **Herramienta:** Codex (Terminal CLI)
- **Prompt:** "Revisa esta prueba técnica como programador senior, especialmente los archivos de configuración AGENTS.md, IA.md y detecta puntos críticos a mejorar. El entorno Docker funciona correctamente y PrestaShop levanta."
- **Qué generó (resumen):** Una auditoría detallada de riesgos técnicos: detectó un escape faltante en la plantilla del módulo administrativo, la omisión de control de excepciones en las sentencias SQL de instalación/desinstalación, y oportunidades de mejora en la documentación del README.
- **Qué hice con el output:** Utilicé el reporte como una lista de control (checklist). Fui archivo por archivo aplicando manualmente las correcciones de seguridad e integridad relacional sugeridas.

### Prompt 6
- **Herramienta:** Codex (Terminal CLI)
- **Prompt:** "Ejecuta la refactorización de los puntos críticos detectados en la auditoría anterior. Elimina la lógica redundante y optimiza la estructura bajo el estándar de arquitectura limpia de PrestaShop."
- **Qué generó (resumen):** Propuestas de parches directos sobre los controladores y el ObjectModel para añadir validaciones estrictas y sanitización de datos.
- **Qué hice con el output:** No apliqué los cambios de forma automatizada ni autónoma. Inspeccioné cada línea del diff generado por la herramienta, ejecuté pruebas de validación sintáctica (PHP lint) dentro del contenedor Docker y solo integré al repositorio aquellas modificaciones técnicas que eran 100% defendibles y seguras.

## 8. Errores de la IA que detecte

### Error 1 - Sintaxis PHP incompleta en formulario
- **Que genero la IA (mal):** Una clave de array truncada en `renderForm`, dejando `'lang' =>` sin valor.
- **Por que estaba mal:** Rompia el parseo PHP y provocaba error 500 en Back Office.
- **Como lo corregiste:** Complete el campo como `'lang' => true` y probe de nuevo en modo debug.

### Error 2 - Cierre incorrecto de bucle Smarty
- **Que genero la IA (mal):** Una plantilla con un `{foreach}` mal cerrado.
- **Por que estaba mal:** Smarty lanzaba excepcion de compilacion y el frontend fallaba.
- **Como lo corregiste:** Sustitui la apertura sobrante por `{/foreach}`.

### Error 3 - Ruta incorrecta de plantilla admin
- **Que genero la IA (mal):** Ubicar `product_tab.tpl` directamente bajo `views/templates/`.
- **Por que estaba mal:** PrestaShop no encontraba la plantilla para el hook de producto.
- **Como lo corregiste:** Movi la plantilla a `views/templates/admin/product_tab.tpl`.

### Error 4 - Escape incompleto en Smarty
- **Que genero la IA (mal):** La plantilla admin imprimia valores de badge sin `|escape:'html':'UTF-8'`.
- **Por que estaba mal:** Permitiria XSS si un texto o color malicioso llegaba a la base de datos.
- **Como lo corregiste:** Anadi escapado en `id_productbadges`, `color_bg`, `color_text` y `text`.

### Error 5 - Resultado SQL ignorado durante instalacion
- **Que genero la IA (mal):** `productbadges.php` incluia `sql/install.php` y `sql/uninstall.php`, pero no comprobaba su valor de retorno.
- **Por que estaba mal:** El modulo podia quedar medio instalado si una consulta SQL fallaba.
- **Como lo corregiste:** Los scripts SQL ahora devuelven `true`/`false` y `install()`/`uninstall()` dependen de ese resultado.

## 9. Partes que NO use IA

- Pruebas manuales en Docker: arranque de contenedores, acceso al Back Office y comprobacion de que PrestaShop carga correctamente.
- Validacion funcional desde Back Office: instalacion del modulo, creacion/edicion de badges, asignacion a productos y comprobacion visual en la tienda.
- Decisiones finales de alcance: no anadir dependencias JS externas, no implementar Drag & Drop y no sobredimensionar la solucion fuera del tiempo estimado.
- Revision humana de los cambios aceptados: lectura del codigo generado, comprobacion de sintaxis y descarte de sugerencias que no encajaban con PrestaShop 1.7..

## 10. Reflexion final

- **Que me ahorro la IA:** acelero el boilerplate de PrestaShop, especialmente `HelperForm`, `ObjectModel`, hooks, estructura SQL y consultas repetitivas.
- **En que me entorpecio:** mezclo detalles de distintas versiones de PrestaShop y genero errores pequenos pero criticos en Smarty, rutas de plantillas y arrays PHP.
- **Que cambiaria si lo repitiera:** dividiria los prompts en tareas mas pequenas, probaria cada bloque antes de pedir el siguiente y mantendria desde el inicio una lista de errores de IA para documentarlos mejor en `IA.md`.
