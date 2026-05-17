<template>
    <Head title="Terminal Management - KDM Stratus" />

    <AdminLayout>
        <template #header>Terminal Grid Control</template>

        <div class="space-y-8">
            
            <div v-if="branch" class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl p-6">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Branch Schematics ({{ branch.name }})</h3>
                
                <div class="flex overflow-x-auto gap-6 pb-4 snap-x custom-scrollbar">
                    <div v-for="image in getBranchImages(branch.name)" :key="image" 
                         class="flex-shrink-0 w-[600px] h-[350px] bg-[#101113] rounded-xl border border-gray-700 overflow-hidden group cursor-pointer snap-center relative shadow-lg hover:border-blue-500 transition-colors"
                         @click="openModal(image)">
                        <img :src="image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500" onerror="this.style.display='none'" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                            <span class="bg-blue-600 border border-blue-400 text-white font-bold px-6 py-3 rounded-lg uppercase tracking-widest shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg> Inspect Schematic
                            </span>
                        </div>
                    </div>
                    <div v-if="getBranchImages(branch.name).length === 0" class="w-full py-12 text-center text-gray-600 border border-gray-800 border-dashed rounded-xl font-bold uppercase tracking-widest">
                        No schematics uploaded to the server for this branch yet.
                    </div>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                    <div>
                        <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Local Hardware Map</h3>
                        <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-widest">Live database sync active.</p>
                    </div>
                    <button @click="showAddModal = true" class="bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold px-4 py-2.5 rounded uppercase tracking-widest transition shadow-lg">
                        + Register New PC
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-8 gap-4">
                        <div v-for="pc in pcs" :key="pc.id" @click="openPcModal(pc)"
                            class="bg-[#101113] border rounded-lg p-4 flex flex-col items-center justify-center h-28 cursor-pointer transition-all duration-300 hover:scale-105"
                            :class="getGridStyle(pc)">
                            <div class="text-center">
                                <div class="font-bold text-sm tracking-widest mb-1 text-white">{{ pc.pc_number }}</div>
                                <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-[10px] uppercase">In Use</div>
                                <div v-else-if="pc.status === 'reserved'" class="text-orange-400 font-bold text-[10px] uppercase">Reserved</div>
                                <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-[10px] uppercase">Offline</div>
                                <div v-else class="text-green-400 font-bold text-[10px] uppercase">Ready</div>
                                
                                <div v-if="getReservationForPc(pc)" class="text-[9px] text-gray-400 mt-2 truncate max-w-[80px]">@{{ getReservationForPc(pc).user.username }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        
        <div v-if="showModal" class="fixed inset-0 z-[999] bg-black/95 flex items-center justify-center backdrop-blur-md overflow-hidden" 
             @mousemove="onDrag" @mouseup="stopDrag" @mouseleave="stopDrag" @wheel.prevent="handleWheel">
            
            <button @click="closeModal" class="absolute top-6 right-6 z-[1000] text-gray-400 hover:text-white bg-[#18191c] border border-gray-700 hover:border-gray-500 rounded-full p-3 transition shadow-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" 
                 @mousedown.self="closeModal" @mousedown="startDrag">
                <img :src="activeImage" draggable="false"
                     class="max-w-[90vw] max-h-[90vh] object-contain transition-transform duration-100 ease-out origin-center" 
                     :style="{ transform: `translate(${pan.x}px, ${pan.y}px) scale(${zoomLevel})` }" />
            </div>

            <div class="absolute bottom-10 right-10 flex bg-[#18191c] border border-gray-700 rounded-lg shadow-[0_0_30px_rgba(0,0,0,0.8)] overflow-hidden z-[1000] select-none">
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

        <div v-if="showAddModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showAddModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Register Terminal</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitNewPc">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Terminal Identifier</label>
                        <input type="text" v-model="addForm.pc_number" placeholder="e.g., PC-69" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white font-bold tracking-widest outline-none focus:border-blue-500 mb-6" />
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white uppercase transition">Cancel</button>
                            <button type="submit" :disabled="addForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-6 py-2 rounded uppercase tracking-widest transition">Save Hardware</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="activePc" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="activePc = null">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Terminal: {{ activePc.pc_number }}</h3>
                </div>
                <div class="p-6 space-y-6">
                    
                    <div v-if="activePc.status === 'reserved' || activePc.status === 'occupied'" class="bg-orange-900/20 border border-orange-500/30 p-4 rounded-lg">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-3">Active Session Options</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="cancelSession(false)" class="w-full bg-[#1c1d21] border border-gray-700 hover:border-red-500 text-gray-300 hover:text-red-400 text-[10px] font-bold py-3 rounded uppercase tracking-widest transition">Cancel (No Refund)</button>
                            <button @click="cancelSession(true)" class="w-full bg-red-600 hover:bg-red-500 text-white text-[10px] font-bold py-3 rounded uppercase tracking-widest transition shadow-lg">Cancel & Refund</button>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Live Hardware Status Override</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="updateStatus('free')" class="border border-green-500/50 text-green-400 text-xs font-bold py-3 rounded uppercase transition hover:bg-green-500/10" :class="{'bg-green-500/20': activePc.status === 'free'}">Set Free</button>
                            <button @click="updateStatus('broken')" class="border border-red-500/50 text-red-400 text-xs font-bold py-3 rounded uppercase transition hover:bg-red-500/10" :class="{'bg-red-500/20': activePc.status === 'broken'}">Mark Broken</button>
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-6 text-center">
                        <button @click="showDeleteConfirm = true; activePc = null" class="text-[10px] text-gray-500 hover:text-red-500 font-bold uppercase tracking-widest transition">
                            Permanently Delete Terminal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDeleteConfirm" class="fixed inset-0 z-[1000] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showDeleteConfirm = false">
            <div class="bg-[#18191c] border border-red-900/50 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-center items-center">
                    <h3 class="font-argentum text-xl text-red-500 uppercase tracking-widest flex items-center gap-2">Warning</h3>
                </div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">Permanently delete this terminal?</p>
                    <p class="text-xs text-red-400 font-bold uppercase tracking-widest bg-red-900/10 p-3 rounded border border-red-900/30">This action destroys all historical data for this PC.</p>
                    <div class="flex gap-4 pt-2">
                        <button @click="showDeleteConfirm = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Cancel</button>
                        <button @click="executeDelete" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-lg text-xs">Delete It</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'; // Added onMounted, onUnmounted
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ 
    branch: Object, pcs: Array, reservations: Array, floorplans: Array 
});

// NEW: WEBSOCKET LISTENER
onMounted(() => {
    // Listen to the specific channel for this branch
    window.Echo.channel(`branch.${props.branch.id}`)
        .listen('PcStatusUpdated', (e) => {
            // When an event hits, find the PC on the screen and update its status instantly!
            const pcIndex = props.pcs.findIndex(p => p.id === e.pc.id);
            if (pcIndex !== -1) {
                props.pcs[pcIndex].status = e.pc.status;
            }
        });
});

onUnmounted(() => {
    // Clean up the connection when we leave the page
    window.Echo.leave(`branch.${props.branch.id}`);
});

const activePc = ref(null);
const showAddModal = ref(false);
const showDeleteConfirm = ref(false);
const pcToDeleteId = ref(null);
const addForm = useForm({ pc_number: '' });

// SCHEMATICS IMAGE VIEWER LOGIC
const showModal = ref(false);
const activeImage = ref('');
const zoomLevel = ref(1);
const pan = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const getBranchImages = (branchName) => {
    if (!props.floorplans || !branchName) return [];
    const formattedName = branchName.toLowerCase().replace(/ /g, '_');
    return props.floorplans.filter(path => path.toLowerCase().includes(formattedName));
};

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
    if (zoomLevel.value <= 1) return;
    isDragging.value = true;
    dragStart.value = { x: e.clientX - pan.value.x, y: e.clientY - pan.value.y };
};

const onDrag = (e) => {
    if (!isDragging.value || zoomLevel.value <= 1) return;
    pan.value = { x: e.clientX - dragStart.value.x, y: e.clientY - dragStart.value.y };
};

const stopDrag = () => { isDragging.value = false; };

// TERMINAL GRID LOGIC
const getReservationForPc = (pc) => {
    return props.reservations.find(res => res.pc_id === pc.id);
};

const getGridStyle = (pc) => {
    if (pc.status === 'free') return 'border-green-500/50 hover:border-green-400';
    if (pc.status === 'occupied') return 'border-blue-500/50 bg-[#1c1d21]';
    if (pc.status === 'reserved') return 'border-orange-500/50 bg-[#1c1d21]';
    return 'border-red-900/50 opacity-50 bg-black';
};

const openPcModal = (pc) => { 
    activePc.value = pc; 
    pcToDeleteId.value = pc.id; 
};

const submitNewPc = () => {
    addForm.post(route('branch.terminals.store'), { onSuccess: () => { showAddModal.value = false; addForm.reset(); }});
};

const updateStatus = (newStatus) => {
    router.patch(route('pcs.update_status', activePc.value.id), { status: newStatus }, { preserveScroll: true, onSuccess: () => activePc.value = null });
};

const cancelSession = (refund) => {
    const res = getReservationForPc(activePc.value);
    if (!res) return;
    router.post(route('branch.reservations.cancel'), { reservation_id: res.id, refund: refund }, { preserveScroll: true, onSuccess: () => activePc.value = null });
};

const executeDelete = () => {
    router.delete(route('branch.terminals.destroy', pcToDeleteId.value), { preserveScroll: true, onSuccess: () => showDeleteConfirm.value = false });
};
</script>

<style scoped>
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
</style>