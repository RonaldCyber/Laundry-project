/* ==========================================================================
   SCRIPT PRINCIPAL DEL SISTEMA DE LAVANDERÍA
   Este archivo contiene la lógica del proyecto:
   - catálogo de servicios
   - cotizador
   - registro de pedidos
   - seguimiento de pedidos
   - persistencia local con localStorage
   ========================================================================== */

/* -------------------------
   CONFIGURACIÓN DEL SISTEMA
   ------------------------- */

/*
  Catálogo base de servicios.
  Cada servicio tiene:
  - id: identificador interno
  - nombre: nombre visible
  - descripcion: detalle del servicio
  - precioKg: precio por kilogramo
*/
const services = [
  {
    id: "lavado",
    nombre: "Lavado general",
    descripcion: "Lavado estándar para ropa de uso diario con proceso normal.",
    precioKg: 2.5
  },
  {
    id: "planchado",
    nombre: "Lavado + planchado",
    descripcion: "Servicio completo para ropa que requiere presentación formal.",
    precioKg: 3.5
  },
  {
    id: "edredones",
    nombre: "Edredones y cobijas",
    descripcion: "Tratamiento especial para prendas de gran tamaño.",
    precioKg: 4.75
  },
  {
    id: "delicada",
    nombre: "Ropa delicada",
    descripcion: "Lavado especial para telas delicadas y prendas sensibles.",
    precioKg: 4.2
  },
  {
    id: "seco",
    nombre: "Lavado en seco",
    descripcion: "Atención para prendas que requieren cuidado especializado.",
    precioKg: 5.4
  },
  {
    id: "uniformes",
    nombre: "Uniformes empresariales",
    descripcion: "Servicio enfocado en uniformes de trabajo y lotes por volumen.",
    precioKg: 3.9
  }
];

/*
  Costos adicionales configurables.
  Esto facilita modificar la lógica del negocio sin cambiar varias líneas.
*/
const EXPRESS_FEE = 3.0;
const PICKUP_FEE = 2.0;

/* -------------------------
   ACCESO A ELEMENTOS DEL DOM
   ------------------------- */

const servicesContainer = document.getElementById("servicesContainer");
const quoteForm = document.getElementById("quoteForm");
const quoteService = document.getElementById("quoteService");
const quoteKg = document.getElementById("quoteKg");
const quoteExpress = document.getElementById("quoteExpress");
const quotePickup = document.getElementById("quotePickup");
const quoteResult = document.getElementById("quoteResult");

const orderForm = document.getElementById("orderForm");
const orderService = document.getElementById("orderService");
const orderMessage = document.getElementById("orderMessage");

const trackingForm = document.getElementById("trackingForm");
const trackingCode = document.getElementById("trackingCode");
const trackingResult = document.getElementById("trackingResult");

const ordersTableBody = document.getElementById("ordersTableBody");
const seedDataBtn = document.getElementById("seedDataBtn");
const clearDataBtn = document.getElementById("clearDataBtn");

/* -------------------------
   FUNCIONES DE UTILIDAD
   ------------------------- */

/*
  Devuelve todos los pedidos almacenados.
  Si no existe información previa, regresa un arreglo vacío.
*/
function getOrders() {
  return JSON.parse(localStorage.getItem("laundry_orders")) || [];
}

/*
  Guarda un arreglo de pedidos dentro del navegador.
*/
function saveOrders(orders) {
  localStorage.setItem("laundry_orders", JSON.stringify(orders));
}

/*
  Formatea valores numéricos en dólares.
*/
function formatCurrency(value) {
  return new Intl.NumberFormat("es-EC", {
    style: "currency",
    currency: "USD"
  }).format(value);
}

/*
  Busca un servicio por su id.
*/
function findServiceById(serviceId) {
  return services.find(service => service.id === serviceId);
}

/*
  Genera un código secuencial amigable para el pedido.
  Ejemplo: LAV-0001, LAV-0002...
*/
function generateOrderCode() {
  const orders = getOrders();
  const nextNumber = orders.length + 1;
  return `LAV-${String(nextNumber).padStart(4, "0")}`;
}

/*
  Define un estado inicial del pedido.
  Para el avance se maneja una ruta simple de estados.
*/
function getInitialStatus() {
  return "Recibido";
}

/*
  Genera un estado de ejemplo para datos precargados.
*/
function getRandomStatus(index) {
  const statuses = ["Recibido", "Lavado", "Secado", "Listo para entrega", "Entregado"];
  return statuses[index % statuses.length];
}

/*
  Calcula el total del pedido a partir de:
  - servicio seleccionado
  - cantidad de kilogramos
  - recargo exprés
  - recargo por domicilio
*/
function calculateTotal(serviceId, kg, isExpress, isPickup) {
  const service = findServiceById(serviceId);

  if (!service) {
    return 0;
  }

  let total = service.precioKg * kg;

  if (isExpress) {
    total += EXPRESS_FEE;
  }

  if (isPickup) {
    total += PICKUP_FEE;
  }

  return Number(total.toFixed(2));
}

/*
  Devuelve una clase CSS según el estado del pedido.
  Esto permite colorear visualmente la tabla.
*/
function getStatusClass(status) {
  const normalized = status.toLowerCase();

  if (normalized.includes("recibido")) return "status status--recibido";
  if (normalized.includes("lavado")) return "status status--lavado";
  if (normalized.includes("secado")) return "status status--secado";
  if (normalized.includes("listo")) return "status status--listo";
  if (normalized.includes("entregado")) return "status status--entregado";

  return "status";
}

/* -------------------------
   FUNCIONES DE RENDERIZADO
   ------------------------- */

/*
  Pinta las tarjetas de servicios en pantalla.
*/
function renderServices() {
  servicesContainer.innerHTML = services.map(service => `
    <article class="card service-card">
      <h3>${service.nombre}</h3>
      <p>${service.descripcion}</p>
      <span class="service-card__price">${formatCurrency(service.precioKg)} por kg</span>
    </article>
  `).join("");
}

/*
  Carga las opciones de servicios en los select del cotizador y formulario.
*/
function loadServiceOptions() {
  const options = services.map(service => `
    <option value="${service.id}">
      ${service.nombre} - ${formatCurrency(service.precioKg)}/kg
    </option>
  `).join("");

  quoteService.innerHTML = options;
  orderService.innerHTML = options;
}

/*
  Muestra la tabla de pedidos guardados.
*/
function renderOrdersTable() {
  const orders = getOrders();

  if (orders.length === 0) {
    ordersTableBody.innerHTML = `
      <tr>
        <td colspan="6">No existen pedidos registrados todavía.</td>
      </tr>
    `;
    return;
  }

  ordersTableBody.innerHTML = orders.map(order => `
    <tr>
      <td>${order.codigo}</td>
      <td>${order.cliente}</td>
      <td>${order.servicioNombre}</td>
      <td>${order.kg} kg</td>
      <td>${formatCurrency(order.total)}</td>
      <td><span class="${getStatusClass(order.estado)}">${order.estado}</span></td>
    </tr>
  `).join("");
}

/* -------------------------
   COTIZADOR
   ------------------------- */

/*
  Evento de cálculo de precio.
  Evita el recargo de página y muestra un resumen claro.
*/
quoteForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const serviceId = quoteService.value;
  const kg = Number(quoteKg.value);
  const isExpress = quoteExpress.checked;
  const isPickup = quotePickup.checked;

  const selectedService = findServiceById(serviceId);
  const total = calculateTotal(serviceId, kg, isExpress, isPickup);

  quoteResult.innerHTML = `
    <p><strong>Servicio:</strong> ${selectedService.nombre}</p>
    <p><strong>Kilogramos:</strong> ${kg} kg</p>
    <p><strong>Exprés:</strong> ${isExpress ? "Sí" : "No"}</p>
    <p><strong>Domicilio:</strong> ${isPickup ? "Sí" : "No"}</p>
    <p><strong>Total estimado:</strong> ${formatCurrency(total)}</p>
  `;
});

/* -------------------------
   REGISTRO DE PEDIDOS
   ------------------------- */

/*
  Registra un pedido y lo almacena en localStorage.
*/
orderForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const customerName = document.getElementById("customerName").value.trim();
  const customerPhone = document.getElementById("customerPhone").value.trim();
  const customerAddress = document.getElementById("customerAddress").value.trim();
  const serviceId = orderService.value;
  const kg = Number(document.getElementById("orderKg").value);
  const notes = document.getElementById("orderNotes").value.trim();
  const isExpress = document.getElementById("orderExpress").checked;
  const isPickup = document.getElementById("orderPickup").checked;

  const selectedService = findServiceById(serviceId);
  const total = calculateTotal(serviceId, kg, isExpress, isPickup);
  const codigo = generateOrderCode();

  const newOrder = {
    codigo,
    cliente: customerName,
    telefono: customerPhone,
    direccion: customerAddress,
    servicioId: serviceId,
    servicioNombre: selectedService.nombre,
    kg,
    observaciones: notes,
    express: isExpress,
    domicilio: isPickup,
    total,
    estado: getInitialStatus(),
    fechaRegistro: new Date().toLocaleString("es-EC")
  };

  const orders = getOrders();
  orders.push(newOrder);
  saveOrders(orders);

  orderMessage.innerHTML = `
    <p><strong>Pedido guardado correctamente.</strong></p>
    <p><strong>Código:</strong> ${codigo}</p>
    <p><strong>Cliente:</strong> ${customerName}</p>
    <p><strong>Total:</strong> ${formatCurrency(total)}</p>
    <p><strong>Estado inicial:</strong> ${newOrder.estado}</p>
  `;

  orderForm.reset();
  renderOrdersTable();
});

/* -------------------------
   SEGUIMIENTO DE PEDIDOS
   ------------------------- */

/*
  Busca un pedido por código y muestra toda su información.
*/
trackingForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const code = trackingCode.value.trim().toUpperCase();
  const orders = getOrders();
  const foundOrder = orders.find(order => order.codigo.toUpperCase() === code);

  if (!foundOrder) {
    trackingResult.innerHTML = `
      <p><strong>No se encontró ningún pedido</strong> con el código ingresado.</p>
      <p>Verifica el código y vuelve a intentarlo.</p>
    `;
    return;
  }

  trackingResult.innerHTML = `
    <p><strong>Código:</strong> ${foundOrder.codigo}</p>
    <p><strong>Cliente:</strong> ${foundOrder.cliente}</p>
    <p><strong>Servicio:</strong> ${foundOrder.servicioNombre}</p>
    <p><strong>Kilogramos:</strong> ${foundOrder.kg} kg</p>
    <p><strong>Total:</strong> ${formatCurrency(foundOrder.total)}</p>
    <p><strong>Estado actual:</strong> ${foundOrder.estado}</p>
    <p><strong>Fecha de registro:</strong> ${foundOrder.fechaRegistro}</p>
  `;
});

/* -------------------------
   DATOS DE EJEMPLO
   ------------------------- */

/*
  Crea algunos pedidos de demostración.
  Esto es útil para mostrar la funcionalidad al docente.
*/
seedDataBtn.addEventListener("click", function () {
  const currentOrders = getOrders();

  if (currentOrders.length > 0) {
    alert("Ya existen pedidos almacenados. Puedes eliminarlos primero si deseas recargar los datos.");
    return;
  }

  const sampleOrders = [
    {
      codigo: "LAV-0001",
      cliente: "María López",
      telefono: "0991111111",
      direccion: "Sauces 3, Guayaquil",
      servicioId: "lavado",
      servicioNombre: "Lavado general",
      kg: 5,
      observaciones: "Separar prendas blancas",
      express: false,
      domicilio: true,
      total: calculateTotal("lavado", 5, false, true),
      estado: getRandomStatus(0),
      fechaRegistro: new Date().toLocaleString("es-EC")
    },
    {
      codigo: "LAV-0002",
      cliente: "Carlos Ponce",
      telefono: "0982222222",
      direccion: "Alborada, Guayaquil",
      servicioId: "planchado",
      servicioNombre: "Lavado + planchado",
      kg: 3,
      observaciones: "No doblar las camisas",
      express: true,
      domicilio: false,
      total: calculateTotal("planchado", 3, true, false),
      estado: getRandomStatus(1),
      fechaRegistro: new Date().toLocaleString("es-EC")
    },
    {
      codigo: "LAV-0003",
      cliente: "Andrea Vera",
      telefono: "0973333333",
      direccion: "Urdesa, Guayaquil",
      servicioId: "delicada",
      servicioNombre: "Ropa delicada",
      kg: 2,
      observaciones: "Prendas delicadas",
      express: false,
      domicilio: false,
      total: calculateTotal("delicada", 2, false, false),
      estado: getRandomStatus(2),
      fechaRegistro: new Date().toLocaleString("es-EC")
    }
  ];

  saveOrders(sampleOrders);
  renderOrdersTable();
  alert("Datos de ejemplo cargados correctamente.");
});

/*
  Limpia todos los pedidos guardados en el navegador.
*/
clearDataBtn.addEventListener("click", function () {
  localStorage.removeItem("laundry_orders");
  renderOrdersTable();
  trackingResult.textContent = "Escribe un código válido para consultar el estado del pedido.";
  orderMessage.textContent = "Aquí aparecerá la confirmación del pedido registrado.";
  alert("Los datos fueron eliminados.");
});

/* -------------------------
   INICIALIZACIÓN
   ------------------------- */

/*
  Se ejecuta al cargar la página.
*/
renderServices();
loadServiceOptions();
renderOrdersTable();
