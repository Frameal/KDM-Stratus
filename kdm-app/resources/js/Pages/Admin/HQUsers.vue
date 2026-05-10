<template>
    <Head title="Enterprise Users - KDM HQ" />

    <AdminLayout>
        <template #header>Global Customer Database</template>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328] flex justify-between items-center">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Network Accounts</h3>
                <input type="text" placeholder="Search username..." class="bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white text-xs font-bold tracking-widest outline-none focus:border-blue-500 w-64" />
            </div>
            
            <div class="p-6">
                <div class="w-full text-left text-gray-400 text-sm">
                    <div class="grid grid-cols-5 font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 pb-3 mb-3 text-xs">
                        <div>Username</div>
                        <div>Name</div>
                        <div>Email</div>
                        <div>Wallet Balance</div>
                        <div class="text-right">Admin Actions</div>
                    </div>
                    
                    <div v-for="customer in customers" :key="customer.id" class="grid grid-cols-5 py-4 border-b border-gray-800/50 hover:bg-[#222328] transition items-center">
                        <div class="text-white font-bold">{{ customer.username }}</div>
                        <div class="text-xs">{{ customer.first_name }} {{ customer.last_name }}</div>
                        <div class="text-xs text-gray-500">{{ customer.email }}</div>
                        <div class="text-green-400 font-mono font-bold">₱{{ Number(customer.balance).toFixed(2) }}</div>
                        <div class="text-right flex justify-end gap-2">
                            <button @click="adjustWallet(customer)" class="bg-blue-900/30 hover:bg-blue-900/50 border border-blue-500/30 text-blue-400 px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition">Adjust Balance</button>
                            <button @click="banUser(customer)" class="bg-red-900/30 hover:bg-red-900/50 border border-red-500/30 text-red-400 px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition">Ban User</button>
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

const props = defineProps({
    customers: Array
});

const adjustWallet = (customer) => {
    const amount = prompt(`Enter amount to ADD to ${customer.username}'s wallet (use negative numbers to deduct):`);
    if(amount) alert(`Backend Request: ₱${amount} processed for ${customer.username}`);
};

const banUser = (customer) => {
    if(confirm(`WARNING: Are you sure you want to globally ban ${customer.username}? They will be locked out of all 28 branches.`)) {
        alert(`Backend Request: ${customer.username} has been permanently banned.`);
    }
};
</script>