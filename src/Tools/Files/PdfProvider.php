<?php

namespace App\Tools\Files;

use App\Exception\AppException;
use mikehaertl\wkhtmlto\Pdf;
use App\Tools\DirectoryResolver;


class PdfProvider
{

    public static function getInstance(): Pdf
    {
        return new Pdf([
            'binary' => 'C:\Program Files\wkhtmltopdf',
            'ignoreWarnings' => true,
            'encoding' => 'UTF-8',
            'user-style-sheet' => self::getStyle(),
            'run-script' => self::getScripts(),
            'no-outline'
        ]);
    }

    public function generate(string $content, ?string $filename = null, ?bool $save = true): bool
    {
        $pdf = $this->getInstance();
        $pdf->addPage($content);
        $method = is_null($filename) ? 'send' : ($filename and $save ? 'save' : 'send');
        if (!$pdf->$method($filename)) {
            throw new AppException($pdf->getError());
        }
        return true;
    }

    private static function getStyle(): string
    {
        return 'localhost:8000/assets/css/pdf.css';
        return DirectoryResolver::getDirectory('public\assets\css\pdf.css', false);
    }

    private static function getScripts(): array
    {
        return [
            DirectoryResolver::getDirectory('public\assets\css\pdf.css', false),
            DirectoryResolver::getDirectory('public\assets\css\pdf.css', false)
        ];
    }
}
