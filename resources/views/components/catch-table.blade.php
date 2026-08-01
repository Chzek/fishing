@props(['records'])

<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table table-bordered table-striped']) }}>
        <thead class="thead-dark">
            <tr>
                <th>Date</th>
                <th>Angler</th>
                <th>Lake</th>
                <th>Fish Species</th>
                <th>Length (in)</th>
                <th>Weight (lbs)</th>
                <th>Lure</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->caught }}</td>
                    <td>{{ optional($record->angler)->fullName ?? 'N/A' }}</td>
                    <td>{{ optional($record->lake)->name ?? 'N/A' }}</td>
                    <td>{{ optional($record->fishBreed)->name ?? 'N/A' }}</td>
                    <td>{{ $record->length ?? '-' }}</td>
                    <td>{{ $record->weight ?? '-' }}</td>
                    <td>{{ optional($record->lure)->displayName ?? '-' }}</td>
                    <td>
                        @if($record->released)
                            <span class="badge badge-success">Released</span>
                        @else
                            <span class="badge badge-warning">Kept</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No catches recorded.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
