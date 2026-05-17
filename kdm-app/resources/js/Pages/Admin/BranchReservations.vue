<template>
    <Head title="Active Reservations - KDM Stratus" />

    <AdminLayout>
        <template #header>Active Network Reservations</template>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4 border-green-500">
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Incoming Customers</h3>
                
                <div class="flex items-center gap-4">
                    <button @click="generatePDF" class="bg-[#1c1d21] hover:bg-blue-900/40 border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-blue-400 px-4 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export PDF Report
                    </button>
                </div>
            </div>
            
            <div class="p-6 overflow-x-auto">
                <div v-if="reservations.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-12">
                    No active reservations for this branch.
                </div>

                <div v-else class="w-full text-left text-gray-400 text-sm min-w-[800px]">
                    <div class="grid grid-cols-[1fr_1.5fr_1fr_1.5fr_1fr_2fr] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-[10px]">
                        <div>Hardware</div><div>Customer Username</div><div>Duration</div><div>Time Remaining</div><div>Fee Paid</div><div class="text-right">Actions</div>
                    </div>
                    
                    <div v-for="res in reservations" :key="res.id" class="grid grid-cols-[1fr_1.5fr_1fr_1.5fr_1fr_2fr] items-center py-4 border-b border-gray-800/50 hover:bg-[#222328] transition">
                        <div class="text-white font-bold font-mono">{{ res.pc?.pc_number }}</div>
                        <div class="text-blue-400 font-bold text-xs">@{{ res.user?.username }}</div>
                        <div class="text-gray-400 font-mono text-[10px] uppercase">{{ res.duration_minutes }} Mins</div>
                        
                        <div class="font-mono font-bold" :class="res.status === 'expired' || calculateTimeLeft(res.expires_at) === 'EXPIRED' ? 'text-red-500' : 'text-orange-400'">
                            {{ calculateTimeLeft(res.expires_at) }}
                        </div>
                        
                        <div class="text-green-400 font-mono font-bold text-xs">₱{{ Number(res.fee_paid).toFixed(2) }}</div>
                        
                        <div class="text-right flex justify-end gap-2">
                            <button v-if="res.status === 'active' && calculateTimeLeft(res.expires_at) !== 'EXPIRED'" 
                                    @click="openCancelModal(res, false)" 
                                    class="bg-[#1c1d21] hover:bg-gray-800 border border-gray-600 text-gray-300 px-3 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition whitespace-nowrap">
                                Cancel
                            </button>
                            
                            <button @click="openCancelModal(res, true)" 
                                    class="bg-red-900/30 hover:bg-red-900/60 border border-red-500/50 text-red-400 px-3 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition shadow-lg whitespace-nowrap">
                                {{ res.status === 'expired' || calculateTimeLeft(res.expires_at) === 'EXPIRED' ? 'Refund Only' : 'Cancel & Refund' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        <div v-if="showCancelModal" class="fixed inset-0 z-[1000] bg-black/95 flex items-center justify-center p-4 backdrop-blur-md" @click.self="showCancelModal = false">
            <div class="bg-[#18191c] border border-red-900/50 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-center items-center"><h3 class="font-argentum text-xl text-red-500 uppercase tracking-widest">Confirm Action</h3></div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">
                        {{ willRefund ? 'Refund ₱' + Number(activeReservation.fee_paid).toFixed(2) + ' to @' + activeReservation.user?.username + '?' : 'Force cancel this session WITHOUT issuing a refund?' }}
                    </p>
                    <p v-if="!willRefund" class="text-[10px] text-red-400 font-bold uppercase tracking-widest bg-red-900/10 p-3 rounded border border-red-900/30">The customer will lose their money and their hardware access.</p>
                    
                    <div class="flex gap-4 pt-2">
                        <button @click="showCancelModal = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Abort</button>
                        <button @click="executeCancel" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-lg text-xs">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ reservations: Array });
const toast = ref({ show: false, message: '' });

const now = ref(new Date().getTime());
let syncInterval = null;

onMounted(() => { syncInterval = setInterval(() => { now.value = new Date().getTime(); }, 1000); });
onUnmounted(() => { clearInterval(syncInterval); });

const calculateTimeLeft = (expiresAt) => {
    const safeDateString = expiresAt.replace(' ', 'T');
    const diffInSeconds = Math.floor((new Date(safeDateString).getTime() - now.value) / 1000);
    if (diffInSeconds <= 0) return 'EXPIRED';
    return `${Math.floor(diffInSeconds / 60).toString().padStart(2, '0')}:${(diffInSeconds % 60).toString().padStart(2, '0')}`;
};

// CUSTOM MODAL LOGIC
const showCancelModal = ref(false);
const activeReservation = ref(null);
const willRefund = ref(false);

const openCancelModal = (res, refund) => {
    activeReservation.value = res;
    willRefund.value = refund;
    showCancelModal.value = true;
};

const executeCancel = () => {
    router.post(route('branch.reservations.cancel'), { reservation_id: activeReservation.value.id, refund: willRefund.value }, { 
        preserveScroll: true,
        onSuccess: () => { 
            showCancelModal.value = false;
            toast.value = { show: true, message: "Action executed and logged." };
            setTimeout(() => { toast.value.show = false; }, 4000);
        }
    });
};

const generatePDF = () => {
    // THIS LINE SECURELY LOGS THE PDF EXPORT TO THE DATABASE
    axios.post(route('branch.log.export'), { details: 'Exported Active Reservations PDF Report' });

    const printWindow = window.open('', '_blank');
    let html = `
        <html><head><title>Active Reservations Report</title>
        <style>body { font-family: sans-serif; padding: 40px; } table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; } th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }</style>
        </head><body><h2>KDM Stratus - Active Reservations</h2>
        <table><tr><th>Hardware</th><th>Customer</th><th>Duration</th><th>Fee Paid</th></tr>
    `;
    props.reservations.forEach(res => {
        html += `<tr><td>${res.pc?.pc_number}</td><td>@${res.user?.username}</td><td>${res.duration_minutes} Mins</td><td>₱${Number(res.fee_paid).toFixed(2)}</td></tr>`;
    });
    html += `</table></body></html>`;
    printWindow.document.write(html);
    printWindow.document.close();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(50px); }
</style>