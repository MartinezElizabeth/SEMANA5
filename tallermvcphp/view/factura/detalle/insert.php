<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/semana5/tallermvcphp/routes.php');

class detalleFactura {

    function __construct() {}

    // Obtiene los detalles de la factura almacenados en un archivo JSON
    public function getDetalles($sesion) {
        return json_decode(file_get_contents(VIEW_PATH."factura/detalle/tmpdetallefacturas$sesion.json"), true);
    }

    // Obtiene un detalle específico por su ID
    public function getDetalleById($id, $sesion) {
        $detalles = $this->getDetalles($sesion);
        foreach ($detalles as $detalle) {
            if ($detalle['id'] == $id) {
                return $detalle;
            }
        }
        return null;
    }

    // Crea un nuevo detalle si el archivo JSON ya existe
    public function createDetalleExist($data, $sesion) {
        $detalles = $this->getDetalles($sesion);
        $detalles[] = $data; // Agrega el nuevo detalle al arreglo
        $this->putJson($detalles, $sesion); // Guarda los cambios en el archivo JSON
    }

    // Crea un nuevo detalle si el archivo JSON no existe
    public function createDetalleNotExist($data, $sesion) {
        $detalles[] = $data; // Si no existe el archivo, solo agrega el detalle
        $this->putJson($detalles, $sesion); // Guarda el nuevo arreglo en el archivo JSON
    }

    // Elimina un detalle por su ID
    public function deleteDetalle($id, $sesion) {
        $detalles = $this->getDetalles($sesion);
        foreach ($detalles as $i => $detalle) {
            if ($detalle['idTmpDetalle'] == $id) {
                array_splice($detalles, $i, 1); // Elimina el detalle de la lista
            }
        }
        $this->putJson($detalles, $sesion); // Guarda el arreglo actualizado
    }

    // Elimina todos los detalles
    public function deleteAllDetalles($sesion) {
        $detalles = array(); // Crea un arreglo vacío
        $this->putJson($detalles, $sesion); // Guarda el arreglo vacío en el archivo
    }

    // Guarda el arreglo de detalles en el archivo JSON
    public function putJson($detalles, $sesion) {
        file_put_contents(VIEW_PATH."factura/detalle/tmpdetallefacturas$sesion.json", json_encode($detalles, JSON_PRETTY_PRINT));
    }
}
?>
