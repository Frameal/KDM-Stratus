<template>
    <Head title="KDM Stratus Dashboard" />

    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans selection:bg-white selection:text-black flex flex-col scroll-smooth">
        
        <nav class="w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24 flex-shrink-0 shadow-md sticky top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    
                    <div class="flex items-center gap-8">
                        <a href="/" class="flex-shrink-0 flex items-center gap-4 hover:opacity-80 transition">
                            <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                            <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                        </a>
                        
                        <div class="hidden md:flex space-x-6 border-l border-gray-700 pl-8">
                            <button @click="scrollTo('reservation')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Reserve PC</button>
                            <button @click="scrollTo('complaint')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Report Issue</button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="bg-white text-black px-4 py-1.5 rounded-lg font-bold text-sm tracking-widest shadow-[0_0_10px_rgba(255,255,255,0.2)]">
                            @{{ $page.props.auth.user.username }}
                        </div>
                        <div class="text-green-400 font-bold font-azn text-lg px-2">
                            ₱{{ Number($page.props.auth.user.balance).toFixed(2) }}
                        </div>
                        <button class="bg-green-600 hover:bg-green-500 text-white px-4 py-1.5 rounded font-bold text-sm transition">Top Up</button>
                        <a :href="route('profile.edit')" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded font-bold text-sm transition shadow-lg">Account Details</a>
                        <Link :href="route('logout')" method="post" as="button" class="border border-red-500/50 hover:border-red-500 hover:bg-red-500/10 text-red-400 hover:text-red-300 px-4 py-1.5 rounded font-bold text-sm transition">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">
            
            <section id="reservation" class="scroll-mt-32">
                <div class="bg-[#222328] rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
                    
                    <div class="p-6 border-b border-gray-800 bg-[#18191c] flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Network Telemetry</h2>
                            <p class="text-sm text-gray-400 mt-1">Select a branch to view floorplans and reserve hardware.</p>
                        </div>
                        
                        <div class="relative w-full md:w-72" ref="dropdownContainer">
                            <div class="relative">
                                <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input v-model="searchQuery" @focus="openSearch" type="text" placeholder="Search branch..." 
                                    class="w-full bg-[#101113] border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:border-blue-500 transition font-bold tracking-wide">
                            </div>
                            
                            <div v-if="showDropdown" class="absolute z-50 w-full mt-2 bg-[#1c1d21] border border-gray-700 rounded-lg shadow-2xl max-h-60 overflow-y-auto">
                                <div v-if="filteredBranches.length === 0" class="p-4 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">
                                    No branches found
                                </div>
                                <button v-for="branch in filteredBranches" :key="branch.id" @mousedown.prevent="selectBranch(branch)"
                                    class="w-full text-left px-4 py-3 hover:bg-blue-600 hover:text-white transition border-b border-gray-800 last:border-0 cursor-pointer">
                                    <div class="font-bold uppercase tracking-widest">{{ branch.name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ branch.address }}</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedBranch" class="p-6 space-y-8">
                        
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Branch Schematics</h3>
                            <div class="flex overflow-x-auto gap-6 pb-4 snap-x hide-scrollbar">
                                <div v-for="image in getBranchImages(selectedBranch.name)" :key="image" 
                                     class="flex-shrink-0 w-[600px] h-[350px] bg-[#101113] rounded-xl border border-gray-700 overflow-hidden group cursor-pointer snap-center relative"
                                     @click="openModal(image)">
                                    <img :src="image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-300" onerror="this.style.display='none'" />
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                        <span class="bg-blue-600 text-white font-bold px-4 py-2 rounded-lg uppercase tracking-widest shadow-lg flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand View
                                        </span>
                                    </div>
                                </div>
                                <div v-if="getBranchImages(selectedBranch.name).length === 0" class="w-full py-12 text-center text-gray-600 border border-gray-800 border-dashed rounded-xl">
                                    No schematics uploaded to the server for this branch yet.
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center justify-between">
                                Live Terminal Status ({{ filteredPcs.length }} Terminals)
                                <div class="flex gap-4 text-[10px]">
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Available</span>
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Occupied</span>
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Maintenance</span>
                                </div>
                            </h3>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                <div v-for="pc in filteredPcs" :key="pc.id" 
                                    class="bg-[#18191c] border rounded-lg p-4 flex flex-col items-center justify-between h-36 transition-all duration-300 hover:-translate-y-1"
                                    :class="{
                                        'border-green-500/50 shadow-[0_0_15px_rgba(34,197,94,0.1)]': pc.status === 'free',
                                        'border-blue-500/50': pc.status === 'occupied',
                                        'border-red-500 opacity-50': pc.status === 'broken'
                                    }">
                                    
                                    <div class="text-center">
                                        <div class="font-bold text-lg tracking-widest mb-1 text-white">{{ pc.pc_number.replace('-', ' ') }}</div>
                                        
                                        <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-xs uppercase tracking-wider animate-pulse">In Use</div>
                                        <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-xs uppercase tracking-wider">Offline</div>
                                        <div v-else class="text-green-400 font-bold text-xs uppercase tracking-wider">Ready</div>
                                    </div>

                                    <button v-if="pc.status === 'free'" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold py-2 rounded transition mt-2 tracking-wider uppercase shadow-lg">
                                        Reserve ₱5.00
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-16 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="font-bold uppercase tracking-widest">Awaiting Database Link</p>
                        <p class="text-sm mt-2">Select a branch from the terminal above to establish telemetry.</p>
                    </div>
                </div>
            </section>

            <section id="complaint" class="scroll-mt-32 max-w-3xl">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 p-8 shadow-xl">
                    <div class="mb-8">
                        <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Submit a Report</h2>
                        <p class="text-sm text-gray-400 mt-1">Help us maintain enterprise standards by reporting facility or technical issues.</p>
                    </div>

                    <form @submit.prevent="submitComplaint" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Location</label>
                                <select class="w-full bg-[#101113] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition appearance-none">
                                    <option value="" disabled selected>Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.name">{{ branch.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Concern Type</label>
                                <select class="w-full bg-[#101113] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition appearance-none">
                                    <option value="" disabled selected>Select Category</option>
                                    <option>Hardware Failure (Broken Mouse/Keyboard/Monitor)</option>
                                    <option>Software Issue (Game Update/Crash)</option>
                                    <option>Network Latency (High Ping/Lag)</option>
                                    <option>Facility Cleanliness</option>
                                    <option>Noise Complaint</option>
                                    <option>Customer Service (Staff Issue)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Details</label>
                            <textarea rows="4" placeholder="Please describe the issue..." class="w-full bg-[#101113] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition resize-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-800 pt-6">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative flex items-center justify-center w-5 h-5 bg-[#101113] border border-gray-600 rounded mr-3 group-hover:border-blue-500 transition">
                                    <input type="checkbox" class="absolute w-full h-full opacity-0 cursor-pointer">
                                    <svg class="w-3 h-3 text-blue-500 hidden group-has-[:checked]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-sm text-gray-400 font-bold uppercase tracking-widest group-hover:text-white transition">Submit Anonymously</span>
                            </label>

                            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest px-8 py-3 rounded-lg text-sm transition shadow-lg">
                                Transmit Report
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </main>

        <div v-if="showModal" class="fixed inset-0 z-[100] bg-black/95 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="closeModal">
            <button @click="closeModal" class="absolute top-6 right-6 text-gray-400 hover:text-white bg-gray-900 rounded-full p-2 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img :src="activeImage" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl border border-gray-700" />
        </div>
        
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    branches: Array,
    pcs: Array,
    floorplans: {
        type: Array,
        default: () => []
    }
});

const searchQuery = ref('');
const showDropdown = ref(false);
const selectedBranch = ref(null);
const showModal = ref(false);
const activeImage = ref('');
const dropdownContainer = ref(null);

// SEARCH LOGIC FIX
const openSearch = () => {
    searchQuery.value = ''; // Clears the input so the user can type fresh
    showDropdown.value = true;
};

// CLOSES DROPDOWN IF CLICKED OUTSIDE
const closeDropdown = (e) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(e.target)) {
        showDropdown.value = false;
        // If they didn't pick anything, put the old branch name back
        if (selectedBranch.value) {
            searchQuery.value = selectedBranch.value.name;
        }
    }
};

onMounted(() => document.addEventListener('mousedown', closeDropdown));
onUnmounted(() => document.removeEventListener('mousedown', closeDropdown));

const filteredBranches = computed(() => {
    if (!props.branches) return [];
    return props.branches.filter(b => b.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const filteredPcs = computed(() => {
    if (!selectedBranch.value || !props.pcs) return [];
    return props.pcs.filter(pc => pc.branch_id === selectedBranch.value.id);
});

const selectBranch = (branch) => {
    selectedBranch.value = branch;
    searchQuery.value = branch.name;
    showDropdown.value = false;
};

// DYNAMIC FLOORPLAN DETECTOR
const getBranchImages = (branchName) => {
    if (!props.floorplans) return [];
    // Formats "Valenzuela" to "valenzuela", or "San Bartolome" to "san_bartolome" to match filenames
    const formattedName = branchName.toLowerCase().replace(/ /g, '_');
    return props.floorplans.filter(path => path.toLowerCase().includes(formattedName));
};

const openModal = (imgSrc) => {
    activeImage.value = imgSrc;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    activeImage.value = '';
};

const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const submitComplaint = () => {
    alert("Report interface ready. Backend transmission logic pending implementation.");
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

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>