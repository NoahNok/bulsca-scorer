<div x-data="{ open: false }">
    <h4 @click="open = !open" class="mt-2 cursor-pointer hover:text-se" x-text="open ? 'Hide Changes' : 'View Changes'">
    </h4>

    <div class="se-table md:max-w-3/4" x-data x-show="open" x-collapse>

        <table>
            <thead>
                <tr>
                    <th>Marking Point</th>
                    <th>Old Score</th>
                    <th>New Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($context['changes'] as $change)
                    <tr>
                        <th>{{ $change['name'] }}</th>
                        <td>{{ array_key_exists('old', $change) ? $change['old'] : 'N/A' }}</td>
                        <td>{{ array_key_exists('new', $change) ? $change['new'] : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



    </div>
</div>
