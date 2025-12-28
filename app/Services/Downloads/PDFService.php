<?php

namespace App\Services\Downloads;

use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PDFService
{
    public function download_pdf(Request $request)
    {

        $table_contant = $request->input('table_contant');
        $title = $request->input('title');
        $orientation = $request->input('orientation');
        $documentFileName = date('d-m-Y').'-'.$title.'.pdf';

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path().'/assets/fonts/',
            ]),
            'fontdata' => $fontData + [ // lowercase letters only in font key
                'nikosh' => [
                    'R' => 'Nikosh.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font_size' => 10,
            'default_font' => 'nikosh',
            'format' => 'A4',
            'orientation' => $orientation,
        ]);

        //Add Table Style Here
        $table_contant .= '<style>
                                table {
                                    font-family: arial, sans-serif;
                                    border-collapse: collapse;
                                    width: 100%;
                                }

                                td, th {
                                    border: 1px solid #dddddd;
                                    text-align: left;
                                    padding: 8px;
                                }
                            </style>';

        // To support Bootstrap add this line
        //$bootstrap = file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$documentFileName.'"');

        $mpdf->WriteHTML($table_contant);
        $mpdf->OutputHttpDownload($documentFileName, 'I');

    }
}
