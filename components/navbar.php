<?php

// 1. GET_NAVBAR COMPONENT
function get_navbar($categoria, $titulo, $active) {

    $output = "";

    $output .= "<div class='header-body pt-3'>
                    <div class='row'>
                        <div class='col'>
                            <nav aria-label='breadcrumb'>
                                <ol class='breadcrumb align-items-center bg-light shadow-sm bg-white'>
                                    <div class='col-sm-12 col-md-6 col-lg-7'>
                                        <li class='mr-3 d-inline'>
                                            <button type='button' class='btn btn-sm btn-main sidebarCollapse'>
                                                <i class='fas fa-bars'></i>
                                            </button>
                                        </li>
                                        <li class='breadcrumb-item d-inline font-weight-bold'>$categoria</li>
                                        <li class='breadcrumb-item d-inline active' aria-current='page'>$titulo</li>
                                    </div>";

    if($active === true) {

        $output .= "<div class='navbar-breakpoint col-sm-12 col-md-6 col-lg-5 '>
                        <div class='input-group searchInput'>
                            <div class='input-group-prepend'>
                            <div class='input-group-text'><i class='fas fa-search'></i></div>
                            </div>
                            <input type='text' id='searchInput' class='form-control form-control' placeholder='Buscar'>
                        </div>
                    </div>";

    }

    $output .= "</ol>
            </nav>
            </div>
            </div> 
            </div>";

    echo $output;
                
}