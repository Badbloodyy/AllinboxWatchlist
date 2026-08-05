@if(session('success'))
    <div style="right: 50%;left: 50%;transform: translate(-50%, -50%); justify-content: space-around;" class="fixed bottom-4 z-50 max-w-sm w-full bg-green-500 text-white px-4 py-3 rounded-lg shadow-xl flex items-center justify-between border border-green-600"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-center text-sm font-medium">
                {{ session('success') }}
            </p>
        </div>
        <button @click="show = false" class="ml-4 text-white hover:text-green-200 focus:outline-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
