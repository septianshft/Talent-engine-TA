<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Talent Request Details</h1>
        <a href="{{ route('talent.requests.index') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Requests</a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <strong class="text-gray-700">From:</strong> {{ $talentRequest->requestingUser->name ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">User Phone:</strong> {{ $talentRequest->requestingUser->phone_number ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong class="text-gray-700">Received:</strong> {{ $talentRequest->created_at->format('Y-m-d H:i') }}
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
                {{ Str::title(str_replace('_', ' ', $talentRequest->status)) }}
            </span>
        </div>
        <div class="mb-6">
            <strong class="block text-gray-700 mb-2">Details:</strong>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $talentRequest->details }}</p>
        </div>

        {{-- Action buttons if status is pending_talent --}}
        @if ($talentRequest->status === 'pending_talent')
            <hr class="my-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Respond to Request</h2>
            <form action="{{ route('talent.requests.update', $talentRequest) }}" method="POST">
                @csrf
                @method('PATCH')
                
                {{-- Optional: Add a comment field here if talents can comment --}}

                <div class="flex items-center space-x-4">
                    <button type="submit" name="action" value="approve" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Approve Request
                    </button>
                    <button type="submit" name="action" value="reject" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="return confirm('Are you sure you want to reject this request?');">
                        Reject Request
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>
</x-layouts.app>