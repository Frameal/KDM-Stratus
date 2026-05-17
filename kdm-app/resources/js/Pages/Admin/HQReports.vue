<template>
    <Head title="Global Reports - KDM Stratus" />

    <AdminLayout>
        <template #header>Enterprise Feedback Repository</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center flex-wrap gap-4">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Global Report Index</h3>
                
                <button @click="generatePDF" class="bg-[#1c1d21] hover:bg-blue-900/40 border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-blue-400 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF Report
                </button>
            </div>
            
            <div class="bg-[#101113] p-4 border-b border-gray-800 flex flex-wrap gap-3">
                <input v-model="filters.search" type="text" placeholder="Search report details..." class="flex-1 bg-[#1c1d21] border border-gray-700 rounded px-3 py-2 text-white text-xs outline-none focus:border-blue-500 min-w-[200px]">
                
                <select v-model="filters.branch" class="bg-[#1c1d21] border border-gray-700 rounded px-3 py-2 text-white text-xs outline-none focus:border-blue-500 cursor-pointer min-w-[150px]">
                    <option value="">All Branches</option>
                    <option v-for="b in branches" :key="b.id" :value="b.name">{{ b.name }}</option>
                </select>

                <select v-model="filters.type" class="bg-[#1c1d21] border border-gray-700 rounded px-3 py-2 text-white text-xs outline-none focus:border-blue-500 cursor-pointer min-w-[150px]">
                    <option value="">All Types</option>
                    <option>Hardware Failure</option>
                    <option>Software Issue</option>
                    <option>Network Latency</option>
                    <option>Facility Cleanliness</option>
                    <option>Customer Service</option>
                    <option>Others</option>
                </select>

                <select v-model="filters.status" class="bg-[#1c1d21] border border-gray-700 rounded px-3 py-2 text-white text-xs outline-none focus:border-blue-500 cursor-pointer min-w-[150px]">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="investigating">Investigating</option>
                    <option value="resolved">Resolved</option>
                </select>

                <button @click="applyFilters" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] uppercase tracking-widest px-6 py-2 rounded transition">Apply Filters</button>
                <button @click="clearFilters" class="bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-gray-400 font-bold text-[10px] uppercase tracking-widest px-4 py-2 rounded transition">Clear</button>
            </div>

            <div class="p-6 overflow-x-auto">
                <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                    No reports match the current filter criteria.
                </div>

                <div v-else class="w-full text-left text-gray-400 min-w-[1000px]">
                    <div class="grid grid-cols-[1fr_1.5fr_1fr_1.5fr_3fr_1fr] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-[10px]">
                        <div>Date</div>
                        <div>Source Branch</div>
                        <div>Customer</div>
                        <div>Category</div>
                        <div class="pr-4">Details</div>
                        <div class="text-right">Status</div>
                    </div>
                    
                    <div v-for="report in reports" :key="report.id" class="grid grid-cols-[1fr_1.5fr_1fr_1.5fr_3fr_1fr] py-4 border-b border-gray-800/50 hover:bg-[#222328] transition items-center">
                        <div class="text-xs text-gray-500 font-mono">{{ new Date(report.created_at).toLocaleDateString() }}</div>
                        <div class="text-white font-bold text-xs uppercase tracking-widest">{{ report.branch_name }}</div>
                        <div class="text-gray-300 font-bold text-xs">
                            {{ report.is_anonymous ? 'Anonymous' : (report.user ? '@' + report.user.username : 'Unknown') }}
                        </div>
                        <div class="text-blue-400 text-[10px] font-bold uppercase tracking-wider">{{ report.concern_type }}</div>
                        <div class="pr-4 text-xs leading-relaxed text-gray-400">{{ report.details }}</div>
                        
                        <div class="text-right">
                            <span class="px-3 py-1.5 rounded text-[9px] font-bold uppercase tracking-widest border"
                                :class="{
                                    'bg-red-900/40 text-red-400 border-red-500/30': (report.status || 'pending') === 'pending',
                                    'bg-orange-900/40 text-orange-400 border-orange-500/30': report.status === 'investigating',
                                    'bg-green-900/40 text-green-400 border-green-500/30': report.status === 'resolved'
                                }">{{ report.status || 'pending' }}</span>
                        </div>
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

const props = defineProps({ reports: Array, branches: Array });

// FILTERS LOGIC
const filters = ref({
    search: new URLSearchParams(window.location.search).get('search') || '',
    branch: new URLSearchParams(window.location.search).get('branch') || '',
    type: new URLSearchParams(window.location.search).get('type') || '',
    status: new URLSearchParams(window.location.search).get('status') || '',
});

const applyFilters = () => {
    router.get(route('hq.reports'), filters.value, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    filters.value = { search: '', branch: '', type: '', status: '' };
    applyFilters();
};

// NATIVE PDF GENERATOR
const generatePDF = () => {
    axios.post(route('hq.log.export'), { details: 'Exported Global Feedback Reports PDF' });
    const printWindow = window.open('', '_blank');
    let html = `
        <html><head><title>Global Enterprise Feedback Report</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; }
            h2 { color: #1a56db; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
            .meta { font-size: 12px; color: #666; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f8fafc; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
            .pending { color: #dc2626; font-weight: bold; text-transform: uppercase; font-size: 9px;}
            .investigating { color: #ea580c; font-weight: bold; text-transform: uppercase; font-size: 9px;}
            .resolved { color: #16a34a; font-weight: bold; text-transform: uppercase; font-size: 9px;}
        </style>
        </head><body>
        <h2>KDM Stratus - Global Feedback Report</h2>
        <div class="meta"><strong>Generated:</strong> ${new Date().toLocaleString()}</div>
        <table>
            <tr><th>Date</th><th>Source Branch</th><th>Customer</th><th>Category</th><th>Details</th><th>Status</th></tr>
    `;
    
    if(props.reports.length === 0) {
        html += `<tr><td colspan="6" style="text-align:center;">No reports match the criteria.</td></tr>`;
    } else {
        props.reports.forEach(r => {
            const customer = r.is_anonymous ? 'Anonymous' : (r.user ? '@' + r.user.username : 'Unknown');
            const status = r.status || 'pending';
            html += `
                <tr>
                    <td>${new Date(r.created_at).toLocaleDateString()}</td>
                    <td><strong>${r.branch_name}</strong></td>
                    <td>${customer}</td>
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