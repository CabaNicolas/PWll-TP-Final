<?php
include_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph.php';
include_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_bar.php';
include_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_pie.php';
include_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_pie3d.php';
class GraphHelper {
    public static function generarBarplot($datos) {
        $directorioGrafico = __DIR__ . '/../public/graficos';

        if (!file_exists($directorioGrafico)) {
            mkdir($directorioGrafico, 0777, true);
        }

        $nombreArchivo = uniqid('barplot_') . '.png';
        $archivoGrafico = $directorioGrafico . '/' . $nombreArchivo;
        $grafico = new Graph(600, 400);
        $grafico->SetScale('textlin');
        $barras = new BarPlot($datos['valores']);
        $grafico->Add($barras);
        $grafico->title->Set($datos['tituloDelGrafico']);
        $grafico->xaxis->title->Set($datos['tituloDeX']);
        $grafico->yaxis->title->Set($datos['tituloDeY']);
        $grafico->xaxis->SetTickLabels($datos['etiquetas']);
        $grafico->Stroke($archivoGrafico);
        return '/public/graficos/' . $nombreArchivo;
    }

    public static function generarPieplot($datos) {
        $directorioGrafico = __DIR__ . '/../public/graficos';

        if (!file_exists($directorioGrafico)) {
            mkdir($directorioGrafico, 0755, true);
        }

        $nombreArchivo = uniqid('pieplot_') . '.png';
        $archivoGrafico = $directorioGrafico . '/' . $nombreArchivo;
        $grafico = new PieGraph(600, 400);
        $grafico->SetShadow();
        $grafico->title->Set($datos['tituloDelGrafico']);
        $grafico->SetBox(true);
        $p1 = new PiePlot3D($datos['valores']);
        $p1->SetSize(0.35);
        $p1->SetCenter(0.5);
        $p1->SetLegends($datos['etiquetas']);
        $p1->SetLabelPos(0.2);
        $grafico->Add($p1);
        $grafico->Stroke($archivoGrafico);
        return '/public/graficos/' . $nombreArchivo; }
}