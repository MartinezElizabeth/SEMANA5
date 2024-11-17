<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start();

// Constantes para los datos de encabezado
define('NOMBRE_EMPRESA', "LPTRES 2024");
define('DIRECCION_EMPRESA', "CAAGUAZU (colectora sur) Km 180");
define('TELEFONO_EMPRESA', "0522 44444");
define('EMAIL_EMPRESA', "lptres@gmail.com");

// Variables obtenidas de REQUEST
$fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : '';
$fecha = date("Y-m-d", strtotime($fecha)); // Corrección del formato de fecha
$idEstudiante = $_REQUEST['idEstudiante'] ?? null;
$idFormaPago = $_REQUEST['idFormaPago'] ?? null;
$idUsuario = $_SESSION['idUsuario'] ?? null;

// Validaciones iniciales
if (!$fecha || !$idEstudiante || !$idFormaPago || !$idUsuario) {
    echo "<script>alert('Faltan datos para generar la factura');</script>";
    echo "<script>window.close();</script>";
    exit();
}

// Base de datos - Tabla FACTURAS
include_once($_SERVER['DOCUMENT_ROOT'] . '/semana5/tallermvcphp/routes.php');
require_once(CONTROLLER_PATH . 'facturaController.php');

$object = new facturaController();
$estudiante = $object->listestudiantes($idEstudiante);
$numero = $object->insert($fecha, $idEstudiante, $idFormaPago, $idUsuario);

// Base de datos - Tabla auxiliar JSON
require_once('../detalle/insert.php');
$JSONdetalle = new detalleFactura();
$sesion = $_SESSION['usuario'] ?? '';
$arrDetalles = $JSONdetalle->getDetalles($sesion);
$count = count($arrDetalles);

// Validación: si no hay detalles
if ($count == 0) {
    echo "<script>alert('No hay artículos agregados a la factura');</script>";
    echo "<script>window.close();</script>";
    exit();
}

// Librería HTML2PDF
require_once ROOT_PATH . 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

// Generar el contenido HTML de la factura
include('doc/factura_html.php');
$content = ob_get_clean();

try {
    // Inicializar HTML2PDF
    $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0));
    // Mostrar la página completa
    $html2pdf->pdf->SetDisplayMode('real');
    // Convertir contenido a PDF
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    // Enviar el PDF al navegador
    $html2pdf->Output('factura_' . $sesion . '_' . session_id() . '.pdf');
} catch (Exception $e) {
    echo 'Error al generar el PDF: ' . htmlspecialchars($e->getMessage());
    exit();
}
?>
