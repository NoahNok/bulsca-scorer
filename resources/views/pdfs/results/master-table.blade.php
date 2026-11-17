{{-- A Master sheet's table: each Club's value from every Results sheet in it, its total and its place --}}
<table class="text-sm text-right w-full border">
    <thead class="text-xs uppercase bg-gray-50">
        <tr>
            <th class="py-2 px-2 border text-left">Club</th>
            @foreach ($section['sheets'] as $sheet)
                <th class="py-2 px-2 border">{{ $sheet->name }}</th>
            @endforeach
            <th class="py-2 px-2 border">Total</th>
            <th class="py-2 px-2 border">Position</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($section['results'] as $result)
            <tr>
                <td class="py-2 px-2 border text-left font-semibold">
                    <div class="entry-name">{{ $result->entity->getName($comp) }}</div>
                </td>
                @foreach ($section['sheets'] as $sheet)
                    <td class="py-2 px-2 border">{{ round($result->sheetResults[$sheet->id] ?? 0, 1) }}</td>
                @endforeach
                <td class="py-2 px-2 border font-semibold">{{ round($result->total, 1) }}</td>
                <td class="py-2 px-2 border">{{ $result->position }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
