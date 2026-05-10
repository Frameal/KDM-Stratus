<template>
    <Head title="KDM Stratus Dashboard" />

    <div class="min-h-screen bg-[#101113] text-gray-200 font-sans selection:bg-blue-500 selection:text-white flex flex-col scroll-smooth relative">
        
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-green-900/10 rounded-full blur-[80px]"></div>
        </div>

        <nav class="w-full z-50 bg-[#101113]/90 backdrop-blur-md border-b border-gray-800 h-24 flex-shrink-0 shadow-lg sticky top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    
                    <div class="flex items-center gap-8">
                        <a href="/" class="flex-shrink-0 flex items-center gap-4 hover:scale-105 transition transform cursor-pointer">
                            <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]" onerror="this.style.display='none';" />
                            <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                        </a>
                        
                        <div class="hidden md:flex space-x-6 border-l border-gray-700 pl-8">
                            <button @click="scrollTo('reservation')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Reserve PC</button>
                            <button @click="scrollTo('complaint')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Report Issue</button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="bg-[#1c1d21] border border-gray-700 text-gray-300 px-4 py-1.5 rounded-lg font-bold text-sm tracking-widest shadow-inner">
                            @{{ $page.props.auth.user.username }}
                        </div>
                        <div class="text-green-400 font-bold font-azn text-lg px-2 drop-shadow-[0_0_10px_rgba(74,222,128,0.2)]"
                             :class="{'text-red-400': Number($page.props.auth.user.balance) < 5.00}">
                            ₱{{ Number($page.props.auth.user.balance).toFixed(2) }}
                        </div>
                        <button class="bg-green-600 hover:bg-green-500 text-white px-4 py-1.5 rounded font-bold text-sm transition shadow-[0_0_15px_rgba(22,163,74,0.3)]">Top Up</button>
                        <a :href="route('profile.edit')" class="bg-[#1c1d21] border border-gray-700 hover:border-blue-500 text-white px-4 py-1.5 rounded font-bold text-sm transition shadow-lg">Account Details</a>
                        <Link :href="route('logout')" method="post" as="button" class="border border-red-500/50 hover:border-red-500 hover:bg-red-500/10 text-red-400 hover:text-red-300 px-4 py-1.5 rounded font-bold text-sm transition">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12 relative z-10">
            
            <div v-if="activeReservation" class="bg-orange-900/20 border border-orange-500/50 rounded-2xl p-6 shadow-[0_0_30px_rgba(249,115,22,0.15)] flex justify-between items-center animate-fade-in">
                <div>
                    <h3 class="font-argentum text-xl text-white uppercase tracking-widest mb-1">Active Reservation: <span class="text-orange-400">{{ activeReservation.pc_number.replace('-', ' ') }}</span></h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Please log into the physical terminal before the timer expires.</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="font-mono text-3xl text-orange-400 font-bold">{{ formattedTimer }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Time Remaining</div>
                    </div>
                    <button @click="cancelReservation(false)" class="bg-red-600/20 hover:bg-red-600 border border-red-500/50 text-red-400 hover:text-white px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition">
                        Cancel Session
                    </button>
                </div>
            </div>

            <section id="reservation" class="scroll-mt-32">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                    
                    <div class="p-6 border-b border-gray-800 bg-[#101113] flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Network Telemetry</h2>
                            <p class="text-sm text-gray-400 mt-1 font-bold">Select a branch to establish telemetry and reserve hardware.</p>
                        </div>
                        
                        <div class="relative w-full md:w-72" ref="dropdownContainer">
                            <div class="relative">
                                <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input v-model="searchQuery" @focus="openSearch" type="text" placeholder="Search branch or region..." 
                                    class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition font-bold tracking-wide">
                            </div>
                            
                            <div v-if="showDropdown" class="absolute z-50 w-full mt-2 bg-[#1c1d21] border border-gray-700 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">
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
                            <div class="flex overflow-x-auto gap-6 pb-4 snap-x custom-scrollbar">
                                <div v-for="image in getBranchImages(selectedBranch.name)" :key="image" 
                                     class="flex-shrink-0 w-[600px] h-[350px] bg-[#101113] rounded-xl border border-gray-700 overflow-hidden group cursor-pointer snap-center relative shadow-lg hover:border-blue-500 transition-colors"
                                     @click="openModal(image)">
                                    <img :src="image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500" onerror="this.style.display='none'" />
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                                        <span class="bg-blue-600 border border-blue-400 text-white font-bold px-6 py-3 rounded-lg uppercase tracking-widest shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg> Inspect Schematic
                                        </span>
                                    </div>
                                </div>
                                <div v-if="getBranchImages(selectedBranch.name).length === 0" class="w-full py-12 text-center text-gray-600 border border-gray-800 border-dashed rounded-xl font-bold uppercase tracking-widest">
                                    No schematics uploaded to the server for this branch yet.
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center justify-between">
                                Live Terminal Status ({{ filteredPcs.length }} Terminals)
                                <div class="flex gap-4 text-[10px] bg-[#1c1d21] px-4 py-2 rounded-lg border border-gray-800">
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></span> Available</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-500 shadow-[0_0_5px_#f97316]"></span> Reserved</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-[0_0_5px_#3b82f6]"></span> Occupied</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_5px_#ef4444]"></span> Maintenance</span>
                                </div>
                            </h3>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                <div v-for="pc in filteredPcs" :key="pc.id" 
                                    class="bg-[#101113] border rounded-lg p-4 flex flex-col items-center justify-between h-36 transition-all duration-300 hover:-translate-y-1"
                                    :class="{
                                        'border-green-500/50 shadow-[0_0_15px_rgba(34,197,94,0.15)] bg-[#101113]': pc.status === 'free' && (!activeReservation || activeReservation.id !== pc.id),
                                        'border-blue-500/50 bg-[#1c1d21]': pc.status === 'occupied',
                                        'border-orange-500/50 shadow-[0_0_15px_rgba(249,115,22,0.15)] bg-[#1c1d21]': pc.status === 'reserved' || (activeReservation && activeReservation.id === pc.id),
                                        'border-red-900/50 opacity-40 bg-black': pc.status === 'broken'
                                    }">
                                    
                                    <div class="text-center">
                                        <div class="font-bold text-lg tracking-widest mb-1 text-white">{{ pc.pc_number.replace('-', ' ') }}</div>
                                        
                                        <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-xs uppercase tracking-wider animate-pulse">In Use</div>
                                        <div v-else-if="pc.status === 'reserved' || (activeReservation && activeReservation.id === pc.id)" class="text-orange-400 font-bold text-xs uppercase tracking-wider animate-pulse">Reserved</div>
                                        <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-xs uppercase tracking-wider">Offline</div>
                                        <div v-else class="text-green-400 font-bold text-xs uppercase tracking-wider">Ready</div>
                                    </div>

                                    <button v-if="pc.status === 'free' && !activeReservation" @click="openReserveModal(pc)" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold py-2 rounded transition mt-2 tracking-wider uppercase shadow-[0_0_10px_rgba(37,99,235,0.3)] hover:shadow-[0_0_15px_rgba(37,99,235,0.6)]">
                                        Reserve ₱5.00
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-20 text-center text-gray-500 relative overflow-hidden">
                        <svg class="w-20 h-20 mx-auto mb-6 opacity-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="font-argentum text-2xl uppercase tracking-widest text-gray-400">Awaiting Branch Selection</p>
                        <p class="text-sm mt-3 font-bold tracking-wide">Select a branch from the search bar above to establish telemetry or reserve a PC.</p>
                    </div>
                </div>
            </section>

            <section id="complaint" class="scroll-mt-32 max-w-3xl mx-auto w-full">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 p-8 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full blur-[40px] pointer-events-none"></div>

                    <div class="mb-8 relative z-10 text-center">
                        <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Submit a Report</h2>
                        <p class="text-sm text-gray-400 mt-2 font-bold">Help us maintain enterprise standards by reporting facility or technical issues.</p>
                    </div>

                    <form @submit.prevent="submitComplaint" class="space-y-6 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Location</label>
                                <select v-model="reportForm.branch_name" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition appearance-none cursor-pointer">
                                    <option value="" disabled>Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.name">{{ branch.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Concern Type</label>
                                <select v-model="reportForm.concern_type" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition appearance-none cursor-pointer">
                                    <option value="" disabled>Select Category</option>
                                    <option>Hardware Failure (Broken Mouse/Keyboard/Monitor)</option>
                                    <option>Software Issue (Game Update/Crash)</option>
                                    <option>Network Latency (High Ping/Lag)</option>
                                    <option>Facility Cleanliness</option>
                                    <option>Noise Complaint</option>
                                    <option>Customer Service (Staff Issue)</option>
                                    <option>Others</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Details</label>
                            <textarea v-model="reportForm.details" required rows="5" placeholder="Please describe the issue in detail..." class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition resize-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-800 pt-8">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative flex items-center justify-center w-5 h-5 bg-[#1c1d21] border border-gray-600 rounded mr-3 group-hover:border-blue-500 transition">
                                    <input type="checkbox" v-model="reportForm.is_anonymous" class="absolute w-full h-full opacity-0 cursor-pointer">
                                    <svg class="w-3 h-3 text-blue-500 hidden group-has-[:checked]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-sm text-gray-400 font-bold uppercase tracking-widest group-hover:text-white transition">Submit Anonymously</span>
                            </label>

                            <button type="submit" :disabled="reportForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest px-8 py-3 rounded-lg text-sm transition shadow-[0_0_15px_rgba(37,99,235,0.4)] disabled:opacity-50">
                                Transmit Report
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </main>

        <div v-if="showModal" class="fixed inset-0 z-[100] bg-black/95 flex items-center justify-center backdrop-blur-md overflow-hidden" 
             @mousemove="onDrag" @mouseup="stopDrag" @mouseleave="stopDrag" @wheel.prevent="handleWheel">
            
            <button @click="closeModal" class="absolute top-6 right-6 z-50 text-gray-400 hover:text-white bg-[#18191c] border border-gray-700 hover:border-gray-500 rounded-full p-3 transition shadow-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" 
                 @mousedown.self="closeModal" @mousedown="startDrag">
                <img :src="activeImage" draggable="false"
                     class="max-w-[90vw] max-h-[90vh] object-contain transition-transform duration-100 ease-out origin-center" 
                     :style="{ transform: `translate(${pan.x}px, ${pan.y}px) scale(${zoomLevel})` }" />
            </div>

            <div class="absolute bottom-10 right-10 flex bg-[#18191c] border border-gray-700 rounded-lg shadow-[0_0_30px_rgba(0,0,0,0.8)] overflow-hidden z-50 select-none">
                <button @click="zoomOut" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 focus:outline-none" title="Zoom Out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                </button>
                <button @click="resetZoom" class="px-6 py-4 text-xs font-bold text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 tracking-widest uppercase focus:outline-none" title="Reset Zoom">
                    Reset
                </button>
                <button @click="zoomIn" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition focus:outline-none" title="Zoom In">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </button>
            </div>
        </div>

        <div v-if="showReserveModal" class="fixed inset-0 z-[100] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showReserveModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all animate-fade-in relative">
                
                <div class="absolute top-0 left-0 w-full h-32 bg-blue-600/10 blur-[50px] pointer-events-none"></div>

                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800 flex justify-between items-center relative z-10">
                    <h3 class="font-argentum text-xl text-white uppercase tracking-widest">Confirm Access</h3>
                    <button @click="showReserveModal = false" class="text-gray-500 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-8 space-y-6 relative z-10">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-900/30 border border-blue-500/30 mb-4 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-3xl font-bold text-white tracking-widest">{{ activePcToReserve?.pc_number.replace('-', ' ') }}</h4>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">{{ selectedBranch?.name }} Branch</p>
                    </div>
                    
                    <div class="bg-[#1c1d21] border border-gray-700 rounded-lg p-5">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider">Network Fee</span>
                            <span class="text-white font-bold tracking-widest">₱5.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-800">
                            <span class="text-gray-400 text-xs font-bold uppercase tracking-wider">Current Balance</span>
                            <span class="font-bold tracking-widest" :class="Number($page.props.auth.user.balance) < 5.00 ? 'text-red-400' : 'text-green-400'">
                                ₱{{ Number($page.props.auth.user.balance).toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <div v-if="Number($page.props.auth.user.balance) < 5.00" class="bg-red-900/20 border border-red-500/50 p-4 rounded-lg text-center">
                        <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest">Insufficient Funds. Please top up your wallet.</p>
                    </div>
                    <p v-else class="text-[10px] text-gray-500 uppercase tracking-widest text-center font-bold">
                        You will have 15 minutes to log into the physical terminal before the reservation auto-forfeits.
                    </p>
                    
                    <button v-if="Number($page.props.auth.user.balance) >= 5.00" @click="confirmReservation" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest py-3.5 rounded-lg text-sm transition shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                        Authorize Deduction
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';

const props = defineProps({
    branches: Array,
    pcs: Array,
    floorplans: {
        type: Array,
        default: () => []
    }
});

// Search & Dropdown State
const searchQuery = ref('');
const showDropdown = ref(false);
const selectedBranch = ref(null);
const dropdownContainer = ref(null);

const regionMap = {
    'cavite': ['imus', 'bacoor'],
    'bulacan': ['baliwag bulacan', 'malolos', 'sjdm'],
    'manila': ['anonas', 'españa', 'taft', 'gastambide', 'morayta'],
    'qc': ['novaliches', 'lagro', 'tandang sora'],
    'quezon city': ['novaliches', 'lagro', 'tandang sora'],
    'makati': ['comembo', 'makati evangelista', 'guadalupe']
};

const openSearch = () => {
    searchQuery.value = '';
    showDropdown.value = true;
};

const closeDropdown = (e) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(e.target)) {
        showDropdown.value = false;
        if (selectedBranch.value) {
            searchQuery.value = selectedBranch.value.name;
        }
    }
};

onMounted(() => document.addEventListener('mousedown', closeDropdown));
onUnmounted(() => document.removeEventListener('mousedown', closeDropdown));

const filteredBranches = computed(() => {
    if (!props.branches) return [];
    
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.branches;

    const targetKeywords = regionMap[query] ? regionMap[query] : [query];

    return props.branches.filter(b => {
        return targetKeywords.some(keyword => 
            b.name.toLowerCase().includes(keyword) || 
            b.address.toLowerCase().includes(keyword)
        );
    });
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

const getBranchImages = (branchName) => {
    if (!props.floorplans) return [];
    const formattedName = branchName.toLowerCase().replace(/ /g, '_');
    return props.floorplans.filter(path => path.toLowerCase().includes(formattedName));
};

// Modal, Zoom, and Pan State
const showModal = ref(false);
const activeImage = ref('');
const zoomLevel = ref(1);
const pan = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const openModal = (imgSrc) => {
    activeImage.value = imgSrc;
    resetZoom();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    activeImage.value = '';
    resetZoom();
};

const zoomIn = () => { if (zoomLevel.value < 4) zoomLevel.value += 0.25; };
const zoomOut = () => { 
    if (zoomLevel.value > 1) zoomLevel.value -= 0.25; 
    if (zoomLevel.value === 1) pan.value = { x: 0, y: 0 };
};
const resetZoom = () => { zoomLevel.value = 1; pan.value = { x: 0, y: 0 }; };

const handleWheel = (e) => {
    e.deltaY < 0 ? zoomIn() : zoomOut();
};

const startDrag = (e) => {
    if (zoomLevel.value <= 1) return; // Only allow drag if zoomed in
    isDragging.value = true;
    dragStart.value = { x: e.clientX - pan.value.x, y: e.clientY - pan.value.y };
};

const onDrag = (e) => {
    if (!isDragging.value || zoomLevel.value <= 1) return;
    pan.value = { x: e.clientX - dragStart.value.x, y: e.clientY - dragStart.value.y };
};

const stopDrag = () => { isDragging.value = false; };

// RESERVATION & TIMER LOGIC
const showReserveModal = ref(false);
const activePcToReserve = ref(null);
const activeReservation = ref(null);
const reservationTimeLeft = ref(15 * 60); // 15 minutes in seconds
let timerInterval = null;

const formattedTimer = computed(() => {
    const minutes = Math.floor(reservationTimeLeft.value / 60).toString().padStart(2, '0');
    const seconds = (reservationTimeLeft.value % 60).toString().padStart(2, '0');
    return `${minutes}:${seconds}`;
});

const openReserveModal = (pc) => {
    activePcToReserve.value = pc;
    showReserveModal.value = true;
};

const confirmReservation = () => {
    // 1. Visually lock the PC in the UI
    activeReservation.value = activePcToReserve.value;
    reservationTimeLeft.value = 15 * 60;
    showReserveModal.value = false;

    // 2. Start the Countdown Timer
    timerInterval = setInterval(() => {
        reservationTimeLeft.value--;
        if (reservationTimeLeft.value <= 0) {
            cancelReservation(true); // Auto-Forfeit
        }
    }, 1000);
};

const cancelReservation = (isAutoForfeit = false) => {
    clearInterval(timerInterval);
    activeReservation.value = null;
    
    if (isAutoForfeit) {
        alert("Reservation Auto-Forfeited: Your 15-minute grace period has expired.");
    }
};

const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};

// Report Logic
const reportForm = useForm({
    branch_name: '',
    concern_type: '',
    details: '',
    is_anonymous: false
});

const submitComplaint = () => {
    reportForm.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            alert("Report successfully transmitted to Branch Management.");
            reportForm.reset();
        }
    });
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

.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>