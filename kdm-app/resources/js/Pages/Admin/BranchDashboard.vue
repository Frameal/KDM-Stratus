<template>
    <Head :title="`${branch.name} Dashboard - KDM Stratus`" />

    <AdminLayout>
        <template #header>{{ branch.name }} Branch Overview</template>

        <div class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-green-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Available Terminals</div>
                    <div class="text-3xl font-argentum text-green-400">{{ stats.free_pcs }} / {{ stats.total_pcs }}</div>
                </div>
                
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-blue-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Active Sessions</div>
                    <div class="text-3xl font-argentum text-white">{{ stats.total_pcs - stats.free_pcs - stats.broken_pcs }}</div>
                </div>

                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-red-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Local Customer Tickets</div>
                    <div class="text-3xl font-argentum text-red-400">{{ stats.reports }} Pending</div>
                </div>
            </div>

            </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept the LIVE data from web.php
const props = defineProps({
    stats: Object,
    branch: Object
});
</script>