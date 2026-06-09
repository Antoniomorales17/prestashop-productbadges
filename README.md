# Product Badges Module - PrestaShop 1.7

Modulo nativo para PrestaShop 1.7 que permite crear, gestionar y asignar etiquetas visuales reutilizables a productos del catalogo, como `NUEVO`, `OFERTA`, `EXCLUSIVO` o `ULTIMAS UNIDADES`.

Este repositorio forma parte de la prueba tecnica de Blinders Group para la posicion de programador/a especializado/a en e-commerce.

## Entorno probado

- **PrestaShop:** 1.7.8.11.
- **PHP:** 7.4, usando la imagen oficial `prestashop/prestashop:1.7.8.11-apache`.
- **Base de datos:** MySQL 5.7.
- **Bootstrap del modulo:** `true`.
- **Dependencias externas:** ninguna. No se usa Composer ni librerias JS externas.

## Instalacion rapida con Docker

El repositorio incluye un `docker-compose.yml` para levantar una tienda PrestaShop limpia y probar el modulo sin preparar un servidor local manualmente.

1. Clonar el repositorio:

```bash
git clone https://github.com/Antoniomorales17/prestashop-productbadges
cd prestashop-productbadges
```

2. Levantar los contenedores:

```bash
docker compose up -d
```

3. Esperar un par de minutos a que PrestaShop termine la autoinstalacion silenciosa.

4. Acceder al Back Office:

- **URL:** http://localhost:8080/admin-dev
- **Email:** demo@prestashop.com
- **Password:** prestashop_demo

5. Ir a **Modulos > Gestor de Modulos**, localizar `productbadges` en modulos no instalados e instalarlo.

## Uso del modulo

- La configuracion global se encuentra en la pantalla de configuracion del modulo.
- La gestion CRUD de badges se encuentra en **Catalogo > Product Badges**.
- La asignacion de badges a productos se realiza desde la ficha de producto, en la pestana adicional del modulo.

## Funcionalidad implementada

- Creacion y edicion de badges desde Back Office.
- Campos: texto traducible, color de fondo, color de texto, posicion izquierda/derecha y estado activo/inactivo.
- Relacion muchos a muchos entre productos y badges mediante tabla intermedia.
- Configuracion global:
  - activar/desactivar modulo,
  - mostrar en listados,
  - mostrar en ficha de producto,
  - numero maximo de badges visibles por producto.
- Renderizado frontend en listados estandar, busqueda, home y ficha de producto cuando el tema expone el hook compatible.
- Instalacion y desinstalacion limpias de tablas, configuraciones, hooks y pestana admin.

## Decisiones tecnicas relevantes

- **Separacion MVC:** la gestion de Back Office se delega en `AdminProductBadgesController`. El archivo principal `productbadges.php` queda centrado en instalacion, configuracion y hooks.
- **ObjectModel:** la entidad `ProductBadge` hereda de `ObjectModel` y usa definicion multilenguaje para el campo `text`.
- **Relacion M:N:** la asignacion a productos se guarda en `productbadges_product`, con clave primaria compuesta para evitar duplicados.
- **Compatibilidad:** el codigo evita caracteristicas exclusivas de PHP 8 y PrestaShop 8.
- **Sin dependencias externas:** se usa la API nativa de PrestaShop, `HelperForm`, `HelperList`, hooks y `DbQuery`.
- **Carga de assets:** el CSS se registra desde `hookHeader` y solo actua sobre clases del modulo.

## Seguridad y validacion

- Los IDs se castean a entero antes de usarse en consultas.
- Las consultas manuales usan `DbQuery`, `Db::getInstance()`, `_DB_PREFIX_` de forma indirecta por las APIs de PrestaShop y casting estricto.
- Los colores se validan con `Validate::isColor` a traves de `ObjectModel`.
- La posicion de la badge se limita a `left` o `right` en el modelo.
- Las variables de las plantillas Smarty se escapan con `|escape:'html':'UTF-8'`.
- La instalacion y desinstalacion comprueban el resultado de los scripts SQL antes de continuar.

## Multilenguaje y multitienda

El texto de la badge es multilenguaje mediante la tabla `productbadges_lang`, con soporte para los idiomas activos de la tienda. Se ha probado con el contexto de PrestaShop 1.7.8.11 y el modulo no introduce dependencias que rompan una instalacion multitienda.

No se implemento una configuracion diferenciada por tienda para cada badge. La decision fue mantener un comportamiento global coherente con el contexto activo, suficiente para el alcance de la prueba.

## Que se dejo fuera y por que

- **Ordenamiento visual con Drag & Drop:** no se incluyo una libreria JS de arrastre para ordenar manualmente la prioridad de las insignias. Se priorizo mantener el modulo sin dependencias externas.
- **Ajax avanzado en navegacion por facetas:** se garantiza el renderizado en listados estandar, busqueda, home y ficha de producto. Los modulos de filtrado por facetas pueden recargar bloques por Ajax y requeririan escuchar eventos especificos del tema o del modulo de facetas.
- **Tests unitarios:** no se incluyeron por tiempo. Se priorizo prueba manual en Docker, instalacion/desinstalacion y validacion funcional en Back Office.

## Uso de IA

Durante el desarrollo se uso IA como apoyo, no como sustituto de revision tecnica. La configuracion y trazabilidad estan documentadas en:

- `AGENTS.md`: instrucciones de contexto y restricciones del proyecto usadas durante el trabajo con IA.
- `IA.md`: herramientas usadas, prompts relevantes, errores detectados en outputs de IA y correcciones aplicadas.

En una revision final se uso Codex como agente de apoyo para auditar seguridad, documentacion y consistencia de entrega. Los cambios aceptados fueron revisados antes de integrarse.
