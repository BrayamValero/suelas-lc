<!-- Navbar -->
<nav id="sidebar">

    <!-- Sidebar Marca -->
    <div class="sidebar-brand">
        <div class="row">
            <div class="col ml-3">
                <p class="text-brand m-0">Suelas LC</p>
            </div>
            <div class="col text-right mr-3">
                <a href="javascript:void(0)" class="sidebarCollapse">
                    <i class="fas fa-times text-dark" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Sidebar Marca -->

    <!-- Sidebar Usuario -->
    <div class="sidebar-user">
        <div class="row mx-auto">
            <div class="col-4">

                <?php

                $rol = $_SESSION['ROL'];
                $nombre = $_SESSION['NOMBRE'];
                $rolFormateado = mb_strtolower($rol);

                echo "<img class='sidebar-img' src='imagenes/imagen-perfil/{$rolFormateado}.png' alt='{$rolFormateado}'>";

                ?>

            </div>
            <div class="col-8 pl-3">
                <span class="sidebar-user-name"><?php echo ucwords(mb_strtolower($nombre)) ?></span>
                <span class="sidebar-user-role"><?php echo ucwords(mb_strtolower($rol)) ?></span>
                <span class="sidebar-user-status"><i class="fa fa-circle sidebar-user-status-circle "></i> Online</span>
            </div>
        </div>
    </div>
    <!-- Sidebar Usuario -->

    <!-- Sidebar Menu  -->
    <div class="sidebar-menu">

        <ul>

            <!-- Inicio -->
            <li class="sidebar-header-menu">
                <span>Inicio</span>
            </li>

            <li class='sidebar-dropdown'>
                <a href='index.php'>
                    <i class="fas fa-bars"></i>
                    <span class='sidebar-menu-text'>Panel Principal</span>
                </a>
            </li>

            <!-- Producción -->
            <?php

            if (in_array($rol, ['ADMINISTRADOR', 'PRODUCCION', 'CONTROL'])) :
            ?>

                <li class="sidebar-header-menu">
                    <span>Producción</span>
                </li>

                <?php
                if (in_array($rol, ['ADMINISTRADOR', 'PRODUCCION'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='tablero-general.php'>
                                <i class='fas fa-columns'></i>
                                <span class='sidebar-menu-text'>Tablero General</span>
                            </a>
                        </li>";
                }
                ?>

                <?php
                if (in_array($rol, ['ADMINISTRADOR', 'PRODUCCION', 'CONTROL'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='control-de-calidad.php'>
                                <i class='far fa-calendar-check'></i>
                                <span class='sidebar-menu-text'>Control de Calidad</span>
                            </a>
                        </li>";
                }
                ?>

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'MOLINERO', 'OPERARIO', 'PRODUCCION']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='reporte-de-produccion.php'>
                //             <i class='fas fa-book'></i>
                //             <span class='sidebar-menu-text'>Reporte de Producción</span>
                //         </a>
                //     </li>";
                // }
                ?>

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'PRODUCCION']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='operarios.php'>
                //             <i class='fas fa-address-card'></i>
                //             <span class='sidebar-menu-text'>Operarios</span>
                //         </a>
                //     </li>";
                // }
                ?>

            <?php
            endif;
            ?>
            <!-- Fin de Producción -->

            <!-- Ventas -->
            <?php
            if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'CLIENTE', 'PRODUCCION'])) :
            ?>

                <li class="sidebar-header-menu">
                    <span>Ventas</span>
                </li>

                <?php

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'CLIENTE'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='añadir-pedido.php'>
                                <i class='fas fa-shopping-cart'></i>
                                <span class='sidebar-menu-text'>Añadir Pedido</span>
                                <span class='badge badge-danger ml-1'>Popular</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['CLIENTE'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='mis-pedidos.php'>
                                <i class='fas fa-store'></i>
                                <span class='sidebar-menu-text'>Mis Pedidos</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'CLIENTE'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='imprimir-pedidos-pendientes.php'>
                                <i class='fas fa-print'></i>
                                <span class='sidebar-menu-text'>Imprimir Pedidos</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'PRODUCCION'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='pedidos-pendientes.php'>
                                <i class='fas fa-store'></i>
                                <span class='sidebar-menu-text'>Pedidos Pendientes</span>
                             
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='busqueda-avanzada.php'>
                                <i class='fas fa-search'></i>
                                <span class='sidebar-menu-text'>Busqueda Avanzada</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='despachos-parciales.php'>
                                <i class='fas fa-truck'></i>
                                <span class='sidebar-menu-text'>Despachos Parciales</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'DESPACHO'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='ventas-culminadas.php'>
                                <i class='fa fa-chart-line'></i>
                                <span class='sidebar-menu-text'>Ventas Culminadas</span>
                            </a>
                        </li>";
                }

                if (in_array($rol, ['ADMINISTRADOR', 'VENTAS'])) {
                    echo "<li class='sidebar-dropdown'>
                            <a href='clientes.php'>
                                <i class='fa fa-user-tie'></i>
                                <span class='sidebar-menu-text'>Clientes</span>
                            </a>
                        </li>";
                }

                ?>

            <?php
            endif;
            ?>
            <!-- Fin de Ventas -->

            <!-- Auditorias -->
            <?php
            if (in_array($rol, ['ADMINISTRADOR'])) :
            ?>

                <li class="sidebar-header-menu">
                    <span>Auditorias</span>
                </li>

                <?php
                if (in_array($rol, ['ADMINISTRADOR'])) {

                    echo "<li class='sidebar-dropdown'>
                            <a href='auditoria-control.php'>
                                <i class='fas fa-clipboard-check'></i>
                                <span class='sidebar-menu-text'>Auditoria de Control</span>
                            </a>
                        </li>";

                    // echo "<li class='sidebar-dropdown'>
                    //     <a href='pedidos-norsaplast.php'>
                    //         <i class='fas fa-clipboard-check'></i>
                    //         <span class='sidebar-menu-text'>Pedidos a Norsaplast</span>
                    //     </a>
                    // </li>";

                    // echo "<li class='sidebar-dropdown'>
                    //     <a href='pedidos-norsaplast.php'>
                    //         <i class='fas fa-clipboard-check'></i>
                    //         <span class='sidebar-menu-text'>Pedidos a Norsaplast</span>
                    //     </a>
                    // </li>";

                    // echo "<li class='sidebar-dropdown'>
                    //     <a href='pedidos-produccion.php'>
                    //         <i class='fas fa-clipboard-check'></i>
                    //         <span class='sidebar-menu-text'>Pedidos a Producción</span>
                    //     </a>
                    // </li>";

                    // echo "<li class='sidebar-dropdown'>
                    //     <a href='norsaplast-inventario.php'>
                    //         <i class='fas fa-clipboard-check'></i>
                    //         <span class='sidebar-menu-text'>Norsaplast a Inventario</span>
                    //     </a>
                    // </li>";

                }
                ?>

            <?php
            endif;
            ?>

            <!-- Inventario -->
            <?php
            if (in_array($rol, ['ADMINISTRADOR', 'MOLINERO'])) :
            ?>

                <li class="sidebar-header-menu">
                    <span>Inventario</span>
                </li>

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'PRODUCCION', 'MOLINERO']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='materia-prima.php'>
                //             <i class='fab fa-react'></i>
                //             <span class='sidebar-menu-text'>Insumos y Materia Prima</span>
                //         </a>
                //     </li>";
                // }
                ?>

                <?php
                if (in_array($rol, ['ADMINISTRADOR', 'DESPACHO'])) {
                    echo "<li class='sidebar-dropdown'>
                        <a href='suelas-en-stock.php'>
                            <i class='fas fa-boxes'></i>
                            <span class='sidebar-menu-text'>Suelas en Stock</span>
                        </a>
                    </li>";
                }
                ?>

                <?php
                if (in_array($rol, ['ADMINISTRADOR'])) {
                    echo "<li class='sidebar-dropdown'>
                        <a href='referencias.php'>
                            <i class='fab fa-slack-hash'></i>
                            <span class='sidebar-menu-text'>Referencias</span>
                        </a>
                    </li>";
                }
                ?>

                <?php
                if (in_array($rol, ['ADMINISTRADOR'])) {
                    echo "<li class='sidebar-dropdown'>
                        <a href='series.php'>
                            <i class='fas fa-sort-amount-up-alt'></i>
                            <span class='sidebar-menu-text'>Series</span>
                        </a>
                    </li>";
                }
                ?>

                <?php
                if (in_array($rol, ['ADMINISTRADOR'])) {
                    echo "<li class='sidebar-dropdown'>
                        <a href='color.php'>
                            <i class='fas fa-star'></i>
                            <span class='sidebar-menu-text'>Color</span>
                        </a>
                    </li>";
                }
                ?>

            <?php
            endif;
            ?>
            <!-- Fin de Inventario -->

            <!-- Norsaplast -->
            <?php
            if (in_array($rol, ['ADMINISTRADOR', 'NORSAPLAST'])) :
            ?>

                <!-- <li class="sidebar-header-menu">
               <span>Norsaplast</span>
            </li> -->

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'NORSAPLAST']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='solicitud-material.php'>
                //             <i class='fas fa-sticky-note'></i>
                //             <span class='sidebar-menu-text'>Solicitud de Material</span>
                //         </a>
                //     </li>";
                // }
                ?>

            <?php
            endif;
            ?>
            <!-- Fin de Norsaplast -->

            <!-- Molinero -->
            <?php
            if (in_array($rol, ['ADMINISTRADOR', 'MOLINERO', 'OPERARIO'])) :
            ?>

                <!-- <li class="sidebar-header-menu">
                <span>Molino</span>
            </li> -->

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'MOLINERO']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='formulas.php'>
                //             <i class='fas fa-flask'></i>
                //             <span class='sidebar-menu-text'>Formulas</span>
                //         </a>
                //     </li>";
                // }
                ?>

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'MOLINERO', 'OPERARIO']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='entrega-de-material.php'>
                //             <i class='fas fa-truck-loading'></i>
                //             <span class='sidebar-menu-text'>Entrega de Material</span>
                //         </a>
                //     </li>";
                // }
                ?>

                <?php
                // if( in_array($rol, ['ADMINISTRADOR', 'MOLINERO']) ) {
                //     echo "<li class='sidebar-dropdown'>
                //         <a href='auditoria-de-entrega.php'>
                //             <i class='fas fa-dolly-flatbed'></i>
                //             <span class='sidebar-menu-text'>Auditoría de Entrega</span>
                //         </a>
                //     </li>";
                // }
                ?>

            <?php
            endif;
            ?>
            <!-- Fin de Molinero -->

        </ul>
    </div>
    <!-- Sidebar Menu  -->

    <!-- Sidebar Footer -->
    <div class="sidebar-footer bg-light">
        <div class="row text-center">
            <?php
            if (in_array($rol, ['ADMINISTRADOR'])) {
                echo "<div class='col'>
                        <div>
                            <a href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fa fa-cog'></i>
                                <span class='badge-sonar'></span>
                            </a>
                            <div class='dropdown-menu' aria-labelledby='dropdownMenuMessage'>
                                <a class='dropdown-item' href='maquinaria.php'>
                                    <i class='fas fa-industry dropdown-hovered'></i>
                                    <span class='sidebar-menu-text dropdown-hovered'>Maquinaria</span>
                                </a>
                                <a class='dropdown-item' href='usuarios.php'>
                                    <i class='fas fa-users dropdown-hovered'></i>
                                    <span class='sidebar-menu-text dropdown-hovered'>Usuarios</span>
                                </a>
                                <a class='dropdown-item' href='modificaciones.php'>
                                    <i class='fas fa-clipboard-list dropdown-hovered'></i>
                                    <span class='sidebar-menu-text dropdown-hovered'>Modificaciones</span>
                                </a>
                            </div>
                        </div>
                    </div>";
            }
            ?>

            <div class="col">
                <a href="backend/api/usuarios/login.php?action=unlogin">
                    <i class="fa fa-power-off"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Sidebar Footer -->

</nav>