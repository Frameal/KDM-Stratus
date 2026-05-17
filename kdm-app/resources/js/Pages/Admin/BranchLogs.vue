<template>
    <Head title="Local Audit Logs - KDM Stratus" />
    <AdminLayout>
        <template #header>Local System Audit Matrix</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Local Security Ledger</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-widest">Permanent, un-erasable record of manager actions.</p>
            </div>
            
            <div class="p-6 overflow-x-auto">
                <div v-if="logs.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">No audit logs recorded for this branch.</div>
                <table v-else class="w-full text-left text-sm min-w-[700px]">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                            <th class="pb-3">Timestamp</th>
                            <th class="pb-3">Executing Staff</th>
                            <th class="pb-3">System Action</th>
                            <th class="pb-3">Audit Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs" :key="log.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                            <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(log.created_at).toLocaleString() }}</td>
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
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineProps({ logs: Array });
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>