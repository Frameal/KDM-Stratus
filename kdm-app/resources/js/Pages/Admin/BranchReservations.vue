<template>
    <Head title="Reservations - KDM Stratus" />

    <AdminLayout>
        <template #header>Active Network Reservations</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Incoming Customers</h3>
            </div>
            
            <div class="p-6">
                <div v-if="reservations.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                    No active reservations for this branch.
                </div>

                <div v-else class="w-full text-left text-gray-400 text-sm">
                    <div class="grid grid-cols-5 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-xs">
                        <div>Terminal</div>
                        <div>Customer Username</div>
                        <div>Time Remaining</div>
                        <div>Fee Paid</div>
                        <div class="text-right">Actions</div>
                    </div>
                    
                    <div v-for="res in reservations" :key="res.id" class="grid grid-cols-5 items-center py-4 border-b border-gray-800/50 hover:bg-[#222328] transition">
                        <div class="text-orange-400 font-bold font-mono">{{ res.pc_number }}</div>
                        <div class="text-white font-bold">{{ res.customer_name }}</div>
                        <div class="text-orange-400 font-bold animate-pulse">{{ res.time_remaining }}</div>
                        <div class="text-green-400 font-mono">₱5.00</div>
                        <div class="text-right flex justify-end gap-2">
                            <button class="bg-gray-800 hover:bg-gray-700 border border-gray-600 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition">Cancel</button>
                            <button class="bg-red-900/30 hover:bg-red-900/50 border border-red-500/30 text-red-400 px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition">Cancel & Refund ₱5</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept LIVE reservations from the database
const props = defineProps({
    reservations: Array
});
</script>