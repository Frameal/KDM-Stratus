<template>
    <Head title="Global Audit Logs - KDM Stratus" />
    <AdminLayout>
        <template #header>Global System Audit Matrix</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center gap-4">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Enterprise Accountability Ledger</h3>
                
                <select v-model="filters.branch" @change="applyFilters" class="bg-[#1c1d21] border border-gray-700 rounded px-4 py-2 text-white text-xs font-bold uppercase tracking-widest outline-none focus:border-blue-500 cursor-pointer min-w-[200px]">
                    <option value="">All Branches</option>
                    <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
            </div>
            
            <div class="p-6 overflow-x-auto">
                <div v-if="logs.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">No audit logs recorded for this criteria.</div>
                <table v-else class="w-full text-left text-sm min-w-[900px]">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                            <th class="pb-3">Timestamp</th>
                            <th class="pb-3">Branch Scope</th>
                            <th class="pb-3">Executing Staff</th>
                            <th class="pb-3">System Action</th>
                            <th class="pb-3">Audit Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs" :key="log.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                            <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(log.created_at).toLocaleString() }}</td>
                            <td class="py-4 text-gray-400 font-bold text-xs uppercase tracking-widest">{{ log.branch?.name || 'HQ / Global' }}</td>
                            <td class="py-4 text-blue-400 font-bold text-xs truncate">@{{ log.user?.username || 'SYSTEM' }}</td>
                            <td class="py-4 text-orange-400 text-[10px] font-bold uppercase">{{ log.action }}</td>
                            <td class="py-4 text-xs text-gray-300 pr-4">{{ log.details }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ logs: Array, branches: Array });
const filters = ref({ branch: new URLSearchParams(window.location.search).get('branch') || '' });

const applyFilters = () => { router.get(route('hq.logs'), filters.value, { preserveState: true, preserveScroll: true }); };
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>