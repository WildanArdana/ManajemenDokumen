<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Proyek / Pengumuman:</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $dashboardInfo }}</p>

                    @if(Auth::user()->isAdmin())
                        <form action="{{ route('dashboard.update.info') }}" method="POST" class="mt-4">
                            @csrf
                            <div class="mb-4">
                                <label for="info" class="block text-sm font-medium text-gray-700">Perbarui Info:</label>
                                <textarea name="info" id="info" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $dashboardInfo }}</textarea>
                                @error('info')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <x-primary-button>Perbarui Informasi</x-primary-button>
                        </form>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 mt-8">Proyek Anda:</h3>

                    {{-- Form Pencarian dan Pengurutan --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                        <div class="flex flex-wrap items-center gap-2">
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
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 flex-grow md:flex-grow-0 text-center">Reset</a>
                            @endif
                        </div>
                    </form>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($projects as $project)
                            {{-- Engineer akan melihat semua project yang memiliki SubSystem --}}
                            @if(Auth::user()->isAdmin() || $project->subSystems->count() > 0)
                                <div class="border rounded-lg shadow-sm p-4">
                                    <h4 class="text-md font-semibold text-gray-800">
                                        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">{{ $project->name }}</a>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($project->description, 100) }}</p>
                                    @if($project->end_date)
                                        <p class="text-xs text-gray-500 mt-1">Deadline: {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <div class="mt-3 flex space-x-2">
                                            <a href="{{ route('projects.edit', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit Project</a>
                                            <a href="{{ route('projects.files.index', $project) }}" class="text-sm text-blue-600 hover:text-blue-900">File Proyek</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @empty
                            <p>Tidak ada proyek tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>