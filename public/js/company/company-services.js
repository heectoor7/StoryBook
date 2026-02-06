/**
 * company-services.js
 * Funciones para gestión de servicios
 */

// Variable global para edición de servicio
let editingServiceId = null;

// Cargar servicios de la empresa
async function loadCompanyServices() {
    const token = localStorage.getItem('auth_token');
    const servicesContainer = document.getElementById('companyServicesContainer');
    
    if (!servicesContainer) return;
    
    servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Loading services...</p>';
    
    if (!token) {
        servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">Not authenticated</p>';
        return;
    }

    try {
        const res = await fetch('/api/company/services', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar servicios');
        }

        const services = await res.json();
        
        if (!Array.isArray(services) || services.length === 0) {
            servicesContainer.innerHTML = '<p style="color: var(--text-secondary);">No services yet. <button class="btn btn-sm btn-primary" onclick="openNewServiceModal()">Create service</button></p>';
            return;
        }

        servicesContainer.innerHTML = services.map(service => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    ${service.image ? `<img src="${service.image}" class="card-img-top" alt="${service.name}" style="max-height:180px;object-fit:cover;">` : ''}
                    <div class="card-body">
                        <h5 class="card-title">${service.name}</h5>
                        <p class="card-text text-truncate">${service.description || ''}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small style="color: var(--text-secondary);">${service.category || 'No category'}</small>
                            <strong>$${parseFloat(service.price || 0).toFixed(2)}</strong>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="editService(${service.id})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteService(${service.id})">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('[Services] Error al cargar:', err);
        servicesContainer.innerHTML = '<p class="text-danger">Error loading services</p>';
    }
}

// Abrir modal para nuevo servicio
function openNewServiceModal() {
    editingServiceId = null;
    document.getElementById('serviceModalTitle').textContent = 'Create new service';
    document.getElementById('serviceName').value = '';
    document.getElementById('serviceDescription').value = '';
    document.getElementById('servicePrice').value = '';
    document.getElementById('serviceCategory').value = '';
    document.getElementById('serviceImage').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}

// Editar servicio existente
async function editService(serviceId) {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('Not authenticated');
        return;
    }

    try {
        const res = await fetch(`/api/company/services/${serviceId}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error loading service');
        }

        const service = await res.json();
        
        editingServiceId = serviceId;
        document.getElementById('serviceModalTitle').textContent = 'Edit service';
        document.getElementById('serviceName').value = service.name || '';
        document.getElementById('serviceDescription').value = service.description || '';
        document.getElementById('servicePrice').value = service.price || '';
        document.getElementById('serviceCategory').value = service.category || '';
        
        const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
        modal.show();
    } catch (err) {
        console.error('[Services] Error al cargar servicio:', err);
        alert('Error loading service');
    }
}

// Guardar servicio (crear o actualizar)
async function saveService() {
    const name = document.getElementById('serviceName').value.trim();
    const description = document.getElementById('serviceDescription').value.trim();
    const price = document.getElementById('servicePrice').value.trim();
    const category = document.getElementById('serviceCategory').value.trim();
    const imageInput = document.getElementById('serviceImage');
    const token = localStorage.getItem('auth_token');

    if (!name || !price) {
        alert('Name and price are required');
        return;
    }

    if (!token) {
        alert('Not authenticated');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('price', price);
    formData.append('category', category);
    
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    try {
        let res;
        if (editingServiceId) {
            // Actualizar servicio existente
            formData.append('_method', 'PUT');
            res = await fetch(`/api/company/services/${editingServiceId}`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });
        } else {
            // Crear nuevo servicio
            res = await fetch('/api/company/services', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });
        }

        if (!res.ok) {
            throw new Error('Error al guardar servicio');
        }

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('serviceModal'));
        if (modal) modal.hide();

        // Recargar servicios
        await loadCompanyServices();
        
        alert(editingServiceId ? 'Service updated successfully' : 'Service created successfully');
        editingServiceId = null;
    } catch (err) {
        console.error('[Services] Error al guardar:', err);
        alert('Error saving service');
    }
}

// Eliminar servicio
async function deleteService(serviceId) {
    if (!confirm('Are you sure you want to delete this service?')) {
        return;
    }

    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        alert('Not authenticated');
        return;
    }

    try {
        const res = await fetch(`/api/company/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error deleting service');
        }

        await loadCompanyServices();
        alert('Service deleted successfully');
    } catch (err) {
        console.error('[Services] Error al eliminar:', err);
        alert('Error deleting service');
    }
}
