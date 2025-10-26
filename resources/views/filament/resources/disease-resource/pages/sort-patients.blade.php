<x-filament-panels::page>
    <div class="space-y-6">
        {{-- <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $this->getTitle() }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Total patients: {{ $this->getTableQuery()->count() }}
                    </p>
                </div>
            </div>
        </div> --}}

        {{ $this->table }}
    </div>
</x-filament-panels::page>
