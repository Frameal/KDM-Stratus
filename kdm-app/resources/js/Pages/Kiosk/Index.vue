<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans selection:bg-white selection:text-black flex flex-col">
        
        <header class="bg-[#222328] border-b border-gray-800 shadow-md py-4 px-6 sm:px-10 flex justify-between items-center sticky top-0 z-50">
            <div class="flex items-center gap-4">
                <img src="/images/logo.png" alt="KDM Logo" class="h-10 w-auto object-contain" onerror="this.style.display='none';" />
                <span class="font-azn tracking-widest text-2xl text-white">KDM STRATUS</span>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="bg-[#18191c] border border-gray-700 px-4 py-2 rounded-lg flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Balance</span>
                    <span class="font-argentum text-xl text-green-400">₱{{ $page.props.auth.user.balance }}</span>
                    <button class="ml-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded transition">Top Up</button>
                </div>
                
                <div class="text-right hidden sm:block">
                    <div class="font-bold text-white text-sm">{{ $page.props.auth.user.name }}</div>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">@{{ $page.props.auth.user.username }}</div>
                </div>
                
            <Link :href="route('logout')" method="post" as="button" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </Link>
            </div>
        </header>

        <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">
            
            <div class="text-center mb-10">
                <h1 class="font-argentum text-4xl text-white uppercase mb-2">Reserve a PC</h1>
                <p class="text-gray-400">Select a branch below to view real-time availability.</p>
            </div>

            <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg mb-10">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Select Location</label>
                <select v-model="selectedBranchId" class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition appearance-none">
                    <option disabled value="">-- Choose a KDM Branch --</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                        {{ branch.name }} 
                    </option>
                </select>
            </div>

            <div v-if="selectedBranchId" class="animate-fade-in">
                <div class="flex justify-between items-end mb-6">
                    <h2 class="font-argentum text-2xl text-white uppercase">Live Telemetry</h2>
                    <div class="flex gap-4 text-xs font-bold uppercase tracking-widest">
                        <span class="text-green-400 flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-green-400"></div> Free</span>
                        <span class="text-blue-400 flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-blue-400"></div> Occupied</span>
                        <span class="text-red-400 flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-red-400"></div> Broken</span>
                    </div>
                </div>

                <div v-if="filteredPcs.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <div v-for="pc in filteredPcs" :key="pc.id" 
                         class="bg-[#222328] p-6 rounded-xl border border-gray-800 relative transition-all duration-300 shadow-lg group"
                         :class="statusBorder(pc.status)">
                        
                        <h3 class="text-2xl font-bold text-white mb-6">{{ pc.pc_number }}</h3>
                        
                        <div class="mt-auto">
                            <button v-if="pc.status === 'free'" class="w-full py-2 bg-white hover:bg-gray-200 text-black rounded text-sm font-bold uppercase tracking-wider transition transform group-hover:-translate-y-1">
                                Reserve (₱5)
                            </button>
                            
                            <div v-else-if="pc.status === 'occupied'" class="w-full py-2 bg-[#18191c] rounded text-center">
                                <span class="text-blue-400 font-bold uppercase tracking-widest text-xs">In Use</span>
                            </div>

                            <div v-else-if="pc.status === 'broken'" class="w-full py-2 bg-red-900/20 rounded text-center">
                                <span class="text-red-500 font-bold uppercase tracking-widest text-xs">Offline</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-[#222328] rounded-xl border border-gray-800">
                    <p class="text-gray-500 font-bold tracking-widest uppercase">No PCs actively reporting telemetry in this branch.</p>
                </div>
            </div>

        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    branches: Array,
    pcs: Array
});

// Holds the currently selected branch from the dropdown
const selectedBranchId = ref("");

// Dynamically filters the PCs array based on the selected branch
const filteredPcs = computed(() => {
    if (!selectedBranchId.value) return [];
    return props.pcs.filter(pc => pc.branch_id === selectedBranchId.value);
});

// Dynamic Tailwind border colors based on PC status
const statusBorder = (status) => {
    switch (status) {
        case 'free': return 'border-t-4 border-t-green-500 hover:border-green-500';
        case 'occupied': return 'border-t-4 border-t-blue-500 opacity-60';
        case 'broken': return 'border-t-4 border-t-red-500 opacity-40';
        default: return 'border-gray-800';
    }
};
</script>

<style>
@font-face {
    font-family: 'ArgentumNovus';
    src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype');
    font-weight: 600;
}
@font-face {
    font-family: 'AZNUnified';
    src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype');
    font-weight: normal;
    font-style: italic;
}
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>