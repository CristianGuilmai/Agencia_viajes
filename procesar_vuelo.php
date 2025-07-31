<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Validar y sanitizar datos
    $origen = trim($_POST['origen']);
    $destino = trim($_POST['destino']);
    $fecha = $_POST['fecha'];
    $plazas_disponibles = (int)$_POST['plazas_disponibles'];
    $precio = (float)$_POST['precio'];
    
    // Validaciones del servidor
    $errores = [];
    
    if (strlen($origen) < 2) {
        $errores[] = "El origen debe tener al menos 2 caracteres";
    }
    
    if (strlen($destino) < 2) {
        $errores[] = "El destino debe tener al menos 2 caracteres";
    }
    
    if ($origen === $destino) {
        $errores[] = "El origen y destino deben ser diferentes";
    }
    
    if (strtotime($fecha) < time()) {
        $errores[] = "La fecha debe ser futura";
    }
    
    if ($plazas_disponibles < 1 || $plazas_disponibles > 500) {
        $errores[] = "Las plazas deben estar entre 1 y 500";
    }
    
    if ($precio < 0) {
        $errores[] = "El precio debe ser positivo";
    }
    
    if (empty($errores)) {
        try {
            $query = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
                      VALUES (:origen, :destino, :fecha, :plazas_disponibles, :precio)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':origen', $origen);
            $stmt->bindParam(':destino', $destino);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':plazas_disponibles', $plazas_disponibles);
            $stmt->bindParam(':precio', $precio);
            
            if ($stmt->execute()) {
                echo "<div style='text-align: center; margin-top: 50px;'>
                        <h2 style='color: #27ae60;'>¡Vuelo registrado exitosamente!</h2>
                        <p><a href='form_vuelo.html' style='color: #667eea;'>Registrar otro vuelo</a></p>
                        <p><a href='mostrar_vuelos.php' style='color: #667eea;'>Ver todos los vuelos</a></p>
                      </div>";
            } else {
                echo "<div style='text-align: center; margin-top: 50px;'>
                        <h2 style='color: #e74c3c;'>Error al registrar el vuelo</h2>
                        <p><a href='form_vuelo.html' style='color: #667eea;'>Intentar nuevamente</a></p>
                      </div>";
            }
        } catch (PDOException $e) {
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h2 style='color: #e74c3c;'>Error en la base de datos: " . $e->getMessage() . "</h2>
                    <p><a href='form_vuelo.html' style='color: #667eea;'>Intentar nuevamente</a></p>
                  </div>";
        }
    } else {
        echo "<div style='text-align: center; margin-top: 50px;'>
                <h2 style='color: #e74c3c;'>Errores encontrados:</h2>
                <ul style='color: #e74c3c;'>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>
                <p><a href='form_vuelo.html' style='color: #667eea;'>Volver al formulario</a></p>
              </div>";
    }
}
?>