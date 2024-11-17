<!-- Hoja de estilo -->
<?php include_once 'style.php'; ?>

<!-- Página de la factura -->
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: Arial;">

    <!-- Encabezado de la factura -->
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 50%; color: #34495e; font-size: 16px; text-align: center;">
                <span style="color: #34495e; font-size: 24px; font-weight: bold;">
                    <?= NOMBRE_EMPRESA ?>
                </span><br>
                <?= DIRECCION_EMPRESA ?><br>
                Teléfono: <?= TELEFONO_EMPRESA ?><br>
                Email: <?= EMAIL_EMPRESA ?>
            </td>
            <td style="width: 25%; text-align: right; font-size: 16px;">
                FACTURA Nº <?= $numero ?>
            </td>
        </tr>
    </table>
    <br>

    <!-- Información del cliente -->
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width: 100%;" class="midnight-blue">RAZÓN SOCIAL</td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <?php
                echo "<pre><b>CLIENTE: </b>" . $estudiante['razonsocial'] . 
                     "<br><b>CI / RUC: </b>" . $estudiante['cin'] . 
                     "<br><b>DOMICILIO: </b>" . $estudiante['ciudad'] . 
                     "</pre>";
                ?>
            </td>
        </tr>
    </table>
    <br>

    <!-- Detalles de la factura -->
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width: 35%;" class="midnight-blue">USUARIO</td>
            <td style="width: 25%;" class="midnight-blue">FECHA</td>
            <td style="width: 40%;" class="midnight-blue">COMPROBANTE</td>
        </tr>
        <tr>
            <td style="width: 35%;"><?= $_SESSION['usuario'] ?></td>
            <td style="width: 25%;"><?= date("d/m/Y", strtotime($fecha)) ?></td>
            <td style="width: 40%;">Sin Valor Fiscal</td>
        </tr>
    </table>
    <br>

    <!-- Tabla de productos -->
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
        <tr>
            <th style="width: 10%; text-align: center;" class="midnight-blue">CANT.</th>
            <th style="width: 60%;" class="midnight-blue">DESCRIPCIÓN</th>
            <th style="width: 15%; text-align: right;" class="midnight-blue">UNITARIO</th>
            <th style="width: 15%; text-align: right;" class="midnight-blue">SUBTOTAL</th>
        </tr>

        <?php
        $nums = 1;
        $sumador_total = 0;
        $impuesto = 11;

        $arrDetalles = $JSONdetalle->getDetalles($sesion);
        foreach ($arrDetalles as $fila) {
            $idCurso = $fila['idConcepto'];
            $cantidad = $fila['cantidad'];
            $concepto = $fila['concepto'];
            $precio_unitario = $fila['unitario'];

            // Insertar detalle de factura en la base de datos
            $object->insertdetail($numero, $idCurso, $cantidad, $precio_unitario);

            // Cálculo de precios
            $precio_unitario_f = number_format($precio_unitario, 0);
            $precio_total = str_replace(",", "", $precio_unitario_f) * $cantidad;
            $precio_total_f = number_format($precio_total, 0);
            $sumador_total += $precio_total;

            // Alternar color de fila
            $clase = ($nums % 2 == 0) ? "clouds" : "silver";
        ?>
            <tr>
                <td class="<?= $clase ?>" style="width: 10%; text-align: center;"><?= $cantidad ?></td>
                <td class="<?= $clase ?>" style="width: 60%; text-align: left;"><?= $concepto ?></td>
                <td class="<?= $clase ?>" style="width: 15%; text-align: right;"><?= $precio_unitario_f ?></td>
                <td class="<?= $clase ?>" style="width: 15%; text-align: right;"><?= $precio_total_f ?></td>
            </tr>
        <?php
            $nums++;
        }

        // Calcular IVA y total de la factura
        $subtotal = number_format($sumador_total, 0, '.', '');
        $total_iva = number_format($subtotal / $impuesto, 0, '.', '');
        $total_factura = $subtotal;
        ?>
    </table>
    <br><br>

    <!-- Resumen de totales -->
    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 12pt;">
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">SUBTOTAL &#x20B2;</td>
            <td style="width: 15%; text-align: right;"><?= number_format($subtotal, 0) ?></td>
        </tr>
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">IVA 10%</td>
            <td style="width: 15%; text-align: right;"><?= number_format($total_iva, 0) ?></td>
        </tr>
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">TOTAL &#x20B2;</td>
            <td style="width: 15%; text-align: right;"><?= number_format($total_factura, 0) ?></td>
        </tr>
    </table>
    <br><br>

    <!-- Pie de página -->
    <div style="font-size: 11pt; text-align: center; font-weight: bold;">
        Factura Sin Valor Fiscal
    </div>

</page>

<?php
// Limpiar detalles de la sesión después de generar la factura
$JSONdetalle->deleteAllDetalles($sesion);
?>