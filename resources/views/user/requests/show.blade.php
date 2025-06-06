<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Detail Permintaan Talent
            </h1>
        </div>
        <a href="{{ route('user.requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Permintaan Saya
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="px-6 py-5 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Informasi Permintaan</h2>
        </div>
        <div class="px-6 py-5 sm:px-8 sm:py-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Permintaan</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">#{{ $talentRequest->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Keseluruhan</dt>
                <dd class="mt-1 text-lg">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @switch($talentRequest->status)
                            @case('pending_admin') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                            @case('pending_talent') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                            @case('approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                            @case('rejected_admin')
                            @case('rejected_talent')
                            @case('rejected_by_one_or_more_talents')
                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                            @case('completed') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 @break
                            @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endswitch
                    ">
                        @switch($talentRequest->status)
                            @case('pending_admin') Menunggu Admin @break
                            @case('pending_talent') Menunggu Talent @break
                            @case('approved') Disetujui @break
                            @case('rejected_admin') Ditolak Admin @break
                            @case('rejected_talent') Ditolak Talent @break
                            @case('rejected_by_one_or_more_talents') Ditolak oleh Satu atau Lebih Talent @break
                            @case('completed') Selesai @break
                            @default {{ Str::title(str_replace('_', ' ', $talentRequest->status)) }}
                        @endswitch
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Diminta Pada</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->created_at->format('d M Y H:i') }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Detail Permintaan</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white whitespace-pre-wrap">{{ $talentRequest->details }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Lokasi Kerja</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">
                    @if($talentRequest->work_location_type === 'remote')
                        Jarak Jauh
                    @elseif($talentRequest->work_location_type === 'on_site')
                        Di Tempat
                    @elseif($talentRequest->work_location_type === 'hybrid')
                        Hibrida
                    @else
                        {{ Str::title(str_replace('_', ' ', $talentRequest->work_location_type)) }}
                    @endif
                </dd>
            </div>

            @if ($talentRequest->work_location_type !== 'remote')
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kota Lokasi Kerja</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->work_location_city ?? 'T/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Negara Lokasi Kerja</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->work_location_country ?? 'T/A' }}</dd>
            </div>
            @endif

            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kota Domisili Peminta</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->user->domicile_city ?? 'T/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Negara Domisili Peminta</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->user->domicile_country ?? 'T/A' }}</dd>
            </div>

            @if ($talentRequest->competencies && $talentRequest->competencies->count() > 0)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Kompetensi yang Diminta</dt>
                <dd class="mt-1 text-gray-900 dark:text-white">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($talentRequest->competencies as $competency)
                            <li class="text-lg">{{ $competency->name }} (Level: {{ $competency->pivot->required_proficiency_level }}, Bobot: {{ $competency->pivot->weight }})</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
            @endif
        </div>



        @if ($talentRequest->assignedTalents && $talentRequest->assignedTalents->count() > 0)
            <div class="px-6 py-5 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Talent yang Ditugaskan & Status</h3>
                <div class="space-y-4">
                    @foreach ($talentRequest->assignedTalents as $talent)
                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700/50">
                            <div class="flex justify-between items-center">
                                <p class="text-md font-semibold text-gray-900 dark:text-white">{{ $talent->name }} ({{ $talent->email }})</p>
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($talent->pivot->status)
                                        @case('pending_assignment_response') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('approved_by_talent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('rejected_by_talent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endswitch
                                ">
                                    @switch($talent->pivot->status)
                                        @case('pending_assignment_response') Menunggu Respon Penugasan @break
                                        @case('approved_by_talent') Disetujui oleh Talent @break
                                        @case('rejected_by_talent') Ditolak oleh Talent @break
                                        @default {{ Str::title(str_replace('_', ' ', $talent->pivot->status)) }}
                                    @endswitch
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ditugaskan: {{ $talent->pivot->created_at ? $talent->pivot->created_at->format('d M Y H:i') : 'T/A' }}</p>
                            @if ($talent->pivot->status === 'approved_by_talent' && $talent->phone_number)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Kontak: {{ $talent->phone_number }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="px-6 py-5 sm:px-8 sm:py-6 text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                <p>Belum ada talent yang ditugaskan untuk permintaan ini.</p>
            </div>
        @endif

        {{-- Tambahkan tindakan spesifik pengguna di sini, mis., mengonfirmasi penyelesaian jika berlaku --}}

    </div>
</div>
</x-layouts.app>
