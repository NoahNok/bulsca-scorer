{{-- Chrome header template: rendered on its own, so it can only use inline styles. PROVISIONAL shows on every page while results are provisional --}}
<div style="width: 100%; padding: 0 10mm; font-family: sans-serif; font-size: 8pt; display: flex; justify-content: space-between; align-items: flex-end;">
    <span style="font-weight: 600; text-transform: uppercase;">
        @if ($provisional ?? false)
            <span style="border: 1px solid black; padding: 0 2mm; margin-right: 2mm;">Provisional</span>
        @endif
        {{ $title }}
    </span>
    <span style="text-align: right;">{{ $comp->name }} - {{ $comp->when->format('jS F Y') }}<br>{{ $comp->where }}</span>
</div>
