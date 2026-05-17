<template>
    <Head title="Feedback & Reports - KDM Stratus" />

    <AdminLayout>
        <template #header>Local Feedback & Reports</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Customer Tickets</h3>
                
                <button @click="generatePDF" class="bg-[#1c1d21] hover:bg-blue-900/40 border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-blue-400 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF Report
                </button>
            </div>
            
            <div class="p-6 overflow-x-auto">
                <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                    No customer feedback filed for this branch.
                </div>

                <div v-else class="w-full text-left text-gray-400 min-w-[900px]">
                    <div class="grid grid-cols-[1fr_1fr_1.5fr_3fr_2.5fr] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-xs">
                        <div>Date</div>
                        <div>Customer</div>
                        <div>Type</div>
                        <div class="pr-4">Details</div>
                        <div class="text-center">Status Action</div>
                    </div>
                    
                    <div v-for="report in reports" :key="report.id" class="grid grid-cols-[1fr_1fr_1.5fr_3fr_2.5fr] py-4 border-b border-gray-800/50 hover:bg-[#222328] transition items-center">
                        <div class="text-xs text-gray-500 font-mono">{{ new Date(report.created_at).toLocaleDateString() }}</div>
                        <div class="text-white font-bold text-sm">
                            {{ report.is_anonymous ? 'Anonymous' : (report.user ? '@' + report.user.username : 'Unknown User') }}
                        </div>
                        <div class="text-blue-400 text-xs font-bold uppercase tracking-wider">{{ report.concern_type }}</div>
                        <div class="pr-4 text-sm leading-relaxed">{{ report.details }}</div>
                        
                        <div class="flex justify-center gap-2">
                            <button @click="updateStatus(report, 'pending')" 
                                class="px-3 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition" 
                                :class="(report.status || 'pending') === 'pending' ? 'bg-red-900/40 text-red-400 border border-red-500/30 cursor-default' : 'bg-[#1c1d21] border border-gray-700 text-gray-500 hover:text-white hover:border-gray-500'">
                                Pending
                            </button>
                            
                            <button @click="triggerConfirm(report, 'investigating')" 
                                class="px-3 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition" 
                                :class="report.status === 'investigating' ? 'bg-orange-900/40 text-orange-400 border border-orange-500/30 cursor-default' : 'bg-[#1c1d21] border border-gray-700 text-gray-500 hover:text-orange-400 hover:border-orange-500'">
                                Investigating
                            </button>
                            
                            <button @click="triggerConfirm(report, 'resolved')" 
                                class="px-3 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition" 
                                :class="report.status === 'resolved' ? 'bg-green-900/40 text-green-400 border border-green-500/30 cursor-default' : 'bg-[#1c1d21] border border-gray-700 text-gray-500 hover:text-green-400 hover:border-green-500'">
                                Resolved
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="confirmModal.show" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="confirmModal.show = false">
            <div class="bg-[#18191c] border rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative" :class="confirmModal.type === 'resolved' ? 'border-green-900/50' : 'border-orange-900/50'">
                <div class="px-6 py-4 border-b flex justify-center items-center" :class="confirmModal.type === 'resolved' ? 'bg-green-900/20 border-green-900/50' : 'bg-orange-900/20 border-orange-900/50'">
                    <h3 class="font-argentum text-xl uppercase tracking-widest" :class="confirmModal.type === 'resolved' ? 'text-green-500' : 'text-orange-500'">Confirm Action</h3>
                </div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide text-lg">
                        {{ confirmModal.type === 'resolved' ? 'Confirm ticket is resolved?' : 'Set status to Investigating?' }}
                    </p>
                    <div class="flex gap-4 pt-2">
                        <button @click="confirmModal.show = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-sm">Cancel</button>
                        <button @click="executeStatusUpdate" class="flex-1 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-sm shadow-lg" :class="confirmModal.type === 'resolved' ? 'bg-green-600 hover:bg-green-500 shadow-green-500/30' : 'bg-orange-600 hover:bg-orange-500 shadow-orange-500/30'">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({ reports: Array });

const confirmModal = ref({ show: false, type: '', report: null });

const triggerConfirm = (report, newStatus) => {
    const currentStatus = report.status || 'pending';
    if (currentStatus === newStatus) return; 
    confirmModal.value = { show: true, type: newStatus, report: report };
};

const updateStatus = (report, newStatus) => {
    const currentStatus = report.status || 'pending';
    if (currentStatus === newStatus) return;
    router.patch(route('branch.feedback.update', report.id), { status: newStatus }, { preserveScroll: true });
};

const executeStatusUpdate = () => {
    router.patch(route('branch.feedback.update', confirmModal.value.report.id), { status: confirmModal.value.type }, {
        preserveScroll: true,
        onSuccess: () => { confirmModal.value.show = false; }
    });
};

// NATIVE PDF GENERATOR
const generatePDF = () => {
    const printWindow = window.open('', '_blank');
    axios.post(route('branch.log.export'), { details: 'Exported Local Feedback PDF' });
    let html = `
        <html><head><title>Customer Feedback Report</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; }
            h2 { color: #1a56db; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
            .meta { font-size: 12px; color: #666; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f8fafc; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
            .pending { color: #dc2626; font-weight: bold; text-transform: uppercase; font-size: 10px;}
            .investigating { color: #ea580c; font-weight: bold; text-transform: uppercase; font-size: 10px;}
            .resolved { color: #16a34a; font-weight: bold; text-transform: uppercase; font-size: 10px;}
        </style>
        </head><body>
        <h2>KDM Stratus - Local Feedback Report</h2>
        <div class="meta"><strong>Generated:</strong> ${new Date().toLocaleString()}</div>
        <table>
            <tr><th>Date</th><th>Customer</th><th>Type</th><th>Details</th><th>Status</th></tr>
    `;
    
    if(props.reports.length === 0) {
        html += `<tr><td colspan="5" style="text-align:center;">No reports filed.</td></tr>`;
    } else {
        props.reports.forEach(r => {
            const customer = r.is_anonymous ? 'Anonymous' : (r.user ? '@' + r.user.username : 'Unknown');
            const status = r.status || 'pending';
            html += `
                <tr>
                    <td>${new Date(r.created_at).toLocaleDateString()}</td>
                    <td><strong>${customer}</strong></td>
                    <td>${r.concern_type}</td>
                    <td>${r.details}</td>
                    <td class="${status}">${status}</td>
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
</style>