<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoryBook</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <img id="sidebarLogo" src="assets/img/storybookLOGO.png" alt="Logo" style="width: 200px;">
        <h3 id="sidebarTitle">Empresa</h3>
        <ul id="sidebarMenu">
            <li id="menuCompanyData">Datos de la empresa</li>
            <li id="menuManageServices">Gestión de servicios</li>
            <li id="menuSchedules">Horarios</li>
            <li id="menuBookings">Reservas recibidas</li>
            <li id="menuSettings">Configuración</li>
        </ul>
    </aside>

    <!-- Main -->
    <div class="main" id="mainContainer">

        <!-- Header -->
        <header class="header d-flex justify-content-between align-items-center p-3" id="mainHeader">
            <h3 class="m-0" id="appTitle">StoryBook</h3>

            <div class="d-flex align-items-center" id="headerActions">
                <input 
                    type="text"
                    class="form-control me-2"
                    id="manageServicesInput"
                    placeholder="Gestionar servicios"
                >
                <button class="btn btn-outline-primary me-2" id="profileButton">
                    Mi perfil
                </button>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="content p-3" id="mainContent">

        <!-- Recordatorio de Servicios Asociados a Clientes -->
        <section class="mb-4 p-4 bg-white rounded shadow-sm" id="servicesReminderSection">
            <h4 class="mb-3" id="reminderTitle">🎯 Servicios ya asociados a clientes</h4>

            <div class="alert alert-info border-start border-primary border-4 mb-3" role="alert">
                <strong>📌 Nota:</strong> Aquí puedes ver los servicios que ya están siendo utilizados por tus clientes. Desliza hacia la derecha para ver más servicios. Adjunta fotos de alta calidad para que los clientes vean tu trabajo.
            </div>

            <!-- Galería horizontal -->
            <div class="overflow-auto" id="servicesGallery" style="height: 50vh; background: #f9f9f9; border-radius: 8px; border: 1px solid #dee2e6;">
                <div class="d-flex gap-3 p-4" style="width: fit-content;">
                    <div class="d-flex align-items-center justify-content-center" style="min-width: 100%; text-align: center; color: #999;">
                        <div>
                            <div style="font-size: 3rem; opacity: 0.5;">📸</div>
                            <p class="mb-0"><strong>Cargando servicios...</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

            <!-- Mis servicios -->
            <section id="myServicesSection">
                <h4 id="myServicesTitle">Mis servicios</h4>

                <div class="list-group" id="myServicesList">
                    <div class="list-group-item" id="serviceItem-1">Corte de pelo</div>
                    <div class="list-group-item" id="serviceItem-2">Revisión mecánica</div>
                    <div class="list-group-item" id="serviceItem-3">Lavado y peinado</div>
                </div>
            </section>

        </main>

    </div>

    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/app.js"></script>
    <script>
        // Objeto para almacenar servicios
        const servicesData = {
            associatedServices: [],
            companyId: 1 // Empresa 1 por defecto
        };

        // Inicializar la página
        document.addEventListener('DOMContentLoaded', async function() {
            await loadAssociatedServices();
        });

        // Cargar servicios asociados a clientes desde API
        async function loadAssociatedServices() {
            try {
                console.log('Cargando servicios para empresa:', servicesData.companyId);
                const response = await fetch(`/api/services?company_id=${servicesData.companyId}`);
                console.log('Response status:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Datos recibidos:', data);
                    servicesData.associatedServices = Array.isArray(data) ? data : (data.data || []);
                    console.log('Servicios a renderizar:', servicesData.associatedServices);
                } else {
                    console.error('Error en respuesta:', response.status, response.statusText);
                    const errorData = await response.text();
                    console.error('Respuesta de error:', errorData);
                }
            } catch (error) {
                console.error('Error al cargar servicios:', error);
            }
            renderAssociatedServices();
        }

        // Renderizar servicios asociados (galería horizontal)
        function renderAssociatedServices() {
            const gallery = document.getElementById('servicesGallery');
            
            if (servicesData.associatedServices.length === 0) {
                gallery.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center" style="height: 100%; text-align: center; color: #999;">
                        <div>
                            <div style="font-size: 3rem; opacity: 0.5;">📸</div>
                            <p class="mb-0"><strong>Sin servicios asociados aún</strong></p>
                            <p style="font-size: 0.9rem; color: #ccc; margin-top: 5px;">Los servicios aparecerán aquí</p>
                        </div>
                    </div>
                `;
                return;
            }

            gallery.innerHTML = `<div class="d-flex gap-3 p-4" style="width: fit-content;">` + servicesData.associatedServices.map(service => `
                <div class="card" style="flex: 0 0 320px; cursor: pointer; transition: all 0.3s ease;" data-service-id="${service.id}" onclick="editService(${service.id})" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 100%; height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; ${service.image ? `background-image: url('${service.image}'); background-size: cover; background-position: center;` : ''}">
                        ${!service.image ? '📸' : ''}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${service.name || 'Sin nombre'}</h5>
                        <p class="card-text" style="font-size: 0.85rem; color: #666; margin: 0;">
                            <span>🏷️ ${service.category || 'General'}</span><br>
                            <span>💰 $${service.price || '0'}</span><br>
                            <span>⏱️ ${service.duration || 'N/A'}</span><br>
                            <span>👥 ${service.clients || '0'} clientes</span>
                        </p>
                    </div>
                </div>
            `).join('') + `</div>`;
        }

        // Editar servicio
        function editService(serviceId) {
            window.location.href = `/service/${serviceId}/edit`;
        }
    </script>
