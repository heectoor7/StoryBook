/**
 * company-bookings.js
 * Funciones para gestión de reservas (agenda)
 */

// Cargar reservas recibidas
async function loadCompanyBookings() {
    const token = localStorage.getItem('auth_token');
    const bookingsContainer = document.getElementById('companyBookingsContainer');
    
    if (!bookingsContainer) return;
    
    bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">Cargando reservas...</p>';
    
    if (!token) {
        bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">No autenticado</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/bookings', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar reservas');
        }

        const bookings = await res.json();
        
        if (!Array.isArray(bookings) || bookings.length === 0) {
            bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">No hay reservas aún</p>';
            return;
        }

        // Agrupar reservas por estado
        const pending = bookings.filter(b => b.status === 'PENDING');
        const confirmed = bookings.filter(b => b.status === 'CONFIRMED');
        const cancelled = bookings.filter(b => b.status === 'CANCELLED');

        bookingsContainer.innerHTML = `
            <div class="row">
                <!-- Pendientes -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-warning">⏳ Pendientes (${pending.length})</h5>
                    <div class="bookings-list">
                        ${pending.length === 0 ? '<p class="text-muted">Sin reservas</p>' : pending.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Confirmadas -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-success">✓ Confirmadas (${confirmed.length})</h5>
                    <div class="bookings-list">
                        ${confirmed.length === 0 ? '<p class="text-muted">Sin reservas</p>' : confirmed.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Canceladas -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-danger">✗ Canceladas (${cancelled.length})</h5>
                    <div class="bookings-list">
                        ${cancelled.length === 0 ? '<p class="text-muted">Sin reservas</p>' : cancelled.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[Bookings] Error al cargar:', err);
        bookingsContainer.innerHTML = '<p class="text-danger">Error al cargar reservas</p>';
    }
}

// Renderizar tarjeta de reserva
function renderBookingCard(booking) {
    const statusColors = {
        'PENDING': 'warning',
        'CONFIRMED': 'success',
        'CANCELLED': 'danger'
    };
    
    const statusColor = statusColors[booking.status] || 'secondary';
    
    return `
        <div class="card mb-2">
            <div class="card-body p-2">
                <div><strong>${booking.user_name || 'Usuario'}</strong></div>
                <div><small>${booking.service_name || 'Servicio'}</small></div>
                <div><small class="text-muted">${booking.date} ${booking.time ? booking.time.substring(0, 5) : ''}</small></div>
                ${booking.notes ? `<div><small class="text-muted">Nota: ${booking.notes}</small></div>` : ''}
                <div class="mt-2">
                    ${booking.status === 'PENDING' ? `
                        <button class="btn btn-sm btn-success me-1" onclick="updateBookingStatus(${booking.id}, 'CONFIRMED')">
                            Confirmar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="updateBookingStatus(${booking.id}, 'CANCELLED')">
                            Cancelar
                        </button>
                    ` : booking.status === 'CONFIRMED' ? `
                        <button class="btn btn-sm btn-warning" onclick="updateBookingStatus(${booking.id}, 'CANCELLED')">
                            Cancelar
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Actualizar estado de reserva
async function updateBookingStatus(bookingId, newStatus) {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('No autenticado');
        return;
    }

    try {
        const res = await fetch(`/api/company/bookings/${bookingId}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!res.ok) {
            throw new Error('Error al actualizar reserva');
        }

        await loadCompanyBookings();
        
        const statusMessages = {
            'CONFIRMED': 'Reserva confirmada',
            'CANCELLED': 'Reserva cancelada'
        };
        
        alert(statusMessages[newStatus] || 'Reserva actualizada exitosamente');
    } catch (err) {
        console.error('[Bookings] Error al actualizar estado:', err);
        alert('Error al actualizar reserva');
    }
}
