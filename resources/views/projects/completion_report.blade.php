<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Penyelesaian Proyek:') }} "{{ $project->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">
                        {{ $project->name }} {{ __('Laporan Proyek') }}
                    </h3>

                    @if(\Carbon\Carbon::now()->isAfter(\Carbon\Carbon::parse($project->end_date)))
                        <p class="text-lg text-gray-700 mb-4 text-center">
                            Proyek ini telah melewati tanggal deadline pada **{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}**.
                        </p>
                    @else
                        <p class="text-lg text-gray-700 mb-4 text-center">
                            Tanggal deadline proyek ini adalah **{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}**.
                        </p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-semibold text-indigo-800 mb-2">Ringkasan Proyek:</h4>
                            <p class="text-sm text-gray-700">Total Sub System: <span class="font-bold">{{ $totalSubSystems }}</span></p>
                            <p class="text-sm text-gray-700">Engineer yang Ditugaskan: <span class="font-bold">{{ $project->assignedUsers->pluck('name')->join(', ') ?: 'Tidak ada' }}</span></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-semibold text-green-800 mb-2">Total Dokumen Diunggah per Kategori:</h4>
                            @forelse($documentCategoryCounts as $category => $count)
                                <p class="text-sm text-gray-700">{{ $category }}: <span class="font-bold">{{ $count }}</span> dokumen</p>
                            @empty
                                <p class="text-sm text-gray-700">Belum ada dokumen yang diunggah.</p>
                            @endforelse
                        </div>
                    </div>

                    <h4 class="text-xl font-semibold text-gray-800 mb-4">Status Penyelesaian per Sub System:</h4>
                    @forelse($subSystemCompletionStatus as $ssStatus)
                        <div class="border-b pb-4 mb-4">
                            <h5 class="text-lg font-bold text-gray-700">{{ $ssStatus['name'] }} ({{ $ssStatus['progress_percentage'] }}% Selesai)</h5>
                            <ul class="list-disc list-inside ml-4 text-sm text-gray-600 mt-2">
                                @forelse($ssStatus['sites_completion'] as $siteComp)
                                    <li>
                                        Site: <span class="font-semibold">{{ $siteComp['name'] }}</span> ({{ $siteComp['progress_percentage'] }}%):
                                        @if($siteComp['all_required_files_uploaded'])
                                            <span class="text-green-600">Dokumen Wajib Lengkap</span>
                                        @else
                                            <span class="text-red-600">Dokumen Wajib BELUM Lengkap</span>
                                            @if($siteComp['deadline_status'] == 'overdue')
                                                <span class="text-red-500"> - Terlambat {{ $siteComp['days_overdue'] }} hari</span>
                                            @endif
                                        @endif
                                    </li>
                                @empty
                                    <li>Tidak ada site di Sub System ini.</li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">Tidak ada Sub System di proyek ini.</p>
                    @endforelse

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Kembali ke Daftar Proyek</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>