<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Validar y sanitizar datos
    $nombre = trim($_POST['nombre']);
    $ubicacion = trim($_POST['ubicacion']);
    $habitaciones_disponibles = (int)$_POST['habitaciones_disponibles'];
    $tarifa_noche = (float)$_POST['tarifa_noche'];
    
    // Validaciones del servidor
    $errores = [];
    
    if (strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener al menos 3 caracteres";
    }
    
    if (strlen($ubicacion) < 3) {
        $errores[] = "La ubicación debe tener al menos 3 caracteres";
    }
    
    if ($habitaciones_disponibles < 1 || $habitaciones_disponibles > 1000) {
        $errores[] = "Las habitaciones deben estar entre 1 y 1000";
    }
    
    if ($tarifa_noche < 0) {
        $errores[] = "La tarifa debe ser positiva";
    }
    
    if (empty($errores)) {
        try {
            $query = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
                      VALUES (:nombre, :ubicacion, :habitaciones_disponibles, :tarifa_noche)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':ubicacion', $ubicacion);
            $stmt->bindParam(':habitaciones_disponibles', $habitaciones_disponibles);
            $stmt->bindParam(':tarifa_noche', $tarifa_noche);
            
            if ($stmt->execute()) {
                echo "<div style='text-align: center; margin-top: 50px;'>
                        <h2 style='color: #27ae60;'>¡Hotel registrado exitosamente!</h2>
                        <p><a href='form_hotel.html' style='color: #f5576c;'>Registrar otro hotel</a></p>
                        <p><a href='mostrar_hoteles.php' style='color: #f5576c;'>Ver todos los hoteles</a></p>
                      </div>";
            } else {
                echo "<div style='text-align: center; margin-top: 50px;'>
                        <h2 style='color: #e74c3c;'>Error al registrar el hotel</h2>
                        <p><a href='form_hotel.html' style='color: #f5576c;'>Intentar nuevamente</a></p>
                      </div>";
            }
        } catch (PDOException $e) {
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h2 style='color: #e74c3c;'>Error en la base de datos: " . $e->getMessage() . "</h2>
                    <p><a href='form_hotel.html' style='color: #f5576c;'>Intentar nuevamente</a></p>
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
                <p><a href='form_hotel.html' style='color: #f5576c;'>Volver al formulario</a></p>
              </div>";
    }
}
?>