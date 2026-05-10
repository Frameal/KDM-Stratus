<template>
    <Head title="Terminal Management - KDM Stratus" />

    <AdminLayout>
        <template #header>Terminal Grid Control</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                <div>
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Local Hardware Map</h3>
                    <p class="text-xs text-gray-400 mt-1 font-bold">Live database sync active.</p>
                </div>
                <button @click="showAddModal = true" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-5 py-2.5 rounded uppercase tracking-widest transition shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                    + Register New PC
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-8 gap-4">
                    <div v-for="pc in pcs" :key="pc.id" @click="openPcModal(pc)"
                        class="bg-[#101113] border rounded-lg p-4 flex flex-col items-center justify-center h-24 cursor-pointer transition-all duration-300 hover:scale-105"
                        :class="{
                            'border-green-500/50 hover:border-green-400': pc.status === 'free',
                            'border-blue-500/50 bg-[#1c1d21]': pc.status === 'occupied',
                            'border-orange-500/50 bg-[#1c1d21]': pc.status === 'reserved',
                            'border-red-900/50 opacity-50 bg-black': pc.status === 'broken'
                        }">
                        <div class="text-center">
                            <div class="font-bold text-sm tracking-widest mb-1 text-white">{{ pc.pc_number }}</div>
                            <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-[10px] uppercase">In Use</div>
                            <div v-else-if="pc.status === 'reserved'" class="text-orange-400 font-bold text-[10px] uppercase">Reserved</div>
                            <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-[10px] uppercase">Broken</div>
                            <div v-else class="text-green-400 font-bold text-[10px] uppercase">Free</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showAddModal" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showAddModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Register Terminal</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitNewPc">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Terminal Identifier</label>
                        <input type="text" v-model="addForm.pc_number" placeholder="e.g., PC-69" required
                            class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white font-bold tracking-widest outline-none focus:border-blue-500 mb-6" />
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white uppercase transition">Cancel</button>
                            <button type="submit" :disabled="addForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-6 py-2 rounded uppercase tracking-widest transition disabled:opacity-50">Save Hardware</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="activePc" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="activePc = null">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Terminal: {{ activePc.pc_number }}</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div v-if="activePc.status === 'occupied'" class="bg-blue-900/20 border border-blue-500/30 p-4 rounded-lg">
                        <button class="w-full bg-red-600 hover:bg-red-500 text-white text-xs font-bold py-3 rounded uppercase tracking-widest transition">
                            Force Log Out User
                        </button>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Live Hardware Status Override</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="updateStatus('free')" class="border border-green-500/50 text-green-400 text-xs font-bold py-3 rounded uppercase transition hover:bg-green-500/10" :class="{'bg-green-500/20': activePc.status === 'free'}">Set Free</button>
                            <button @click="updateStatus('broken')" class="border border-red-500/50 text-red-400 text-xs font-bold py-3 rounded uppercase transition hover:bg-red-500/10" :class="{'bg-red-500/20': activePc.status === 'broken'}">Mark Broken</button>
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-6 mt-6 text-center">
                        <button @click="deletePc" class="text-[10px] text-gray-500 hover:text-red-500 font-bold uppercase tracking-widest transition">
                            Permanently Delete Terminal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    pcs: Array
});

const activePc = ref(null);
const showAddModal = ref(false);

const addForm = useForm({
    pc_number: ''
});

const openPcModal = (pc) => { activePc.value = pc; };

const submitNewPc = () => {
    addForm.post(route('branch.terminals.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        }
    });
};

const updateStatus = (newStatus) => {
    router.patch(route('pcs.update_status', activePc.value.id), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            activePc.value = null;
        }
    });
};

const deletePc = () => {
    if (confirm(`Are you absolutely sure you want to delete ${activePc.value.pc_number}? This action cannot be undone.`)) {
        router.delete(route('branch.terminals.destroy', activePc.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                activePc.value = null;
            }
        });
    }
};
</script>