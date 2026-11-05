<?php

namespace App\Pdf;

use App\Models\Competition;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

/**
 * A competition's PDF (a Pack or a Results printout), rendered with headless Chrome: an inline A4
 * PDF of a view extending pdfs.layout, with the running header and page-numbered footer.
 * See docs/adr/0002-browsershot-for-pdf-packs.md.
 */
class CompetitionPdf
{
    public static function make(
        Competition $comp,
        string $view,
        string $title,
        array $data,
        Orientation $orientation = Orientation::Portrait,
        bool $provisional = false,
    ): PdfBuilder {
        $shared = ['comp' => $comp, 'title' => $title];

        return Pdf::view($view, [...$data, ...$shared])
            ->headerView('pdfs.partials.header', [...$shared, 'provisional' => $provisional])
            ->footerView('pdfs.partials.footer')
            ->format(Format::A4)
            ->orientation($orientation)
            ->margins(20, 10, 15, 10)
            // No PDF needs JavaScript, and Chrome may run without its sandbox (LARAVEL_PDF_NO_SANDBOX),
            // so don't run any script that organiser-entered HTML might carry
            ->withBrowsershot(fn (Browsershot $browsershot) => $browsershot->disableJavascript())
            ->inline(Str::slug("{$comp->name} {$title}") . '.pdf');
    }
}
