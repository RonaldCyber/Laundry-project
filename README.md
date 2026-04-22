# Proyecto grupal - Avance 1  
## Sistema web para lavandería **Lavandería AquaClean**

Este proyecto corresponde al **avance 1** del proyecto grupal de la materia **Construcción de Software**.  
Se desarrolló una aplicación web básica orientada a la gestión de servicios de una lavandería.

## Tecnologías utilizadas
- HTML5
- CSS3
- JavaScript (Vanilla JS)
- localStorage del navegador

## Estructura del proyecto
```bash
lavanderia_proyecto_avance1/
│
├── index.html
├── styles.css
├── script.js
├── README.md
├── articulo_avance.md
├── bitacora_actividades.md
└── docs/
    └── Informe_Entrega_Avance_1_Lavanderia.docx
```

## Funcionalidades implementadas
1. **Pantalla principal de bienvenida**
2. **Visualización de servicios**
3. **Cotizador automático por servicio y kilogramos**
4. **Registro de pedidos**
5. **Generación de código único**
6. **Seguimiento de pedido**
7. **Tabla de pedidos almacenados**
8. **Persistencia local mediante localStorage**

## Instrucciones para abrir el proyecto en Visual Studio Code
1. Descargar y descomprimir el archivo `.zip`.
2. Abrir **Visual Studio Code**.
3. Ir a **File > Open Folder**.
4. Seleccionar la carpeta `lavanderia_proyecto_avance1`.
5. Abrir el archivo `index.html`.
6. Ejecutar el proyecto con una de estas opciones:
   - Doble clic sobre `index.html`, o
   - Instalar la extensión **Live Server** en VS Code y presionar **Go Live**.

## Recomendación de demostración
Para mostrar rápidamente el sistema al docente:
1. Ingresar a la sección **Bitácora visual de pedidos**.
2. Hacer clic en **Cargar datos de ejemplo**.
3. Revisar la tabla generada.
4. Probar el módulo de **seguimiento** con códigos como `LAV-0001`, `LAV-0002` o `LAV-0003`.

## Comentarios del código
El archivo `script.js` contiene comentarios explicativos sobre:
- configuración del sistema,
- acceso a elementos del DOM,
- funciones de cálculo,
- registro de pedidos,
- seguimiento,
- persistencia de datos.

## Posibles mejoras futuras
- Conexión con base de datos real.
- Inicio de sesión por roles.
- Panel administrativo.
- Reportes de ventas.
- Historial por cliente.
- Integración con WhatsApp para confirmaciones.
