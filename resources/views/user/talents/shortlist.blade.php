<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                My Shortlist
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Manage your shortlisted talents</p>
        </div>
        <div class="flex gap-3 mt-4 lg:mt-0">
            <a href="{{ route('user.talents.index') }}"
               class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Back to Search
            </a>
            @if($shortlists->count() >= 2)
            <button onclick="compareAllShortlisted()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l3-3l3 3v13M9 19H6a2 2 0 01-2-2V9a2 2 0 012-2h3m4 0h3a2 2 0 012 2v8a2 2 0 01-2 2h-3M9 19v-6"></path>
                </svg>
                Compare All
            </button>
            @endif
        </div>
    </div>

    <!-- List Navigation -->
    @if($listNames->count() > 1)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">My Lists</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($listNames as $name)
                <a href="{{ route('user.talents.shortlist', ['list' => $name]) }}"
                   class="px-4 py-2 rounded-lg transition-colors {{ $listName === $name ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ ucfirst($name) }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Shortlisted Talents -->
    @if($shortlists->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($shortlists as $shortlist)
                @php
                    $talent = $shortlist->talent;
                    $priorityConfig = [
                        0 => ['label' => 'Normal', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                        1 => ['label' => 'High', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                        2 => ['label' => 'Critical', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300']
                    ];
                    $priority = $priorityConfig[$shortlist->priority] ?? $priorityConfig[0];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Priority Indicator -->
                    <div class="h-2 {{ $shortlist->priority === 2 ? 'bg-red-500' : ($shortlist->priority === 1 ? 'bg-yellow-500' : 'bg-gray-300') }}"></div>

                    <div class="p-6">
                        <!-- Talent Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="relative">
                                    @if($talent->profile_picture && Storage::exists('public/' . $talent->profile_picture))
                                        <img src="{{ Storage::url($talent->profile_picture) }}" alt="{{ $talent->name }}"
                                             class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $talent->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $talent->email }}</p>
                                </div>
                            </div>

                            <!-- Priority Badge -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $priority['color'] }}">
                                {{ $priority['label'] }}
                            </span>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm">{{ $talent->domicile_city ?? 'Unknown' }}, {{ $talent->domicile_country ?? 'Unknown' }}</span>
                        </div>

                        <!-- Top Skills -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Top Skills</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach($talent->competencies->take(3) as $competency)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $competency->name }}
                                    </span>
                                @endforeach
                                @if($talent->competencies->count() > 3)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        +{{ $talent->competencies->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($shortlist->notes)
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $shortlist->notes }}</p>
                        </div>
                        @endif

                        <!-- Added Date -->
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Added {{ $shortlist->created_at->diffForHumans() }}
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('user.talents.show', $talent->id) }}"
                               class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors text-center">
                                View Profile
                            </a>
                            <button onclick="editShortlistItem({{ $shortlist->id }}, '{{ $shortlist->notes }}', {{ $shortlist->priority }})"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="removeFromShortlist({{ $talent->id }}, '{{ $listName }}')"
                                    class="px-3 py-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No shortlisted talents</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Start building your shortlist by adding talents from the search page.
            </p>
            <a href="{{ route('user.talents.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Find Talents
            </a>
        </div>
    @endif
</div>

<!-- Edit Shortlist Modal -->
<div id="edit-shortlist-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Shortlist Item</h3>
            <form id="edit-shortlist-form">
                <input type="hidden" id="edit-talent-id" name="talent_id">
                <div class="mb-4">
                    <label for="edit-priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Priority Level
                    </label>
                    <select id="edit-priority" name="priority" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="0">Normal</option>
                        <option value="1">High</option>
                        <option value="2">Critical</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="edit-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea id="edit-notes" name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Add notes about this talent..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function compareAllShortlisted() {
    const talentIds = @json($shortlists->pluck('talent.id'));
    if (talentIds.length >= 2) {
        const compareUrl = `{{ route('user.talents.compare') }}?talents=${talentIds.slice(0, 4).join(',')}`;
        window.open(compareUrl, '_blank');
    }
}

function editShortlistItem(talentId, notes, priority) {
    document.getElementById('edit-talent-id').value = talentId;
    document.getElementById('edit-notes').value = notes || '';
    document.getElementById('edit-priority').value = priority;
    document.getElementById('edit-shortlist-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-shortlist-modal').classList.add('hidden');
    document.getElementById('edit-shortlist-form').reset();
}

function removeFromShortlist(talentId, listName) {
    if (!confirm('Are you sure you want to remove this talent from your shortlist?')) {
        return;
    }

    fetch(`/talents/${talentId}/shortlist`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            list_name: listName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Talent removed from shortlist!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Failed to remove talent', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Handle edit form submission
document.getElementById('edit-shortlist-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const talentId = formData.get('talent_id');

    fetch(`/talents/${talentId}/shortlist`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            showNotification('Shortlist updated successfully!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Failed to update shortlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
});

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    notification.classList.add(bgColor, 'text-white');
    notification.textContent = message;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
</x-layouts.app>
