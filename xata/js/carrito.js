/**
 * Sistema de Carrito de Compras Simulado
 * Asociación xät'ä - Frontend
 */

// Inicializar el carrito desde localStorage al cargar la página
let carrito = [];

// Cargar carrito guardado
function cargarCarrito() {
    const carritoGuardado = localStorage.getItem('carritoXata');
    if (carritoGuardado) {
        carrito = JSON.parse(carritoGuardado);
        actualizarContadorCarrito();
    }
}

// Guardar carrito en localStorage
function guardarCarrito() {
    localStorage.setItem('carritoXata', JSON.stringify(carrito));
    actualizarContadorCarrito();
}

// Agregar producto al carrito
function agregarAlCarrito(id, nombre, precio) {
    // Verificar si el producto ya existe en el carrito
    const productoExistente = carrito.find(item => item.id === id);
    
    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1
        });
    }
    
    guardarCarrito();
    
    // Mostrar notificación
    mostrarNotificacion('Producto agregado al carrito', 'success');
}

// Eliminar producto del carrito
function eliminarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    guardarCarrito();
    mostrarCarrito();
}

// Actualizar cantidad de un producto
function actualizarCantidad(id, cantidad) {
    const producto = carrito.find(item => item.id === id);
    if (producto) {
        if (cantidad > 0) {
            producto.cantidad = cantidad;
        } else {
            eliminarDelCarrito(id);
        }
        guardarCarrito();
        mostrarCarrito();
    }
}

// Actualizar el contador del carrito
function actualizarContadorCarrito() {
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    const contadorElemento = document.getElementById('cartCount');
    if (contadorElemento) {
        contadorElemento.textContent = totalItems;
    }
}

// Calcular el total del carrito
function calcularTotal() {
    return carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
}

// Mostrar el modal del carrito
function mostrarCarrito() {
    let html = `
        <div class="modal fade show" id="carritoModal" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
                        </h5>
                        <button type="button" class="btn-close btn-close-white" onclick="cerrarCarrito()"></button>
                    </div>
                    <div class="modal-body">
    `;
    
    if (carrito.length === 0) {
        html += `
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Tu carrito está vacío</h4>
                <p class="text-muted">Agrega productos desde nuestro catálogo</p>
            </div>
        `;
    } else {
        html += '<div class="table-responsive"><table class="table table-hover">';
        html += '<thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Acciones</th></tr></thead>';
        html += '<tbody>';
        
        carrito.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            html += `
                <tr>
                    <td><strong>${item.nombre}</strong></td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td>
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm text-center" 
                                   value="${item.cantidad}" 
                                   min="1" 
                                   onchange="actualizarCantidad(${item.id}, parseInt(this.value))">
                            <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td><strong>$${subtotal.toFixed(2)}</strong></td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        const total = calcularTotal();
        html += `
            <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Total:</h4>
                    <h3 class="text-success mb-0">$${total.toFixed(2)}</h3>
                </div>
            </div>
        `;
    }
    
    html += `
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarCarrito()">
                            Cerrar
                        </button>
                        ${carrito.length > 0 ? `
                            <button type="button" class="btn btn-danger" onclick="vaciarCarrito()">
                                <i class="fas fa-trash"></i> Vaciar Carrito
                            </button>
                            <button type="button" class="btn btn-success" onclick="finalizarCompra()">
                                <i class="fas fa-check"></i> Finalizar Compra (Simulado)
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar el modal al body
    const modalExistente = document.getElementById('carritoModal');
    if (modalExistente) {
        modalExistente.remove();
    }
    document.body.insertAdjacentHTML('beforeend', html);
}

// Cerrar el modal del carrito
function cerrarCarrito() {
    const modal = document.getElementById('carritoModal');
    if (modal) {
        modal.remove();
    }
}

// Vaciar el carrito
function vaciarCarrito() {
    if (confirm('¿Estás seguro de vaciar el carrito?')) {
        carrito = [];
        guardarCarrito();
        mostrarCarrito();
        mostrarNotificacion('Carrito vaciado', 'info');
    }
}

// Finalizar compra (simulado)
function finalizarCompra() {
    const total = calcularTotal();
    alert(`Compra finalizada (SIMULACIÓN)
    
Total de productos: ${carrito.length}
Total a pagar: $${total.toFixed(2)}

¡Gracias por su compra!

Nota: Este es un carrito simulado. No se procesa ningún pago real.`);
    
    carrito = [];
    guardarCarrito();
    cerrarCarrito();
    mostrarNotificacion('¡Compra finalizada exitosamente! (Simulación)', 'success');
}

// Mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    const colores = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colores[tipo]};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

// Agregar estilos para las animaciones
const estilos = document.createElement('style');
estilos.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(estilos);

// Cargar el carrito al iniciar
document.addEventListener('DOMContentLoaded', cargarCarrito);