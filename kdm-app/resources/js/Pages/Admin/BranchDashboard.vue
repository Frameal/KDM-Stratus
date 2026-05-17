<template>
    <Head :title="`${branch.name} Dashboard - KDM Stratus`" />

    <AdminLayout>
        <template #header>{{ branch.name }} Branch Overview</template>

        <div class="space-y-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                
                <div class="bg-[#222328] border border-gray-800 p-5 rounded-xl shadow-lg border-t-4 border-t-green-500 relative overflow-hidden group">
                    <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 relative z-10">Available Hardware</div>
                    <div class="text-3xl font-mono font-bold text-green-400 relative z-10">{{ stats.free_pcs }}<span class="text-lg text-gray-600">/{{ stats.total_pcs }}</span></div>
                </div>
                
                <div class="bg-[#222328] border border-gray-800 p-5 rounded-xl shadow-lg border-t-4 border-t-orange-500 relative overflow-hidden group">
                    <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 relative z-10">Active Sessions</div>
                    <div class="text-3xl font-mono font-bold text-orange-400 relative z-10">{{ stats.active_reservations }}</div>
                </div>

                <div class="bg-[#222328] border border-gray-800 p-5 rounded-xl shadow-lg border-t-4 border-t-blue-500 relative overflow-hidden group">
                    <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 relative z-10">Reservation Income</div>
                    <div class="text-3xl font-mono font-bold text-white relative z-10"><span class="text-blue-500">₱</span>{{ Number(totalRevenue).toFixed(2) }}</div>
                </div>

                <div class="bg-[#222328] border border-gray-800 p-5 rounded-xl shadow-lg border-t-4 border-t-indigo-500 relative overflow-hidden group">
                    <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 relative z-10">Top-Up Revenue</div>
                    <div class="text-3xl font-mono font-bold text-white relative z-10"><span class="text-indigo-500">₱</span>{{ Number(todayTopUps || 0).toFixed(2) }}</div>
                </div>

                <div class="bg-[#222328] border border-gray-800 p-5 rounded-xl shadow-lg border-t-4 border-t-red-500 relative overflow-hidden group">
                    <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 relative z-10">Pending Reports</div>
                    <div class="text-3xl font-mono font-bold text-red-400 relative z-10">{{ stats.reports }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden flex flex-col h-[500px]">
                    <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                        <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Hardware Reservations</h3>
                        <div class="flex gap-3 items-center">
                            <button @click="generatePDF" class="bg-[#1c1d21] hover:bg-blue-900/40 border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-blue-400 px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition shadow-lg">
                                Export PDF
                            </button>
                            <div class="bg-[#101113] border border-gray-700 px-3 py-1.5 rounded text-[10px] font-bold text-green-400 uppercase tracking-widest">Receiving</div>
                        </div>
                    </div>
                    <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                        <div v-if="shiftRevenue.length === 0" class="text-center py-12 border border-gray-800 border-dashed rounded-xl">
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">No hardware reservations today.</p>
                        </div>
                        <div v-else class="w-full text-left text-sm">
                            <div class="grid grid-cols-4 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-[10px]">
                                <div>Time</div><div>Customer</div><div>PC</div><div class="text-right">Fee</div>
                            </div>
                            <div v-for="rev in shiftRevenue" :key="rev.id" class="grid grid-cols-4 py-3 border-b border-gray-800/50 hover:bg-[#222328] transition items-center">
                                <div class="text-[10px] text-gray-400 font-mono">{{ new Date(rev.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                                <div class="text-white font-bold text-xs truncate pr-2">@{{ rev.user?.username || 'Unknown' }}</div>
                                <div class="text-gray-300 font-mono text-xs">{{ rev.pc?.pc_number || 'N/A' }}</div>
                                <div class="text-right font-mono font-bold text-[10px]" :class="rev.status === 'refunded' ? 'text-gray-500 line-through' : 'text-green-400'">
                                    ₱{{ Number(rev.fee_paid).toFixed(2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden flex flex-col h-[500px]">
                    <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                        <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Digital Top-Ups</h3>
                        <div class="bg-[#101113] border border-gray-700 px-3 py-1.5 rounded text-[10px] font-bold text-green-400 uppercase tracking-widest">Receiving</div>
                    </div>
                    <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                        <div v-if="(!todayTopUpData || todayTopUpData.length === 0)" class="text-center py-12 border border-gray-800 border-dashed rounded-xl">
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">No local top-ups today.</p>
                        </div>
                        <div v-else class="w-full text-left text-sm">
                            <div class="grid grid-cols-4 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-[10px]">
                                <div>Time</div><div class="col-span-2">Customer</div><div class="text-right">Funded</div>
                            </div>
                            <div v-for="top in todayTopUpData" :key="top.id" class="grid grid-cols-4 py-3 border-b border-gray-800/50 hover:bg-[#222328] transition items-center">
                                <div class="text-[10px] text-gray-400 font-mono">{{ new Date(top.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                                <div class="col-span-2 text-white font-bold text-xs truncate pr-2">@{{ top.user?.username || 'Unknown' }}</div>
                                <div class="text-right font-mono font-bold text-[10px] text-green-400">
                                    ₱{{ Number(top.amount).toFixed(2) }}
                                </div>
                            </div>
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
import axios from 'axios';

const props = defineProps({
    stats: Object,
    branch: Object,
    shiftRevenue: Array,
    totalRevenue: Number,
    todayTopUps: Number,
    todayTopUpData: Array
});

// NATIVE PDF GENERATOR FOR DASHBOARD (Handles both ledgers)
const generatePDF = () => {
    axios.post(route('branch.log.export'), { details: 'Exported Shift Revenue Ledger PDF' });
    const printWindow = window.open('', '_blank');
    let html = `
        <html><head><title>Shift Revenue Ledger</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; }
            h2 { color: #1a56db; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
            .meta { font-size: 12px; color: #666; margin-bottom: 30px; display: flex; justify-content: space-between;}
            .total { font-size: 14px; font-weight: bold; color: #16a34a; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; margin-bottom: 30px;}
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f8fafc; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
            .refunded { color: #9ca3af; text-decoration: line-through; }
        </style>
        </head><body>
        <h2>KDM Stratus - Shift Revenue Ledger</h2>
        <div class="meta">
            <span><strong>Generated:</strong> ${new Date().toLocaleString()}</span>
            <div style="text-align:right;">
                <div class="total">Total Reservation Revenue: ₱${Number(props.totalRevenue).toFixed(2)}</div>
                <div class="total">Total Top-Up Revenue: ₱${Number(props.todayTopUps || 0).toFixed(2)}</div>
            </div>
        </div>
        
        <h3>Hardware Reservations</h3>
        <table>
            <tr><th>Timestamp</th><th>Customer</th><th>Hardware</th><th>Duration</th><th>Fee Collected</th></tr>
    `;
    
    if(props.shiftRevenue.length === 0) {
        html += `<tr><td colspan="5" style="text-align:center;">No hardware reservations recorded.</td></tr>`;
    } else {
        props.shiftRevenue.forEach(rev => {
            const statusClass = rev.status === 'refunded' ? 'refunded' : '';
            html += `
                <tr>
                    <td>${new Date(rev.created_at).toLocaleTimeString()}</td>
                    <td><strong>@${rev.user?.username || 'Unknown'}</strong></td>
                    <td>${rev.pc?.pc_number || 'N/A'}</td>
                    <td>${rev.duration_minutes} Mins</td>
                    <td class="${statusClass}">₱${Number(rev.fee_paid).toFixed(2)} ${rev.status === 'refunded' ? '(REFUNDED)' : ''}</td>
                </tr>`;
        });
    }
    html += `</table>`;

    html += `<h3>Digital Top-Ups</h3><table><tr><th>Timestamp</th><th>Customer</th><th>Amount Funded</th></tr>`;
    if(!props.todayTopUpData || props.todayTopUpData.length === 0) {
        html += `<tr><td colspan="3" style="text-align:center;">No local top-ups recorded.</td></tr>`;
    } else {
        props.todayTopUpData.forEach(top => {
            html += `
                <tr>
                    <td>${new Date(top.created_at).toLocaleTimeString()}</td>
                    <td><strong>@${top.user?.username || 'Unknown'}</strong></td>
                    <td style="color: #16a34a;">₱${Number(top.amount).toFixed(2)}</td>
                </tr>`;
        });
    }

    html += `</table></body></html>`;
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
</style>