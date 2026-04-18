
<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans selection:bg-white selection:text-black flex flex-col">
        
        <nav class="w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24 flex-shrink-0 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <div class="flex-shrink-0 flex items-center gap-4">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                    </div>
                    </div>
            </div>
        </nav>

        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-[#222328] w-full max-w-xl rounded-2xl border border-gray-800 shadow-2xl p-8 md:p-12 text-center relative overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-blue-400"></div>

                <h1 class="font-argentum text-3xl text-white uppercase mb-4 mt-2">Security Check</h1>
                
                <div class="mb-8 text-sm text-gray-400 leading-relaxed bg-[#18191c] p-6 rounded-lg border border-gray-700">
                    Welcome to the KDM Stratus network. Before we can grant you access to the reservation grid, we need to verify your identity. <br><br>
                    <span class="text-white font-bold tracking-wide">Please click the secure link we just sent to your email address.</span>
                </div>

                <div v-if="verificationLinkSent" class="animate-fade-in mb-8 p-4 bg-green-900/20 border border-green-500/50 rounded-lg text-green-400 text-sm font-bold tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    A fresh verification link has been transmitted.
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <button type="submit" :disabled="form.processing" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest py-4 rounded-lg text-sm transition transform hover:-translate-y-1 shadow-lg disabled:opacity-50 disabled:hover:translate-y-0 cursor-pointer">
                        Resend Verification Email
                    </button>

                    <Link :href="route('logout')" method="post" as="button" 
                        class="w-full bg-transparent border border-gray-700 hover:border-white text-gray-400 hover:text-white uppercase tracking-widest py-3 rounded-lg text-xs font-bold transition">
                        Return to Log In
                    </Link>
                </form>
            </div>
        </main>
        
        <footer class="bg-[#101113] py-6 text-center border-t border-gray-800">
            <p class="text-gray-600 font-semibold text-sm tracking-wide">© 2026 KDM Esports Cafe. Secure SD-WAN Portal.</p>
        </footer>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

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

<style>
@font-face {
    font-family: 'ArgentumNovus';
    src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype');
    font-weight: 600;
}
@font-face {
    font-family: 'AZNUnified';
    src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype');
    font-weight: normal;
    font-style: italic;
}
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
