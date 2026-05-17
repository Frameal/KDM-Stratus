<template>
    <Head title="Pricing Matrix - KDM Stratus" />

    <AdminLayout>
        <template #header>Global Pricing Matrix</template>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4 border-green-500">
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden max-w-3xl">
            <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Network Access Rates</h3>
                <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-widest">Changes apply instantly to all customer terminals.</p>
            </div>
            
            <form @submit.prevent="submitPricing" class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-[#101113] p-6 rounded-xl border border-gray-800">
                        <label class="block text-xs font-bold text-orange-400 uppercase tracking-widest mb-3">15 Minute Grace</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500 font-bold">₱</span>
                            <input type="number" v-model.number="form.price_15m" step="0.50" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg pl-8 pr-4 py-3 text-white font-mono text-xl outline-none focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="bg-[#101113] p-6 rounded-xl border border-gray-800">
                        <label class="block text-xs font-bold text-orange-400 uppercase tracking-widest mb-3">30 Minute Session</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500 font-bold">₱</span>
                            <input type="number" v-model.number="form.price_30m" step="0.50" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg pl-8 pr-4 py-3 text-white font-mono text-xl outline-none focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="bg-[#101113] p-6 rounded-xl border border-gray-800">
                        <label class="block text-xs font-bold text-orange-400 uppercase tracking-widest mb-3">60 Minute Standard</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500 font-bold">₱</span>
                            <input type="number" v-model.number="form.price_60m" step="0.50" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg pl-8 pr-4 py-3 text-white font-mono text-xl outline-none focus:border-blue-500" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 flex justify-end">
                    <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(37,99,235,0.4)] text-xs">
                        Synchronize Pricing
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ settings: Object });

const form = useForm({
    price_15m: parseFloat(props.settings.price_15m || 5),
    price_30m: parseFloat(props.settings.price_30m || 10),
    price_60m: parseFloat(props.settings.price_60m || 25),
});

const toast = ref({ show: false, message: '' });

const submitPricing = () => {
    form.post(route('hq.pricing.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.value = { show: true, message: "Global pricing updated successfully." };
            setTimeout(() => { toast.value.show = false; }, 4000);
        }
    });
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(50px); }
</style>