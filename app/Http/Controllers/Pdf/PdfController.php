<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Pdf\CompetitionPdfCreator;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function test(Competition $comp)
    {

        $pdfCreator = new CompetitionPdfCreator($comp);


        return  $pdfCreator->chiefTimekeeper();
    }
}
