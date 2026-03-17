@props(['headers' => []])

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(count($headers))
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            @endif
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="px-6 py-4 border-t">{{ $pagination }}</div>
    @endif
</div>
