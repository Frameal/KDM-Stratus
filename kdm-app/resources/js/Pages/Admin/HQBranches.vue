<template>
    <Head title="Global Branch Viewer - KDM HQ" />

    <AdminLayout>
        <template #header>Global Branch Operations</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl p-6 mb-8 flex items-center justify-between">
            <div>
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Target Branch</h3>
                <p class="text-xs text-gray-400 font-bold mt-1">Select a node to view its live telemetry and local feedback.</p>
            </div>
            <select @change="changeBranch($event)" class="bg-[#222328] border border-blue-500 text-white font-bold tracking-widest uppercase rounded-lg px-6 py-3 outline-none">
                <option v-for="b in branches" :key="b.id" :value="b.id" :selected="b.id === selectedBranch.id">
                    {{ b.name }} Branch
                </option>
            </select>
        </div>

        <div class="space-y-8">
            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">{{ selectedBranch.name }} Terminal Grid</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        <div v-for="pc in pcs" :key="pc.id" 
                            class="bg-[#101113] border rounded-lg p-3 flex flex-col items-center justify-center h-20 transition-all duration-300"
                            :class="{
                                'border-green-500/50': pc.status === 'free',
                                'border-blue-500/50 bg-[#1c1d21]': pc.status === 'occupied',
                                'border-orange-500/50 bg-[#1c1d21]': pc.status === 'reserved',
                                'border-red-900/50 opacity-50 bg-black': pc.status === 'broken'
                            }">
                            <div class="text-center">
                                <div class="font-bold text-xs tracking-widest text-white">{{ pc.pc_number }}</div>
                                <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-[8px] uppercase">In Use</div>
                                <div v-else-if="pc.status === 'reserved'" class="text-orange-400 font-bold text-[8px] uppercase">Reserved</div>
                                <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-[8px] uppercase">Broken</div>
                                <div v-else class="text-green-400 font-bold text-[8px] uppercase">Free</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">{{ selectedBranch.name }} Feedback Reports</h3>
                </div>
                <div class="p-6">
                    <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">
                        No reports filed for this branch.
                    </div>
                    <div v-else class="w-full text-left text-gray-400 text-sm">
                        <div class="grid grid-cols-4 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-xs">
                            <div>Concern Type</div>
                            <div>Details</div>
                            <div>Anonymous</div>
                            <div>Status</div>
                        </div>
                        <div v-for="report in reports" :key="report.id" class="grid grid-cols-4 py-4 border-b border-gray-800/50">
                            <div class="text-orange-400 text-xs font-bold uppercase">{{ report.concern_type }}</div>
                            <div class="text-xs pr-4">{{ report.details }}</div>
                            <div class="text-xs">{{ report.is_anonymous ? 'Yes' : 'No' }}</div>
                            <div class="text-xs text-blue-400 uppercase font-bold">{{ report.status }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    branches: Array,
    selectedBranch: Object,
    pcs: Array,
    reports: Array
});

// Sends Narpim to the URL with the specific branch ID to reload the data
const changeBranch = (event) => {
    router.get(route('hq.branches'), { branch_id: event.target.value }, { preserveState: true });
};
</script>