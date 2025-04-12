<?php
include_once __DIR__ . '/conexion.php';

$conexion = obtenerConexion();
$nombre_usuario = "Invitado";

// Consulta fija para el usuario con ID 1
$stmt = $conexion->prepare("SELECT nombres FROM usuario WHERE id = 1 AND activo = 1");
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $fila = $resultado->fetch_assoc()) {
    $nombre_usuario = $fila['nombres'];
}
$stmt->close();
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Iconos Font Awesome -->
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
   
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="../home/index.php">
                    <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="Logo Unifranz">
                </a>
            </div>
            <div class="hamburger" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>


            <nav class="navbar">

                <ul class="nav-links">
                <li class="dropdown">
    <a href="../dimensiones/dimensiones.php">Dimensiones <i class="fas fa-chevron-down dropdown-icon"></i></a>
    <ul class="submenu">
        <?php
        include_once __DIR__ . '/conexion.php';
        $conexion = obtenerConexion();
        $query = "SELECT id, nombres FROM dimensiones WHERE activo = 1";
        $resultado = $conexion->query($query);

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $nombre = htmlspecialchars($fila['nombres']);
                $id = (int)$fila['id'];

                $icono = '';
                $enlace = '../dimensiones/dimensiones.php';
                
                // Asignar páginas específicas según el nombre de la dimensión
                switch (strtolower($nombre)) {
                    case 'documentación legal':
                        $icono = 'fas fa-gavel';
                        $enlace = '../categorias/doclegal.php?id='.$id;
                        break;
                    case 'documentación académica':
                        $icono = 'fas fa-graduation-cap';
                        $enlace = '../categorias/docacademica.php?id='.$id;
                        break;
                    case 'comunidad estudiantil':
                        $icono = 'fas fa-users';
                        $enlace = '../categorias/comunidad.php?id='.$id;
                        break;
                    case 'infraestructura':
                        $icono = 'fas fa-building';
                        $enlace = '../categorias/infrestructura.php?id='.$id;
                        break;
                    default:
                        $icono = 'fas fa-folder';
                        $enlace = '../dimensiones/dimensiones.php?id='.$id;
                        break;
                }

                echo "<li><a href='$enlace'><i class='$icono'></i> $nombre</a></li>";
            }
        } else {
            echo "<li><a href='#'>Sin dimensiones</a></li>";
        }
        ?>
    </ul>
</li>
                    <li class="dropdown">
                    <a href="#">Páginas <i class="fas fa-chevron-down dropdown-icon"></i></a>

                        <ul class="submenu">
                            <li><a href="https://tools.unifranz.edu.bo/">Tools Unifranz</a></li>
                            <li><a href="#">Página 2</a></li>
                            <li><a href="#">Página 3</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Buscar...">
                    <i class="fas fa-microphone mic-icon"></i>
                </div>

            </nav>
            <div class="nav-right">
                <div class="profile-container">
                    <img src="../../assets/img/fotoperfil.jpg" alt="Profile" class="profile-img">
                    <div class="status-indicator"></div>
                    <div class="user-info">
                        <span>Bienvenida</span>
                        <span class="name"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    </div>

                </div>
            </div>
        </header>
    </div>
    <script src="../../assets/js/header.js"></script>
</body>
</html>


