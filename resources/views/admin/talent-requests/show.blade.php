<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Talent Request Details</h1>
        <a href="{{ route('admin.talent-requests.index') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Requests</a>
    </div>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <strong class="text-gray-700">Requester:</strong> {{ $talentRequest->user->name ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">Requester Phone:</strong> {{ $talentRequest->user->phone_number ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">Talent:</strong> {{ $talentRequest->talent->name ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">Requested On:</strong> {{ $talentRequest->created_at->format('Y-m-d H:i') }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">Status:</strong>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                @switch($talentRequest->status)
                    @case('pending_admin') bg-yellow-100 text-yellow-800 @break
                    @case('pending_talent') bg-blue-100 text-blue-800 @break
                    @case('approved') bg-green-100 text-green-800 @break
                    @case('rejected_admin')
                    @case('rejected_talent') bg-red-100 text-red-800 @break
                    @default bg-gray-100 text-gray-800
                @endswitch
            ">
                {{ ucfirst(str_replace('_', ' ', $talentRequest->status)) }}
            </span>
        </div>
        <div class="mb-6">
            <strong class="block text-gray-700 mb-2">Details:</strong>
            <p class="text-gray-700 text-base">{{ $talentRequest->details ?? 'No details provided.' }}</p>
        </div>

        {{-- Admin Actions --}}
        @if ($talentRequest->status === 'pending_admin')
            <div class="flex items-center justify-start space-x-4">
                <form action="{{ route('admin.talent-requests.update', $talentRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Approve (Send to Talent)
                    </button>
                </form>
                <form action="{{ route('admin.talent-requests.update', $talentRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Reject Request
                    </button>
                </form>
            </div>
            {{-- Optional: Add field for admin comments --}}
        @endif
    </div>
</div>
</x-layouts.app>