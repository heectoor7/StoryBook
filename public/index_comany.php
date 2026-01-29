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

            <!-- Sección empresas -->
            <section class="mb-4" id="companiesSection">
                <h4 id="companiesTitle">Empresas asociadas</h4>

                <div class="d-flex gap-3" id="companiesList">

                    <div class="card" style="width: 18rem;" id="companyCard-1">
                        <div class="card-body">
                            <h5 class="card-title" id="companyName-1">Peluquería Pepe</h5>
                            <p class="card-text" id="companyDesc-1">
                                Gestión de servicios activos
                            </p>
                        </div>
                    </div>

                    <div class="card" style="width: 18rem;" id="companyCard-2">
                        <div class="card-body">
                            <h5 class="card-title" id="companyName-2">Taller Paco</h5>
                            <p class="card-text" id="companyDesc-2">
                                Control de reservas
                            </p>
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
</body>

</html>
