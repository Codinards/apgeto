<?php

namespace App\SpreadSheets;

interface SheetInterface
{
    public function create(?string $folder): void;
}
