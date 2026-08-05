@if(session('error') || $errors->any())
    <div style="right: 50%;left: 50%;transform: translate(-50%, -50%); justify-content: space-around;" class="fixed bottom-4 right-4 z-50 max-w-sm w-full bg-red-500 text-white px-4 py-3 rounded-lg shadow-xl flex items-center border border-red-600"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="flex items-center">
            <p class="text-center text-sm font-medium">
                {{ session('error') ?? $errors->first() }}
            </p>
        </div>
        <button @click="show = false" class="ml-4 text-white hover:text-red-200 focus:outline-none cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
