<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Proyek untuk:') }} "{{ $project->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(Auth::user()->isAdmin())
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Unggah File Proyek Baru</h3>
                        <form action="{{ route('projects.files.store', $project) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama File</label>
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="mb-4">
                                <label for="file" class="block text-sm font-medium text-gray-700">Pilih File</label>
                                <input type="file" name="file" id="file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                                <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            </div>
                            <x-primary-button>Unggah File</x-primary-button>
                        </form>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">File Proyek yang Ada</h3>
                    @if($projectFiles->isEmpty())
                        <p>Belum ada file proyek yang diunggah.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($projectFiles as $file)
                                <li class="py-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $file->name }}</p>
                                        <p class="text-xs text-gray-500">Diunggah oleh: {{ $file->uploader->name }} pada {{ $file->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('project-files.download', $file) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Unduh</a>
                                        @if(Auth::user()->isAdmin())
                                            <form action="{{ route('projects.files.destroy', [$project, $file]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
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