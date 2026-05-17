<template>
    <Head title="Enterprise Users - KDM Stratus" />

    <AdminLayout>
        <template #header>Global Customer Database</template>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4" :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'">
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Registered Network Accounts</h3>
            </div>
            
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[1000px]">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                            <th class="pb-3 font-medium">Member Since</th>
                            <th class="pb-3 font-medium">Username</th>
                            <th class="pb-3 font-medium">Last Branch Activity</th>
                            <th class="pb-3 font-medium">Wallet Balance</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 text-right font-medium">Admin Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in customers" :key="user.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                            <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                            <td class="py-4 text-blue-400 font-bold text-xs">@{{ user.username }}</td>
                            <td class="py-4 text-gray-300 text-xs uppercase tracking-widest">{{ user.last_branch }}</td>
                            <td class="py-4 text-green-400 font-mono font-bold">₱{{ Number(user.balance).toFixed(2) }}</td>
                            <td class="py-4">
                                <span v-if="user.is_banned" class="bg-red-900/30 text-red-500 border border-red-500/30 px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest">Banned</span>
                                <span v-else class="bg-green-900/30 text-green-500 border border-green-500/30 px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest">Active</span>
                            </td>
                            <td class="py-4 text-right flex justify-end gap-2">
                                <button @click="openViewModal(user)" class="bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-gray-300 px-3 py-1.5 rounded text-[9px] font-bold uppercase tracking-widest transition">View Account</button>
                                <button @click="openBalanceModal(user)" class="bg-blue-900/30 hover:bg-blue-900/60 border border-blue-500/50 text-blue-400 px-3 py-1.5 rounded text-[9px] font-bold uppercase tracking-widest transition">Adjust Balance</button>
                                <button @click="openBanModal(user)" :class="user.is_banned ? 'bg-orange-900/30 text-orange-400 border-orange-500/50 hover:bg-orange-900/60' : 'bg-red-900/30 text-red-400 border-red-500/50 hover:bg-red-900/60'" class="border px-3 py-1.5 rounded text-[9px] font-bold uppercase tracking-widest transition">
                                    {{ user.is_banned ? 'Lift Ban' : 'Ban User' }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        <div v-if="showViewModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showViewModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative">
                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Account Dossier</h3>
                    <button @click="showViewModal = false" class="text-gray-500 hover:text-white transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-8 space-y-4">
                    <div class="grid grid-cols-3 border-b border-gray-800 pb-4">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-widest col-span-1">Username</div>
                        <div class="text-white font-bold col-span-2">@{{ activeUser.username }}</div>
                    </div>
                    <div class="grid grid-cols-3 border-b border-gray-800 pb-4">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-widest col-span-1">Full Name</div>
                        <div class="text-gray-300 col-span-2">{{ activeUser.first_name }} {{ activeUser.last_name }}</div>
                    </div>
                    <div class="grid grid-cols-3 border-b border-gray-800 pb-4">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-widest col-span-1">Email</div>
                        <div class="text-blue-400 col-span-2">{{ activeUser.email }}</div>
                    </div>
                    <div class="grid grid-cols-3 border-b border-gray-800 pb-4">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-widest col-span-1">Phone</div>
                        <div class="text-gray-300 font-mono col-span-2">{{ activeUser.contact_number || 'Not Provided' }}</div>
                    </div>
                    <div class="grid grid-cols-3">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-widest col-span-1">Joined</div>
                        <div class="text-gray-300 font-mono col-span-2">{{ new Date(activeUser.created_at).toLocaleString() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showBalanceModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showBalanceModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest text-center">Modify Ledger</h3>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Target Account</p>
                        <p class="text-blue-400 font-bold text-lg">@{{ activeUser.username }}</p>
                    </div>
                    <form @submit.prevent>
                        <input type="number" v-model.number="balanceForm.amount" min="1" step="1" placeholder="Enter amount (₱)" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white font-mono text-center text-xl outline-none focus:border-blue-500 mb-6" />
                        <div class="flex gap-4">
                            <button @click="submitBalance('minus')" :disabled="balanceForm.processing" class="flex-1 bg-red-900/30 hover:bg-red-900/60 border border-red-500/50 text-red-400 font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">
                                Deduct (-)
                            </button>
                            <button @click="submitBalance('add')" :disabled="balanceForm.processing" class="flex-1 bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(22,163,74,0.4)] text-xs">
                                Add (+)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="showBanModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showBanModal = false">
            <div class="bg-[#18191c] border rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative" :class="activeUser.is_banned ? 'border-orange-900/50' : 'border-red-900/50'">
                <div class="px-6 py-4 border-b flex justify-center items-center" :class="activeUser.is_banned ? 'bg-orange-900/20 border-orange-900/50' : 'bg-red-900/20 border-red-900/50'">
                    <h3 class="font-argentum text-xl uppercase tracking-widest" :class="activeUser.is_banned ? 'text-orange-500' : 'text-red-500'">Confirm Override</h3>
                </div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">
                        {{ activeUser.is_banned ? `Lift network restriction for @${activeUser.username}?` : `Ban @${activeUser.username} from the network?` }}
                    </p>
                    <div class="flex gap-4 pt-2">
                        <button @click="showBanModal = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Cancel</button>
                        <button @click="executeBan" class="flex-1 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-lg text-xs" :class="activeUser.is_banned ? 'bg-orange-600 hover:bg-orange-500' : 'bg-red-600 hover:bg-red-500'">
                            {{ activeUser.is_banned ? 'Unban User' : 'Execute Ban' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ customers: Array });

// TOASTS
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 4000);
};

// MODAL STATES
const activeUser = ref(null);
const showViewModal = ref(false);
const showBalanceModal = ref(false);
const showBanModal = ref(false);
const balanceForm = useForm({ amount: null, type: '' });

const openViewModal = (user) => { activeUser.value = user; showViewModal.value = true; };
const openBalanceModal = (user) => { activeUser.value = user; balanceForm.amount = null; showBalanceModal.value = true; };
const openBanModal = (user) => { activeUser.value = user; showBanModal.value = true; };

const submitBalance = (type) => {
    if (!balanceForm.amount || balanceForm.amount <= 0) {
        showToast("Enter a valid amount.", "error"); return;
    }
    balanceForm.type = type;
    balanceForm.post(route('hq.users.balance', activeUser.value.id), {
        preserveScroll: true,
        onSuccess: () => { showBalanceModal.value = false; showToast("Ledger updated."); },
        onError: (err) => { showToast(err.balance || "Transaction failed.", "error"); }
    });
};

const executeBan = () => {
    router.patch(route('hq.users.ban', activeUser.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => { showBanModal.value = false; showToast("Network restriction updated."); }
    });
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(50px); }
</style>