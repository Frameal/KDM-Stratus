<template>
    <Head title="HQ Command Center" />

    <AdminLayout>
        <template #header>Global Telemetry Matrix</template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Enterprise Revenue</div>
                    <div class="text-3xl font-argentum text-green-400">₱0.00</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Active Branches</div>
                    <div class="text-3xl font-argentum text-white">28</div>
                </div>
                <div class="bg-[#222328] border border-gray-800 p-6 rounded-xl shadow-lg">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">System Health</div>
                    <div class="text-3xl font-argentum text-blue-400">Optimal</div>
                </div>
            </div>

            <div class="border-2 border-dashed border-gray-700 rounded-2xl p-16 text-center">
                <p class="text-gray-500 font-bold uppercase tracking-widest">Global Telemetry Grid Loading...</p>
                <p class="text-sm text-gray-600 mt-2">Connect live database feed to view all terminals.</p>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>