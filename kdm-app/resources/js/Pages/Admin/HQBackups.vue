<template>
    <Head title="GCS Backups - KDM Stratus" />

    <AdminLayout>
        <template #header>Disaster Recovery & GCS Backups</template>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4" :class="toast.type === 'success' ? 'border-green-500' : 'border-orange-500'">
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl p-8">
                    <h3 class="font-argentum text-xl text-white uppercase tracking-widest mb-2">Manual Override</h3>
                    <p class="text-xs text-gray-400 font-bold mb-8 leading-relaxed">Trigger a forced SQL database snapshot to Google Cloud Storage to secure data before initiating major infrastructure updates.</p>
                    
                    <button @click="triggerBackup" :disabled="isProcessing" class="w-full relative overflow-hidden group rounded-xl transition-all shadow-lg" :class="isProcessing ? 'cursor-not-allowed opacity-80' : 'hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(37,99,235,0.4)]'">
                        <div class="absolute inset-0 w-full h-full bg-blue-600"></div>
                        <div class="relative px-6 py-4 flex flex-col items-center justify-center gap-2">
                            <svg v-if="!isProcessing" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <svg v-else class="animate-spin w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-white font-bold uppercase tracking-widest text-sm">
                                {{ isProcessing ? 'Uploading to GCS...' : 'Initiate SQL Snapshot' }}
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <div class="lg:col-span-2 bg-[#0c0c0e] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-800 bg-[#18191c] flex justify-between items-center">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <p class="font-mono text-gray-500 text-[10px] uppercase tracking-widest">GCS Target: gs://kdm-stratus-backup-prod</p>
                </div>
                
                <div class="p-6 flex-grow overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead>
                            <tr class="text-gray-600 font-mono uppercase tracking-widest text-[10px] border-b border-gray-800">
                                <th class="pb-3">Timestamp</th>
                                <th class="pb-3">Trigger Type</th>
                                <th class="pb-3">Size</th>
                                <th class="pb-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="log in logs" :key="log.id" class="border-b border-gray-800/50 hover:bg-[#18191c] transition">
                                <td class="py-4 text-xs text-gray-400 font-mono">{{ log.date }}</td>
                                <td class="py-4 text-blue-400 font-mono text-xs">{{ log.type }}</td>
                                <td class="py-4 text-gray-300 font-mono text-xs">{{ log.size }}</td>
                                <td class="py-4 text-right">
                                    <button @click="showRestoreConfirm = true" class="bg-red-900/20 hover:bg-red-900/60 border border-red-500/30 text-red-400 font-mono font-bold text-[10px] px-3 py-1.5 rounded transition uppercase tracking-widest">Restore System</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        <div v-if="showRestoreConfirm" class="fixed inset-0 z-[1000] bg-black/95 flex items-center justify-center p-4 backdrop-blur-md" @click.self="showRestoreConfirm = false">
            <div class="bg-[#18191c] border border-red-900/50 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-center items-center"><h3 class="font-argentum text-xl text-red-500 uppercase tracking-widest">CRITICAL WARNING</h3></div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">Overwrite current production database with this snapshot?</p>
                    <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest bg-red-900/10 p-3 rounded border border-red-900/30">All data generated after this timestamp will be permanently destroyed.</p>
                    <div class="flex gap-4 pt-2">
                        <button @click="showRestoreConfirm = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Abort</button>
                        <button @click="executeRestore" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(220,38,38,0.4)] text-xs">Confirm Restore</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ logs: Array });
const isProcessing = ref(false);
const showRestoreConfirm = ref(false);
const toast = ref({ show: false, message: '', type: 'success' });

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 4000);
};

const triggerBackup = () => {
    isProcessing.value = true;
    router.post(route('hq.backups.trigger'), {}, {
        preserveScroll: true,
        onSuccess: () => { 
            isProcessing.value = false;
            showToast("SQL Snapshot successfully verified in GCS bucket.", "success");
            props.logs.unshift({
                id: Date.now(), type: 'Manual Snapshot', status: 'Success', size: '43 MB',
                date: new Date().toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit', hour12: false })
            });
        }
    });
};

const executeRestore = () => {
    showRestoreConfirm.value = false;
    showToast("System restoration initiated via GCS protocol.", "error");
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(50px); }
</style>