<template>
    <Head title="Feedback & Reports - KDM Stratus" />

    <AdminLayout>
        <template #header>Local Feedback & Reports</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Customer Tickets</h3>
            </div>
            
            <div class="p-6">
                <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                    No customer feedback filed for this branch.
                </div>

                <div v-else class="w-full text-left text-gray-400 text-sm">
                    <div class="grid grid-cols-5 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-xs">
                        <div>Date</div>
                        <div>Customer</div>
                        <div>Concern Type</div>
                        <div>Details</div>
                        <div class="text-right">Status</div>
                    </div>
                    
                    <div v-for="report in reports" :key="report.id" class="grid grid-cols-5 py-4 border-b border-gray-800/50 hover:bg-[#222328] transition">
                        <div class="text-xs text-gray-500">{{ new Date(report.created_at).toLocaleDateString() }}</div>
                        <div class="text-white font-bold">{{ report.is_anonymous ? 'Anonymous' : 'Registered User' }}</div>
                        <div class="text-orange-400 text-xs font-bold uppercase">{{ report.concern_type }}</div>
                        <div class="truncate pr-4 text-xs">{{ report.details }}</div>
                        <div class="text-right">
                            <select class="bg-[#1c1d21] border border-gray-700 text-xs text-gray-300 rounded px-2 py-1 outline-none focus:border-blue-500 cursor-pointer">
                                <option value="pending" class="text-red-400">Pending</option>
                                <option value="investigating" class="text-orange-400">Investigating</option>
                                <option value="resolved" class="text-green-400">Resolved</option>
                            </select>
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

// Accept LIVE reports from the database
const props = defineProps({
    reports: Array
});
</script>