<?php

namespace App\Services;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\Blade;

class PdfDownloadService
{
    protected static array $pdfConfig = [
        'default_font' => 'Cairo',
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 12,
        'orientation' => 'P',
    ];

    /**
     * Generates PDF from a Blade view.
     */
    protected static function generatePdf(string $viewPath, array $data): Mpdf
    {
        $mpdf = new Mpdf(self::$pdfConfig);
        $html = Blade::render($viewPath, $data);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    /**
     * Streams PDF for download.
     */
    public static function streamPdfDownload(string $viewPath, string $fileName, array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($viewPath, $data) {
            $mpdf = self::generatePdf($viewPath, $data);
            $mpdf->Output();
        }, "{$fileName}.pdf");
    }
}
