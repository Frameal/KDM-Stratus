<template>
    <Head title="Verify Email - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-blue-500 selection:text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-green-900/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="flex justify-center mb-6">
                <img src="/images/logo.png" alt="KDM Logo" class="h-20 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]" onerror="this.style.display='none';" />
            </div>
            <h2 class="mt-2 text-center font-argentum text-3xl font-extrabold text-white uppercase tracking-widest">
                Verify Identity
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-[#18191c] py-8 px-6 shadow-2xl sm:rounded-xl border border-gray-800 text-center">
                
                <svg class="mx-auto h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>

                <p class="text-sm text-gray-400 font-bold tracking-widest uppercase leading-relaxed mb-6">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                </p>

                <div v-if="verificationLinkSent" class="mb-6 p-4 bg-green-900/20 border border-green-500/50 rounded-lg text-xs font-bold text-green-400 uppercase tracking-widest">
                    A new verification link has been transmitted.
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <button type="submit" :disabled="form.processing" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-500 transition disabled:opacity-50">
                        Resend Verification Email
                    </button>
                    
                    <Link :href="route('logout')" method="post" as="button"
                        class="w-full flex justify-center py-3 px-4 border border-gray-700 rounded-lg text-sm font-bold uppercase tracking-widest text-gray-400 hover:text-white hover:bg-gray-800 transition">
                        Log Out
                    </Link>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>