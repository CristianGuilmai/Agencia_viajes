<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vuelos - Nuvia</title>
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
        
        .vuelos-table {
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
            background: linear-gradient(135deg, #3498db, #2980b9);
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
            background: linear-gradient(135deg, #27ae60, #2ecc71);
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
            color: #27ae60;
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
                <a href="mostrar_vuelos.php" class="nav-link active">Vuelos</a>
                <a href="mostrar_hoteles.php" class="nav-link">Hoteles</a>
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
    // Estadísticas por hotel
    $query = "SELECT h.nombre, h.ubicacion, COUNT(r.id_reserva) as total_reservas,
                     AVG(h.tarifa_noche) as tarifa_promedio,
                     h.habitaciones_disponibles
              FROM HOTEL h
              LEFT JOIN RESERVA r ON h.id_hotel = r.id_hotel
              GROUP BY h.id_hotel, h.nombre, h.ubicacion, h.habitaciones_disponibles
              ORDER BY total_reservas DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<h2>Estadísticas por Hotel</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background-color: #4facfe; color: white;'>
            <th style='padding: 10px;'>Hotel</th>
            <th style='padding: 10px;'>Ubicación</th>
            <th style='padding: 10px;'>Total Reservas</th>
            <th style='padding: 10px;'>Tarifa Promedio</th>
            <th style='padding: 10px;'>Habitaciones</th>
            <th style='padding: 10px;'>Ocupación %</th>
          </tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ocupacion = $row['habitaciones_disponibles'] > 0 ? 
                    ($row['total_reservas'] / $row['habitaciones_disponibles']) * 100 : 0;
        
        $color_ocupacion = $ocupacion > 50 ? '#e74c3c' : ($ocupacion > 25 ? '#f39c12' : '#27ae60');
        
        echo "<tr>
                <td style='padding: 10px;'>{$row['nombre']}</td>
                <td style='padding: 10px;'>{$row['ubicacion']}</td>
                <td style='padding: 10px; font-weight: bold;'>{$row['total_reservas']}</td>
                <td style='padding: 10px;'>$" . number_format($row['tarifa_promedio'], 2) . "</td>
                <td style='padding: 10px;'>{$row['habitaciones_disponibles']}</td>
                <td style='padding: 10px; color: $color_ocupacion; font-weight: bold;'>" . 
                number_format($ocupacion, 1) . "%</td>
              </tr>";
    }
    
    echo "</table>";
    
    // Estadísticas generales
    $query2 = "SELECT 
                (SELECT COUNT(*) FROM VUELO) as total_vuelos,
                (SELECT COUNT(*) FROM HOTEL) as total_hoteles,
                (SELECT COUNT(*) FROM RESERVA) as total_reservas,
                (SELECT AVG(precio) FROM VUELO) as precio_promedio_vuelo,
                (SELECT AVG(tarifa_noche) FROM HOTEL) as tarifa_promedio_hotel";
    
    $stmt2 = $db->prepare($query2);
    $stmt2->execute();
    $stats = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 10px; margin-top: 30px;'>";
    echo "<h3>Estadísticas Generales del Sistema</h3>";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;'>";
    
    echo "<div style='background: white; padding: 15px; border-radius: 8px; text-align: center;'>";
    echo "<h4 style='color: #4facfe;'>Total Vuelos</h4>";
    echo "<p style='font-size: 24px; font-weight: bold; color: #333;'>{$stats['total_vuelos']}</p>";
    echo "</div>";
    
    echo "<div style='background: white; padding: 15px; border-radius: 8px; text-align: center;'>";
    echo "<h4 style='color: #4facfe;'>Total Hoteles</h4>";
    echo "<p style='font-size: 24px; font-weight: bold; color: #333;'>{$stats['total_hoteles']}</p>";
    echo "</div>";
    
    echo "<div style='background: white; padding: 15px; border-radius: 8px; text-align: center;'>";
    echo "<h4 style='color: #4facfe;'>Total Reservas</h4>";
    echo "<p style='font-size: 24px; font-weight: bold; color: #333;'>{$stats['total_reservas']}</p>";
    echo "</div>";
    
    echo "<div style='background: white; padding: 15px; border-radius: 8px; text-align: center;'>";
    echo "<h4 style='color: #4facfe;'>Precio Promedio Vuelo</h4>";
    echo "<p style='font-size: 24px; font-weight: bold; color: #333;'>$" . number_format($stats['precio_promedio_vuelo'], 2) . "</p>";
    echo "</div>";
    
    echo "<div style='background: white; padding: 15px; border-radius: 8px; text-align: center;'>";
    echo "<h4 style='color: #4facfe;'>Tarifa Promedio Hotel</h4>";
    echo "<p style='font-size: 24px; font-weight: bold; color: #333;'>$" . number_format($stats['tarifa_promedio_hotel'], 2) . "</p>";
    echo "</div>";
    
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


    </div>
</body>
</html>