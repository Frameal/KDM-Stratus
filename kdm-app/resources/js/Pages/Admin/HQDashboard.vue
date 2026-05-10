<template>
    <Head title="HQ Command Center" />

    <AdminLayout>
        <template #header>Global Telemetry Matrix</template>

        <div class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-green-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Global Network Hardware</div>
                    <div class="text-3xl font-argentum text-green-400">{{ stats.total_pcs }} Terminals</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-blue-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Active Branches</div>
                    <div class="text-3xl font-argentum text-white">{{ stats.total_branches }} Locations</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-orange-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Global Active Sessions</div>
                    <div class="text-3xl font-argentum text-orange-400">{{ stats.occupied_pcs }} Users</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-red-500">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Enterprise Feedback Tickets</div>
                    <div class="text-3xl font-argentum text-red-400">{{ stats.total_reports }} Pending</div>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Network Topology</h3>
                    <div class="flex gap-3">
                        <button class="bg-[#101113] border border-gray-700 hover:border-gray-500 text-gray-300 text-xs font-bold px-4 py-2 rounded transition">Generate CSV Report</button>
                        <button class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-4 py-2 rounded transition shadow-lg">Deploy New Branch</button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-700 rounded-xl p-20 text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-blue-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-gray-500 font-bold uppercase tracking-widest">Global Telemetry Grid Loading...</p>
                        <p class="text-sm text-gray-600 mt-2">Awaiting Tailscale SD-WAN handshake for live data feed.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept the live data passed from the controller
const props = defineProps({
    stats: Object
});
</script>