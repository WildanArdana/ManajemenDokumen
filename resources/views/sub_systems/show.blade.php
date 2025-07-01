<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Sub System:') }} "{{ $subSystem->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Informasi Sub System</h3>
                    <p class="text-gray-700"><strong>Proyek:</strong> {{ $subSystem->project->name }}</p>
                    <p class="text-gray-700"><strong>Nama Sub System:</strong> {{ $subSystem->name }}</p>
                    <p class="text-lg font-bold mt-4">Progress Keseluruhan: {{ $subSystem->progress_percentage }}%</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Site</h3>

                    @if(Auth::user()->isAdmin())
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Tambah Site Baru</h4>
                        <form action="{{ route('sites.store', $subSystem) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Site</label>
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>Tambah Site</x-primary-button>
                            </div>
                        </form>
                    @endif

                    <h4 class="text-lg font-medium text-gray-900 mt-8 mb-4">Daftar Site</h4>
                    @if($subSystem->sites->isEmpty())
                        <p>Belum ada site ditambahkan ke Sub System ini.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($subSystem->sites as $site)
                                <li class="py-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('sites.show', $site) }}" class="text-indigo-600 hover:underline">
                                                {{ $site->name }}
                                            </a>
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $site->address }}</p>
                                        <p class="text-sm text-gray-500 mt-1">Progress: <span class="font-bold">{{ $site->progress_percentage }}%</span></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('sites.show', $site) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Lihat Detail</a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('sites.edit', $site) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                                            <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
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