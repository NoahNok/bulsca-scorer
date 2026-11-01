<div x-data="{ open: false }">
    <h4 @click="open = !open" class="mt-2 cursor-pointer hover:text-se" x-text="open ? 'Hide Changes' : 'View Changes'">
    </h4>

    <div class="se-table md:max-w-3/4" x-data x-show="open" x-collapse>

        <table>
            <thead>
                <tr>
                    <th>{{ $changes['columns']['label'] }}</th>
                    <th>{{ $changes['columns']['old'] }}</th>
                    <th>{{ $changes['columns']['new'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($changes['rows'] as $change)
                    <tr>
                        <th>{{ $change['label'] }}</th>
                        <td>{{ $change['old'] ?? 'N/A' }}</td>
                        <td>{{ $change['new'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



    </div>
</div>
