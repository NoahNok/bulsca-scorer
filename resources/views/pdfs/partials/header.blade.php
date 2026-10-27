{{-- Chrome header template: rendered on its own, so it can only use inline styles --}}
<div style="width: 100%; padding: 0 10mm; font-family: sans-serif; font-size: 8pt; display: flex; justify-content: space-between; align-items: flex-end;">
    <span style="font-weight: 600; text-transform: uppercase;">{{ $packName }}</span>
    <span style="text-align: right;">{{ $comp->name }} - {{ $comp->when->format('jS F Y') }}<br>{{ $comp->where }}</span>
</div>
