<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;



class ExampleExport implements WithEvents
{
	use Exportable, RegistersEventListeners;

    public static function beforeExport(BeforeExport $event)
    {
        
        $event->writer->getProperties()->setCreator('Patrick');

    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        //	Set the Labels for each data series we want to plot
        //		Datatype
        //		Cell reference for data
        //		Format Code
        //		Number of datapoints in series
        //		Data values
        //		Data Marker
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), //	2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), //	2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), //	2012
        ];
        //	Set the X-Axis Labels
        //		Datatype
        //		Cell reference for data
        //		Format Code
        //		Number of datapoints in series
        //		Data values
        //		Data Marker
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), //	Q1 to Q4
        ];
        //	Set the Data values for each data series we want to plot
        //		Datatype
        //		Cell reference for data
        //		Format Code
        //		Number of datapoints in series
        //		Data values
        //		Data Marker
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
        ];
        $dataSeriesValues[2]->setLineWidth(60000);

        //	Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_LINECHART, // plotType
            DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );

        //	Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        //	Set the chart legend
        $legend = new Legend(Legend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test Stacked Line Chart');
        $yAxisLabel = new Title('Value ($k)');

        //	Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            0, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        //	Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        //	Add the chart to the worksheet
        $event->sheet->getDelegate()->addChart($chart);

        
    }

    public static function beforeWriting(BeforeWriting $event)
    {
           $event->writer->setIncludeCharts(true);
    }
}