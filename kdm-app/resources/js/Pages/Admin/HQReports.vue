<template>
    <Head title="Global Reports - KDM HQ" />

    <AdminLayout>
        <template #header>Enterprise Feedback Log</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Network-Wide Tickets</h3>
                <button class="bg-[#101113] border border-gray-700 hover:border-gray-500 text-gray-300 text-[10px] font-bold px-4 py-2 rounded transition uppercase tracking-widest">Generate CSV Summary</button>
            </div>
            
            <div class="p-6">
                <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                    No reports filed in the network.
                </div>
                <div v-else class="w-full text-left text-gray-400 text-sm">
                    <div class="grid grid-cols-5 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-[10px]">
                        <div>Date</div>
                        <div>Branch Source</div>
                        <div>Concern Type</div>
                        <div>Details</div>
                        <div class="text-right">Status</div>
                    </div>
                    
                    <div v-for="report in reports" :key="report.id" class="grid grid-cols-5 py-4 border-b border-gray-800/50 hover:bg-[#222328] transition">
                        <div class="text-xs text-gray-500">{{ new Date(report.created_at).toLocaleDateString() }}</div>
                        <div class="text-white font-bold text-xs uppercase tracking-widest">{{ report.branch_name }}</div>
                        <div class="text-orange-400 text-[10px] font-bold uppercase">{{ report.concern_type }}</div>
                        <div class="truncate pr-4 text-xs">{{ report.details }}</div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded border"
                                :class="{'bg-red-900/30 text-red-400 border-red-500/30': report.status === 'Pending', 'bg-blue-900/30 text-blue-400 border-blue-500/30': report.status !== 'Pending'}">
                                {{ report.status }}
                            </span>
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

const props = defineProps({
    reports: Array
});
</script>