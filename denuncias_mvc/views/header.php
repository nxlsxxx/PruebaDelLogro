<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNL Denuncias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-white bg-white sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Toggle button for sidebar - only visible on small screens -->
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Breadcrumbs / Page Title -->
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Escritorio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Denuncias</li>
                </ol>
            </nav>

            <!-- Right side icons/text -->
            <div class="d-flex align-items-center ms-auto">
                <span class="navbar-text d-none d-lg-block me-3">
                    <i class="bi bi-person-fill"></i> <?php echo $_SESSION['user_name'] ?? 'Guest'; ?> (<?php echo $_SESSION['user_rol'] ?? 'N/A'; ?>)
                </span>
                <ul class="navbar-nav flex-row">
                    <li class="nav-item text-nowrap">
                        <a class="nav-link px-2" href="index.php?controller=User&action=logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
