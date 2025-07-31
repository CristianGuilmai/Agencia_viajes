<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->getConnection();

$mensaje = '';
$error = '';

// Procesar formularios
if ($_POST) {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'insertar_vuelo':
                    $query = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        $_POST['origen'],
                        $_POST['destino'],
                        $_POST['fecha'],
                        $_POST['plazas_disponibles'],
                        $_POST['precio']
                    ]);
                    $mensaje = "Vuelo insertado correctamente";
                    break;
                
                case 'insertar_hotel':
                    $query = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) VALUES (?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        $_POST['nombre'],
                        $_POST['ubicacion'],
                        $_POST['habitaciones_disponibles'],
                        $_POST['tarifa_noche']
                    ]);
                    $mensaje = "Hotel insertado correctamente";
                    break;
                
                case 'insertar_reserva':
                    $query = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) VALUES (?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        $_POST['id_cliente'],
                        $_POST['fecha_reserva'],
                        $_POST['id_vuelo'],
                        $_POST['id_hotel']
                    ]);
                    $mensaje = "Reserva insertada correctamente";
                    break;
                
                case 'insertar_datos_ejemplo':
                    // Insertar vuelos de ejemplo
                    $vuelos = [
                        ['Madrid', 'Barcelona', '2024-12-15', 150, 125.50],
                        ['Barcelona', 'Valencia', '2024-12-20', 120, 89.90],
                        ['Madrid', 'Sevilla', '2024-12-18', 180, 99.99],
                        ['Valencia', 'Bilbao', '2024-12-22', 100, 150.00]
                    ];
                    
                    foreach ($vuelos as $vuelo) {
                        $query = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $stmt->execute($vuelo);
                    }
                    
                    // Insertar hoteles de ejemplo
                    $hoteles = [
                        ['Hotel Majestic', 'Madrid', 50, 120.00],
                        ['Hotel Costa Brava', 'Barcelona', 80, 95.50],
                        ['Hotel Mediterráneo', 'Valencia', 45, 85.00],
                        ['Hotel Norte', 'Bilbao', 60, 110.00]
                    ];
                    
                    foreach ($hoteles as $hotel) {
                        $query = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) VALUES (?, ?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $stmt->execute($hotel);
                    }
                    
                    // Insertar reservas de ejemplo
                    $reservas = [
                        [1001, '2024-12-01', 1, 1],
                        [1002, '2024-12-02', 2, 2],
                        [1003, '2024-12-03', 1, 1],
                        [1004, '2024-12-04', 3, 3],
                        [1005, '2024-12-05', 2, 2],
                        [1006, '2024-12-06', 1, 1],
                        [1007, '2024-12-07', 4, 4],
                        [1008, '2024-12-08', 2, 2],
                        [1009, '2024-12-09', 1, 1],
                        [1010, '2024-12-10', 3, 3]
                    ];
                    
                    foreach ($reservas as $reserva) {
                        $query = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) VALUES (?, ?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $stmt->execute($reserva);
                    }
                    
                    $mensaje = "Todos los datos de ejemplo insertados correctamente";
                    break;
                
                case 'limpiar_datos':
                    $db->exec("DELETE FROM RESERVA");
                    $db->exec("DELETE FROM VUELO");
                    $db->exec("DELETE FROM HOTEL");
                    $db->exec("ALTER TABLE VUELO AUTO_INCREMENT = 1");
                    $db->exec("ALTER TABLE HOTEL AUTO_INCREMENT = 1");
                    $db->exec("ALTER TABLE RESERVA AUTO_INCREMENT = 1");
                    $mensaje = "Todos los datos han sido eliminados";
                    break;
            }
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener datos para mostrar
try {
    $vuelos = $db->query("SELECT * FROM VUELO ORDER BY id_vuelo DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $hoteles = $db->query("SELECT * FROM HOTEL ORDER BY id_hotel DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $reservas = $db->query("SELECT r.*, v.origen, v.destino, h.nombre as hotel_nombre FROM RESERVA r LEFT JOIN VUELO v ON r.id_vuelo = v.id_vuelo LEFT JOIN HOTEL h ON r.id_hotel = h.id_hotel ORDER BY r.id_reserva DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Datos de Ejemplo - Sistema de Reservas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .tab {
            flex: 1;
            padding: 15px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: #3498db;
            color: white;
        }

        .tab:hover {
            background: #2980b9;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #3498db;
        }

        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-field {
            flex: 1;
            min-width: 200px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .data-table th {
            background: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .data-section {
            margin-top: 30px;
        }

        .data-section h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛫 Sistema de Inserción de Datos</h1>
            <p>Gestor de datos de ejemplo para el sistema de reservas</p>
        </div>

        <div class="content">
            <?php if ($mensaje): ?>
                <div class="alert alert-success">✅ <?php echo $mensaje; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Botones de acción principales -->
            <div class="action-buttons">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="accion" value="insertar_datos_ejemplo">
                    <button type="submit" class="btn btn-success">🚀 Insertar Datos de Ejemplo</button>
                </form>
                
                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar todos los datos?')">
                    <input type="hidden" name="accion" value="limpiar_datos">
                    <button type="submit" class="btn btn-danger">🗑️ Limpiar Todos los Datos</button>
                </form>
            </div>

            <!-- Pestañas -->
            <div class="tabs">
                <button class="tab active" onclick="showTab('vuelos')">✈️ Vuelos</button>
                <button class="tab" onclick="showTab('hoteles')">🏨 Hoteles</button>
                <button class="tab" onclick="showTab('reservas')">📋 Reservas</button>
                <button class="tab" onclick="showTab('datos')">📊 Ver Datos</button>
            </div>

            <!-- Contenido de las pestañas -->
            <div id="vuelos" class="tab-content active">
                <div class="form-section">
                    <h3>➕ Insertar Nuevo Vuelo</h3>
                    <form method="POST">
                        <input type="hidden" name="accion" value="insertar_vuelo">
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="origen">Origen:</label>
                                <input type="text" id="origen" name="origen" required>
                            </div>
                            <div class="form-field">
                                <label for="destino">Destino:</label>
                                <input type="text" id="destino" name="destino" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="fecha">Fecha:</label>
                                <input type="date" id="fecha" name="fecha" required>
                            </div>
                            <div class="form-field">
                                <label for="plazas_disponibles">Plazas Disponibles:</label>
                                <input type="number" id="plazas_disponibles" name="plazas_disponibles" min="1" required>
                            </div>
                            <div class="form-field">
                                <label for="precio">Precio (€):</label>
                                <input type="number" id="precio" name="precio" min="0" step="0.01" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">✈️ Insertar Vuelo</button>
                    </form>
                </div>
            </div>

            <div id="hoteles" class="tab-content">
                <div class="form-section">
                    <h3>➕ Insertar Nuevo Hotel</h3>
                    <form method="POST">
                        <input type="hidden" name="accion" value="insertar_hotel">
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="nombre">Nombre del Hotel:</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-field">
                                <label for="ubicacion">Ubicación:</label>
                                <input type="text" id="ubicacion" name="ubicacion" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="habitaciones_disponibles">Habitaciones Disponibles:</label>
                                <input type="number" id="habitaciones_disponibles" name="habitaciones_disponibles" min="1" required>
                            </div>
                            <div class="form-field">
                                <label for="tarifa_noche">Tarifa por Noche (€):</label>
                                <input type="number" id="tarifa_noche" name="tarifa_noche" min="0" step="0.01" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">🏨 Insertar Hotel</button>
                    </form>
                </div>
            </div>

            <div id="reservas" class="tab-content">
                <div class="form-section">
                    <h3>➕ Insertar Nueva Reserva</h3>
                    <form method="POST">
                        <input type="hidden" name="accion" value="insertar_reserva">
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="id_cliente">ID Cliente:</label>
                                <input type="number" id="id_cliente" name="id_cliente" min="1" required>
                            </div>
                            <div class="form-field">
                                <label for="fecha_reserva">Fecha de Reserva:</label>
                                <input type="date" id="fecha_reserva" name="fecha_reserva" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-field">
                                <label for="id_vuelo">ID Vuelo:</label>
                                <input type="number" id="id_vuelo" name="id_vuelo" min="1" required>
                            </div>
                            <div class="form-field">
                                <label for="id_hotel">ID Hotel:</label>
                                <input type="number" id="id_hotel" name="id_hotel" min="1" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">📋 Insertar Reserva</button>
                    </form>
                </div>
            </div>

            <div id="datos" class="tab-content">
                <div class="data-section">
                    <h4>📊 Últimos Vuelos Insertados</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Fecha</th>
                                <th>Plazas</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vuelos as $vuelo): ?>
                            <tr>
                                <td><?php echo $vuelo['id_vuelo']; ?></td>
                                <td><?php echo $vuelo['origen']; ?></td>
                                <td><?php echo $vuelo['destino']; ?></td>
                                <td><?php echo $vuelo['fecha']; ?></td>
                                <td><?php echo $vuelo['plazas_disponibles']; ?></td>
                                <td>€<?php echo number_format($vuelo['precio'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="data-section">
                    <h4>🏨 Últimos Hoteles Insertados</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Habitaciones</th>
                                <th>Tarifa/Noche</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hoteles as $hotel): ?>
                            <tr>
                                <td><?php echo $hotel['id_hotel']; ?></td>
                                <td><?php echo $hotel['nombre']; ?></td>
                                <td><?php echo $hotel['ubicacion']; ?></td>
                                <td><?php echo $hotel['habitaciones_disponibles']; ?></td>
                                <td>€<?php echo number_format($hotel['tarifa_noche'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="data-section">
                    <h4>📋 Últimas Reservas Insertadas</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha Reserva</th>
                                <th>Vuelo</th>
                                <th>Hotel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo $reserva['id_reserva']; ?></td>
                                <td><?php echo $reserva['id_cliente']; ?></td>
                                <td><?php echo $reserva['fecha_reserva']; ?></td>
                                <td><?php echo $reserva['origen'] . ' → ' . $reserva['destino']; ?></td>
                                <td><?php echo $reserva['hotel_nombre']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todos los contenidos
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Desactivar todas las pestañas
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Mostrar el contenido seleccionado
            document.getElementById(tabName).classList.add('active');
            
            // Activar la pestaña seleccionada
            event.target.classList.add('active');
        }

        // Establecer fecha actual por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const fechaInputs = document.querySelectorAll('input[type="date"]');
            fechaInputs.forEach(input => {
                if (!input.value) {
                    input.value = today;
                }
            });
        });
    </script>
</body>
</html>