<template>
    <Head title="Executive Command - KDM Stratus" />

    <AdminLayout>
        <template #header>Global Enterprise Overview</template>

        <div class="space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#18191c] border border-gray-800 p-8 rounded-2xl shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition transform group-hover:scale-110">
                        <svg class="w-32 h-32 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Network Reservation Revenue (Today)</p>
                        <h2 class="text-5xl font-mono font-bold text-white"><span class="text-blue-500">₱</span>{{ Number(todayRevenue).toFixed(2) }}</h2>
                        <p class="text-[10px] text-green-400 mt-3 font-bold uppercase tracking-widest">Aggregated across all branches</p>
                    </div>
                </div>

                <div class="bg-[#18191c] border border-gray-800 p-8 rounded-2xl shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition transform group-hover:scale-110">
                        <svg class="w-32 h-32 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Wallet Top-Ups Processed (Today)</p>
                        <h2 class="text-5xl font-mono font-bold text-white"><span class="text-green-500">₱</span>{{ Number(todayTopUps).toFixed(2) }}</h2>
                        <p class="text-[10px] text-blue-400 mt-3 font-bold uppercase tracking-widest">Digital GCash/Maya/QRPh Inflows</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-blue-500 text-center">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Active Branches</div>
                    <div class="text-3xl font-mono font-bold text-blue-400">{{ stats.total_branches }}</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-indigo-500 text-center">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Hardware</div>
                    <div class="text-3xl font-mono font-bold text-indigo-400">{{ stats.total_pcs }}</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-orange-500 text-center">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Units In-Use</div>
                    <div class="text-3xl font-mono font-bold text-orange-400">{{ stats.occupied_pcs }}</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-t-red-500 text-center">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Unresolved Reports</div>
                    <div class="text-3xl font-mono font-bold text-red-400">{{ stats.total_reports }}</div>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Network Topology Status</h3>
                </div>
                
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[700px]">
                        <thead>
                            <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                                <th class="pb-3">Node Name</th>
                                <th class="pb-3">Location Segment</th>
                                <th class="pb-3 text-center">Hardware Count</th>
                                <th class="pb-3 text-right">Telemetry Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="branch in branches" :key="branch.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                                <td class="py-4 text-white font-bold text-xs uppercase tracking-widest">{{ branch.name }}</td>
                                <td class="py-4 text-gray-400 text-xs font-mono">{{ branch.address }}</td>
                                <td class="py-4 text-center text-blue-400 font-bold font-mono">{{ branch.total_pcs }} Units</td>
                                <td class="py-4 text-right">
                                    <span class="bg-green-900/30 text-green-500 border border-green-500/30 px-3 py-1.5 rounded text-[9px] font-bold uppercase tracking-widest inline-flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Online
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ stats: Object, todayRevenue: Number, todayTopUps: Number, branches: Array });
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>