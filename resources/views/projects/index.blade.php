<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Proyek (Admin)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                        {{-- Tombol "Tambah Proyek Baru" hanya akan ditampilkan jika pengguna yang login adalah admin --}}
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Tambah Proyek Baru</a>
                        @endif
                        
                        {{-- Form Pencarian dan Pengurutan --}}
                        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-center gap-2 flex-grow">
                            <x-text-input type="text" name="search" placeholder="Cari proyek..." value="{{ request('search') }}" class="w-full md:w-auto flex-grow" />
                            <select name="sort" class="rounded-md border-gray-300 shadow-sm flex-grow md:flex-grow-0">
                                <option value="name" {{ $sortColumn == 'name' ? 'selected' : '' }}>Urutkan berdasarkan Nama</option>
                                <option value="start_date" {{ $sortColumn == 'start_date' ? 'selected' : '' }}>Urutkan berdasarkan Tanggal Mulai</option>
                                <option value="end_date" {{ $sortColumn == 'end_date' ? 'selected' : '' }}>Urutkan berdasarkan Deadline</option>
                            </select>
                            <select name="direction" class="rounded-md border-gray-300 shadow-sm flex-grow md:flex-grow-0">
                                <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>Menaik (A-Z, Lama ke Baru)</option>
                                <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>Menurun (Z-A, Baru ke Lama)</option>
                            </select>
                            <x-primary-button type="submit" class="flex-grow md:flex-grow-0">Terapkan</x-primary-button>
                            @if(request('search') || request('sort') != 'name' || request('direction') != 'asc')
                                <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 flex-grow md:flex-grow-0 text-center">Reset</a>
                            @endif
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($projects as $project)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $project->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($project->description, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat SubSystem</a>
                                        {{-- Tombol "Detail Laporan" tampil jika Admin ATAU project sudah lewat deadline --}}
                                        @if(Auth::user()->isAdmin() || ($project->end_date && \Carbon\Carbon::now()->isAfter(\Carbon\Carbon::parse($project->end_date))))
                                            <a href="{{ route('projects.completion_report', $project) }}" class="text-purple-600 hover:text-purple-900 mr-2">Detail Laporan</a>
                                        @endif
                                        @if(Auth::user()->isAdmin()) {{-- Tombol Edit, Hapus hanya tampil jika user adalah Admin --}}
                                            <a href="{{ route('projects.edit', $project) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada proyek ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
