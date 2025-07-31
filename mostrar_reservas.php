<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->getConnection();

$reservas = [];
$total_reservas = 0;
$ingresos_totales = 0;

try {
    $query = "SELECT r.id_reserva, r.id_cliente, r.fecha_reserva,
                     v.origen, v.destino, v.fecha as fecha_vuelo, v.precio as precio_vuelo,
                     h.nombre as hotel_nombre, h.ubicacion, h.tarifa_noche
              FROM RESERVA r
              LEFT JOIN VUELO v ON r.id_vuelo = v.id_vuelo
              LEFT JOIN HOTEL h ON r.id_hotel = h.id_hotel
              ORDER BY r.fecha_reserva DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reservas[] = $row;
        $total_reservas++;
        $costo_total = ($row['precio_vuelo'] ?? 0) + ($row['tarifa_noche'] ?? 0);
        $ingresos_totales += $costo_total;
    }
    
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Nuvia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header Sticky */
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

        /* Contenido principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .page-header p {
            font-size: 1.2rem;
            color: #666;
        }

        .reservas-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-responsive {
            overflow-x: auto;
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
            font-size: 0.9rem;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .vuelo-info {
            font-weight: 500;
            color: #2c3e50;
        }

        .hotel-info {
            font-weight: 500;
            color: #8e44ad;
        }

        .costo-total {
            font-weight: 600;
            color: #27ae60;
            font-size: 1.1rem;
        }

        .cliente-id {
            background: #e3f2fd;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .resumen {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #27ae60;
        }

        .resumen h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .resumen-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .resumen-item .numero {
            font-size: 2rem;
            font-weight: 700;
            color: #3498db;
            display: block;
        }

        .resumen-item .etiqueta {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .no-reservas {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .no-reservas-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-reservas h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .no-reservas p {
            color: #999;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        /* Responsive */
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

            .page-header h1 {
                font-size: 2rem;
            }

            .resumen-grid {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 10px;
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
                <a href="mostrar_hoteles.php" class="nav-link">Hoteles</a>
                <a href="mostrar_reservas.php" class="nav-link active">Mis Reservas</a>
                <a href="estadisticas.php" class="nav-link">Estadísticas</a>
            </nav>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <div class="page-header">
            <h1>📋 Mis Reservas</h1>
            <p>Gestiona y revisa todas tus reservas de vuelos y hoteles</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($reservas)): ?>
            <div class="no-reservas">
                <div class="no-reservas-icon">📭</div>
                <h3>No tienes reservas aún</h3>
                <p>Comienza a planificar tu próximo viaje reservando vuelos y hoteles</p>
            </div>
        <?php else: ?>
            <div class="reservas-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>Cliente</th>
                                <th>Fecha Reserva</th>
                                <th>Vuelo</th>
                                <th>Hotel</th>
                                <th>Ubicación</th>
                                <th>Costo Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <?php
                                $vuelo_info = $reserva['origen'] ? $reserva['origen'] . ' → ' . $reserva['destino'] : 'N/A';
                                $hotel_info = $reserva['hotel_nombre'] ? $reserva['hotel_nombre'] : 'N/A';
                                $costo_total = ($reserva['precio_vuelo'] ?? 0) + ($reserva['tarifa_noche'] ?? 0);
                                ?>
                                <tr>
                                    <td><strong>#<?php echo $reserva['id_reserva']; ?></strong></td>
                                    <td><span class="cliente-id">Cliente #<?php echo $reserva['id_cliente']; ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                                    <td><span class="vuelo-info"><?php echo $vuelo_info; ?></span></td>
                                    <td><span class="hotel-info"><?php echo $hotel_info; ?></span></td>
                                    <td><?php echo $reserva['ubicacion'] ?? 'N/A'; ?></td>
                                    <td><span class="costo-total">$<?php echo number_format($costo_total, 2); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="resumen">
                <h3>📊 Resumen de Reservas</h3>
                <div class="resumen-grid">
                    <div class="resumen-item">
                        <span class="numero"><?php echo $total_reservas; ?></span>
                        <span class="etiqueta">Total de Reservas</span>
                    </div>
                    <div class="resumen-item">
                        <span class="numero">$<?php echo number_format($ingresos_totales, 2); ?></span>
                        <span class="etiqueta">Ingresos Totales</span>
                    </div>
                    <div class="resumen-item">
                        <span class="numero">$<?php echo number_format($total_reservas > 0 ? $ingresos_totales / $total_reservas : 0, 2); ?></span>
                        <span class="etiqueta">Promedio por Reserva</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>