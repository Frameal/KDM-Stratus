<template>
    <Head title="Local Transactions - KDM Stratus" />

    <AdminLayout>
        <template #header>Local Transaction Ledger</template>

        <div class="space-y-8">
            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden p-6 flex justify-between items-center">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Branch Revenue History</h3>
                <button @click="generatePDF" class="bg-[#1c1d21] hover:bg-blue-900/40 border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-blue-400 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Revenue Report
                </button>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 bg-[#222328]">
                    <h3 class="font-argentum text-sm text-white uppercase tracking-widest">Local Hardware Reservations</h3>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[700px]">
                        <thead>
                            <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                                <th class="pb-3">Timestamp</th>
                                <th class="pb-3">Customer</th>
                                <th class="pb-3">Terminal</th>
                                <th class="pb-3 text-right">Fee Collected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="res in reservations" :key="res.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                                <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(res.created_at).toLocaleString() }}</td>
                                <td class="py-4 text-white font-bold text-xs">@{{ res.user?.username || 'Unknown' }}</td>
                                <td class="py-4 text-gray-300 font-mono text-xs">{{ res.pc?.pc_number || 'N/A' }}</td>
                                <td class="py-4 text-right font-mono font-bold text-sm" :class="res.status === 'refunded' ? 'text-gray-500 line-through' : 'text-green-400'">
                                    ₱{{ Number(res.fee_paid).toFixed(2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 bg-[#222328]">
                    <h3 class="font-argentum text-sm text-white uppercase tracking-widest">Local Customer Top-Ups</h3>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead>
                            <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                                <th class="pb-3">Timestamp</th>
                                <th class="pb-3">Customer</th>
                                <th class="pb-3">Reference ID</th>
                                <th class="pb-3 text-right">Amount Funded</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="top in topups" :key="top.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                                <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(top.created_at).toLocaleString() }}</td>
                                <td class="py-4 text-white font-bold text-xs">@{{ top.user?.username || 'Unknown' }}</td>
                                <td class="py-4 text-blue-400 font-mono text-[10px]">{{ top.reference_id }}</td>
                                <td class="py-4 text-right font-mono font-bold text-sm text-green-400">₱{{ Number(top.amount).toFixed(2) }}</td>
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
import axios from 'axios';

const props = defineProps({ reservations: Array, topups: Array });

const generatePDF = () => {
    const totalRes = props.reservations.reduce((sum, r) => r.status !== 'refunded' ? sum + Number(r.fee_paid) : sum, 0);
    const totalTop = props.topups.reduce((sum, t) => sum + Number(t.amount), 0);
    axios.post(route('branch.log.export'), { details: 'Exported Local Transaction Ledger PDF' });

    const printWindow = window.open('', '_blank');
    let html = `
        <html><head><title>Local Transaction Report</title>
        <style>
            body { font-family: sans-serif; padding: 40px; color: #333; }
            h2 { color: #1a56db; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
            .summary { margin-bottom: 30px; padding: 15px; background: #f8fafc; border: 1px solid #ddd; font-size: 14px;}
            table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 12px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f1f5f9; text-transform: uppercase; font-size: 10px; }
            .refunded { color: #9ca3af; text-decoration: line-through; }
        </style>
        </head><body>
        <h2>KDM Stratus - Local Transaction Report</h2>
        <div class="summary">
            <strong>Date Generated:</strong> ${new Date().toLocaleString()}<br><br>
            <strong>Total Local Reservations:</strong> ₱${totalRes.toFixed(2)}<br>
            <strong>Total Local Top-Ups:</strong> ₱${totalTop.toFixed(2)}
        </div>
        
        <h3>Hardware Reservations</h3>
        <table><tr><th>Date</th><th>Customer</th><th>PC</th><th>Fee</th></tr>
    `;
    props.reservations.forEach(r => {
        html += `<tr><td>${new Date(r.created_at).toLocaleString()}</td><td>@${r.user?.username}</td><td>${r.pc?.pc_number}</td><td class="${r.status === 'refunded' ? 'refunded' : ''}">₱${Number(r.fee_paid).toFixed(2)}</td></tr>`;
    });
    
    html += `</table><h3>Wallet Top-Ups</h3><table><tr><th>Date</th><th>Customer</th><th>Ref ID</th><th>Amount</th></tr>`;
    props.topups.forEach(t => {
        html += `<tr><td>${new Date(t.created_at).toLocaleString()}</td><td>@${t.user?.username}</td><td>${t.reference_id}</td><td>₱${Number(t.amount).toFixed(2)}</td></tr>`;
    });
    
    html += `</table></body></html>`;
    printWindow.document.write(html);
    printWindow.document.close();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>