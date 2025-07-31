<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoteles - Nuvia</title>
    <style>
        /* CSS para el Header Sticky */
        .header {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .nav-links {
            display: flex;
            gap: 30px;
        }
        .nav-link {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #3498db;
        }
        .nav-link.active {
            color: #3498db;
            font-weight: 600;
        }
        
        /* Estilos para el contenido */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2rem;
            text-align: center;
        }
        
        .hoteles-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .add-button {
            display: inline-block;
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .add-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .precio {
            font-weight: 600;
            color: #e67e22;
        }
        
        .hotel-nombre {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .ubicacion {
            color: #7f8c8d;
            font-style: italic;
        }
        
        .habitaciones {
            text-align: center;
            font-weight: 500;
        }
        
        .error {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        /* Responsive para el header */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                height: auto;
                padding: 20px;
            }
            .nav-links {
                margin-top: 20px;
                gap: 20px;
            }
            
            .container {
                padding: 20px 10px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Sticky -->
    <div class="header">
        <div class="header-content">
            <a href="index.html" class="logo">
                <div class="logo-icon">✈️</div>
                <span>Nuvia</span>
            </a>
            <nav class="nav-links">
                <a href="mostrar_vuelos.php" class="nav-link">Vuelos</a>
                <a href="mostrar_hoteles.php" class="nav-link active">Hoteles</a>
                <a href="mostrar_reservas.php" class="nav-link">Mis Reservas</a>
                <a href="estadisticas.php" class="nav-link">Estadísticas</a>
            </nav>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <?php
        require_once 'conexion.php';

        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "SELECT * FROM HOTEL ORDER BY nombre";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            echo "<h2>Lista de Hoteles</h2>";
            echo "<div class='hoteles-table'>";
            echo "<table>";
            echo "<tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Habitaciones</th>
                    <th>Tarifa/Noche</th>
                  </tr>";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['id_hotel']}</td>
                        <td class='hotel-nombre'>{$row['nombre']}</td>
                        <td class='ubicacion'>{$row['ubicacion']}</td>
                        <td class='habitaciones'>{$row['habitaciones_disponibles']}</td>
                        <td class='precio'>$" . number_format($row['tarifa_noche'], 2) . "</td>
                      </tr>";
            }
            
            echo "</table>";
            echo "</div>";
            echo "<p><a href='form_hotel.html' class='add-button'>🏨 Agregar nuevo hotel</a></p>";
            
        } catch (PDOException $e) {
            echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
        }
        ?>
    </div>
</body>
</html>