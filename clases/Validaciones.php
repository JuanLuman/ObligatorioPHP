<?php

// clase con validaciones reutilizables para todos los formularios
// las funciones son estaticas asi no hace falta instanciar la clase

class Validaciones {


    // valida que un campo obligatorio no venga vacio
    public static function esRequerido($valor) {
        return isset($valor) && trim($valor) != "";
    }



    // valida formato de email (usa el filtro nativo de PHP)
    public static function esEmailValido($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }



    // valida que el password tenga al menos $min caracteres (letra pide minimo 8)
    public static function esPasswordValido($password, $min = 8) {
        return isset($password) && strlen($password) >= $min;
    }



    // valida que una fecha venga en formato YYYY-MM-DD y sea real
    public static function esFechaValida($fecha) {
        if (empty($fecha)) {
            return false;
        }
        // separo la fecha y verifico que sea valida en el calendario
        $partes = explode("-", $fecha);
        if (count($partes) != 3) {
            return false;
        }
        return checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0]);
    }



    // valida que $fechaFin sea posterior a $fechaInicio (regla de negocio de prestamos)
    public static function fechaPosterior($fechaInicio, $fechaFin) {
        if (!self::esFechaValida($fechaInicio) || !self::esFechaValida($fechaFin)) {
            return false;
        }
        return strtotime($fechaFin) > strtotime($fechaInicio);
    }



    // valida que un valor sea un numero entero positivo (para ids, cantidades, etc)
    public static function esEnteroPositivo($valor) {
        return is_numeric($valor) && (int)$valor > 0;
    }



    // valida un formulario completo. Recibe un array asociativo con los campos requeridos.
    // Devuelve array vacio si esta todo OK, o array con los mensajes de error.
    // Ejemplo de uso:
    //   $errores = Validaciones::validarFormulario([
    //       'nombre'   => $_POST['nombre'],
    //       'email'    => $_POST['email'],
    //       'password' => $_POST['password'],
    //   ], ['nombre', 'email', 'password']);
    public static function validarFormulario($campos, $requeridos) {
        $errores = [];

        foreach ($requeridos as $campo) {
            if (!isset($campos[$campo]) || !self::esRequerido($campos[$campo])) {
                $errores[] = "El campo '$campo' es obligatorio";
            }
        }

        // si vino un email, valido su formato
        if (isset($campos['email']) && !empty($campos['email'])) {
            if (!self::esEmailValido($campos['email'])) {
                $errores[] = "El email no tiene un formato valido";
            }
        }

        // si vino un password, valido su longitud minima
        if (isset($campos['password']) && !empty($campos['password'])) {
            if (!self::esPasswordValido($campos['password'])) {
                $errores[] = "El password debe tener al menos 8 caracteres";
            }
        }

        return $errores;
    }

}

?>
