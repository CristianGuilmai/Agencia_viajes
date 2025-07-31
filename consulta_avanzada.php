<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT h.id_hotel, h.nombre, h.ubicacion, h.habitaciones_disponibles, 
                     h.tarifa_noche, COUNT(r.id_reserva) as total_reservas
              FROM HOTEL h
              INNER JOIN RESERVA r ON h.id_hotel = r.id_hotel
              GROUP BY h.id_hotel, h.nombre, h.ubicacion, h.habitaciones_disponibles, h.tarifa_noche
              HAVING COUNT(r.id_reserva) > 2
              ORDER BY total_reservas DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<h2>Hoteles con más de 2 reservas</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th style='padding: 10px;'>ID Hotel</th>
            <th style='padding: 10px;'>Nombre</th>
            <th style='padding: 10px;'>Ubicación</th>
            <th style='padding: 10px;'>Habitaciones</th>
            <th style='padding: 10px;'>Tarifa/Noche</th>
            <th style='padding: 10px;'>Total Reservas</th>
          </tr>";
    
    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        echo "<tr>
                <td style='padding: 10px;'>{$row['id_hotel']}</td>
                <td style='padding: 10px;'>{$row['nombre']}</td>
                <td style='padding: 10px;'>{$row['ubicacion']}</td>
                <td style='padding: 10px;'>{$row['habitaciones_disponibles']}</td>
                <td style='padding: 10px;'>$" . number_format($row['tarifa_noche'], 2) . "</td>
                <td style='padding: 10px; font-weight: bold; color: #e74c3c;'>{$row['total_reservas']}</td>
              </tr>";
    }
    
    if ($count == 0) {
        echo "<tr><td colspan='6' style='padding: 20px; text-align: center; color: #666;'>No hay hoteles con más de 2 reservas</td></tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Total de hoteles con más de 2 reservas: $count</strong></p>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>