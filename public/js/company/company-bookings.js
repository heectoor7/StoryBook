/**
 * company-bookings.js
 * Funciones para gestión de reservas (agenda)
 */

// Cargar reservas recibidas
async function loadCompanyBookings() {
    const token = localStorage.getItem('auth_token');
    const bookingsContainer = document.getElementById('companyBookingsContainer');
    
    if (!bookingsContainer) return;
    
    bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">Loading bookings...</p>';
    
    if (!token) {
        bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">Not authenticated</p>';
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
            throw new Error('Error loading bookings');
        }

        const bookings = await res.json();
        
        if (!Array.isArray(bookings) || bookings.length === 0) {
            bookingsContainer.innerHTML = '<p style="color: var(--text-secondary);">No bookings yet</p>';
            return;
        }

        // Agrupar reservas por estado
        const pending = bookings.filter(b => b.status === 'PENDING');
        const confirmed = bookings.filter(b => b.status === 'CONFIRMED');
        const cancelled = bookings.filter(b => b.status === 'CANCELLED');

        bookingsContainer.innerHTML = `
            <div class="row">
                <!-- Pending -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-warning">⏳ Pending (${pending.length})</h5>
                    <div class="bookings-list">
                        ${pending.length === 0 ? '<p class="text-muted">No bookings</p>' : pending.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Confirmed -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-success">✓ Confirmed (${confirmed.length})</h5>
                    <div class="bookings-list">
                        ${confirmed.length === 0 ? '<p class="text-muted">No bookings</p>' : confirmed.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
                
                <!-- Cancelled -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-danger">✗ Cancelled (${cancelled.length})</h5>
                    <div class="bookings-list">
                        ${cancelled.length === 0 ? '<p class="text-muted">No bookings</p>' : cancelled.map(b => renderBookingCard(b)).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error('[Bookings] Error loading:', err);
        bookingsContainer.innerHTML = '<p class="text-danger">Error loading bookings</p>';
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
                <div><strong>${booking.user_name || 'User'}</strong></div>
                <div><small>${booking.service_name || 'Service'}</small></div>
                <div><small class="text-muted">${booking.date} ${booking.time ? booking.time.substring(0, 5) : ''}</small></div>
                ${booking.notes ? `<div><small class="text-muted">Note: ${booking.notes}</small></div>` : ''}
                <div class="mt-2">
                    ${booking.status === 'PENDING' ? `
                        <button class="btn btn-sm btn-success me-1" onclick="updateBookingStatus(${booking.id}, 'CONFIRMED')">
                            Confirm
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="updateBookingStatus(${booking.id}, 'CANCELLED')">
                            Cancel
                        </button>
                    ` : booking.status === 'CONFIRMED' ? `
                        <button class="btn btn-sm btn-warning" onclick="updateBookingStatus(${booking.id}, 'CANCELLED')">
                            Cancel
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
        alert('Not authenticated');
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
            throw new Error('Error updating booking');
        }

        await loadCompanyBookings();
        
        const statusMessages = {
            'CONFIRMED': 'Booking confirmed',
            'CANCELLED': 'Booking cancelled'
        };
        
        alert(statusMessages[newStatus] || 'Booking updated successfully');
    } catch (err) {
        console.error('[Bookings] Error updating status:', err);
        alert('Error updating booking');
    }
}
