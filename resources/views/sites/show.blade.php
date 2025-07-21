<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Site:') }} "{{ $site->name }}" ({{ $site->subSystem->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Informasi Site</h3>
                    <p class="text-gray-700"><strong>Proyek:</strong> {{ $site->subSystem->project->name }}</p>
                    <p class="text-gray-700"><strong>Sub System:</strong> {{ $site->subSystem->name }}</p>
                    <p class="text-gray-700"><strong>Nama Site:</strong> {{ $site->name }}</p>
                    <p class="text-gray-700"><strong>Alamat:</strong> {{ $site->address }}</p>
                    <p class="text-lg font-bold mt-4">Progress: {{ $percentage }}%</p>

                    {{-- Menambahkan informasi status dokumen dan deadline --}}
                    <p class="text-md font-bold mt-2">Status Dokumen Wajib:
                        @if($site->allRequiredFilesUploaded())
                            <span class="text-green-600">LENGKAP</span>
                        @else
                            <span class="text-red-600">BELUM LENGKAP</span>
                        @endif
                    </p>
                    <p class="text-md font-bold mt-1">Status Deadline:
                        @if($site->deadline_status == 'ontime')
                            <span class="text-green-600">Sesuai Jadwal</span>
                        @elseif($site->deadline_status == 'overdue')
                            <span class="text-red-600">TERLAMBAT ({{ $site->days_overdue }} hari)</span>
                        @else
                            <span class="text-gray-600">Tidak ada Deadline Proyek</span>
                        @endif
                    </p>
                    {{-- Akhir bagian baru --}}
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Unggah Dokumen</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($documents as $document)
                            <div class="border p-4 rounded-lg shadow-sm">
                                <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $document->name }}</h4>
                                @php
                                    $latestUpload = $uploadedDocuments->has($document->id) ? $uploadedDocuments[$document->id]->sortByDesc('uploaded_at')->first() : null;
                                @endphp

                                @if ($latestUpload)
                                    <p class="text-sm text-green-600 mb-2">Status: Diunggah <span class="font-medium">({{ $latestUpload->uploaded_at->format('d M Y H:i') }})</span> oleh <span class="font-medium">{{ $latestUpload->uploader->name }}</span></p>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('site-documents.download', $latestUpload) }}" class="px-3 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600">Unduh</a>
                                        @if(in_array(pathinfo($latestUpload->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                                            <a href="{{ route('site-documents.view', $latestUpload) }}" target="_blank" class="px-3 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">Lihat</a>
                                        @endif
                                        @can('deleteDocument', [$site, $latestUpload])
                                            <form action="{{ route('site-documents.destroy', $latestUpload) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                @else
                                    <p class="text-sm text-red-600 mb-2">Status: Belum Diunggah</p>
                                @endif

                                {{-- Hanya user yang diizinkan (Admin atau Engineer yang ditugaskan) yang bisa upload --}}
                                @if($canPerformActions)
                                    <form action="{{ route('sites.documents.store', $site) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                                        <div class="mb-2">
                                            <label for="file_{{ $document->id }}" class="sr-only">Unggah File untuk {{ $document->name }}</label>
                                            <input type="file" name="file" id="file_{{ $document->id }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                            @error('file')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <x-primary-button class="text-sm py-1 px-3">Unggah / Perbarui</x-primary-button>
                                    </form>
                                @else
                                    <p class="text-sm text-gray-500 mt-3">Anda tidak ditugaskan ke proyek ini untuk mengunggah dokumen.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4">Komentar</h3>

                    <div class="space-y-4 mb-6">
                        @forelse($site->comments->sortByDesc('created_at') as $comment)
                            <div class="border rounded-md p-3 bg-gray-50">
                                <p class="text-sm font-medium text-gray-800">{{ $comment->user->name }}:</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $comment->comment }}</p>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $comment->created_at->format('d M Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        @endforelse
                    </div>

                    <h4 class="text-lg font-medium text-gray-900 mb-2">Tambah Komentar</h4>
                    {{-- Hanya user yang diizinkan (Admin atau Engineer yang ditugaskan) yang bisa berkomentar --}}
                    @if($canPerformActions)
                        <form action="{{ route('sites.comments.store', $site) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="comment" class="sr-only">Komentar Anda</label>
                                <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tulis komentar..."></textarea>
                                @error('comment')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <x-primary-button>Kirim Komentar</x-primary-button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500 mt-3">Anda tidak ditugaskan ke proyek ini untuk menambahkan komentar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
