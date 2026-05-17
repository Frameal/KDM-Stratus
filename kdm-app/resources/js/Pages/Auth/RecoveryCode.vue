<template>
    <Head title="Save Recovery Code - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-blue-500 selection:text-white relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute top-20 left-20 w-96 h-96 bg-red-900/10 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 right-20 w-72 h-72 bg-blue-900/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900/30 border border-red-500/30 mb-6">
                <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="font-argentum text-3xl font-extrabold text-white uppercase tracking-widest">
                Save Your Code
            </h2>
            <p class="mt-3 text-sm text-gray-400 font-bold tracking-widest uppercase px-4">
                This is the only time this code will be shown.
            </p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-[#18191c] py-8 px-8 shadow-2xl sm:rounded-xl border border-gray-800 text-center">
                
                <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                    If you ever lose access to your email or forget your password, this permanent recovery code is the <strong class="text-red-400">only way</strong> to bypass MFA and recover your wallet balance.
                </p>

                <div class="bg-[#101113] border-2 border-dashed border-gray-700 p-6 rounded-xl mb-8 relative group">
                    <div class="text-3xl font-mono font-bold tracking-[0.2em] text-white select-all">
                        {{ recoveryCode }}
                    </div>
                    <button @click="copyCode" class="absolute top-2 right-2 text-gray-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <a href="/dashboard" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-green-600 hover:bg-green-500 transition">
                    I Have Saved It Safely
                </a>
                
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    recoveryCode: String
});

const copyCode = () => {
    navigator.clipboard.writeText(props.recoveryCode);
    alert('Code copied to clipboard!');
};
</script>

<style scoped>
@font-face {
    font-family: 'ArgentumNovus';
    src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype');
    font-weight: 600;
}
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>