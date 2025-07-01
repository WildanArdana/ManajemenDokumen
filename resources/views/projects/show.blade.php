<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Proyek:') }} "{{ $project->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Informasi Proyek</h3>
                    <p class="text-gray-700"><strong>Nama:</strong> {{ $project->name }}</p>
                    <p class="text-gray-700"><strong>Deskripsi:</strong> {{ $project->description }}</p>
                    <p class="text-gray-700"><strong>Tanggal Mulai:</strong> {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}</p>
                    <p class="text-gray-700"><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>

                    <h4 class="text-lg font-medium text-gray-900 mt-6 mb-4">Engineer yang Ditugaskan:</h4>
                    @if($project->assignedUsers->isEmpty())
                        <p class="text-sm text-gray-600">Tidak ada engineer yang ditugaskan ke proyek ini.</p>
                    @else
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach($project->assignedUsers as $user)
                                <li>{{ $user->name }} ({{ $user->email }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Sub System</h3>

                    @if(Auth::user()->isAdmin())
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Tambah Sub System Baru</h4>
                        <form action="{{ route('sub_systems.store', $project) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Sub System (contoh: SS#1 MAMUJU - MAMASA)</label>
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>Tambah Sub System</x-primary-button>
                            </div>
                        </form>
                    @endif

                    <h4 class="text-lg font-medium text-gray-900 mt-8 mb-4">Daftar Sub System</h4>
                    @if($project->subSystems->isEmpty())
                        <p>Belum ada Sub System ditambahkan ke proyek ini.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($project->subSystems as $subSystem)
                                <li class="py-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('sub_systems.show', $subSystem) }}" class="text-indigo-600 hover:underline">
                                                {{ $subSystem->name }}
                                            </a>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">Progress: <span class="font-bold">{{ $subSystem->progress_percentage }}%</span></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('sub_systems.show', $subSystem) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Lihat Site</a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('sub_systems.edit', $subSystem) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                                            <form action="{{ route('sub_systems.destroy', $subSystem) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>