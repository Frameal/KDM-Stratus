<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans flex flex-col">
        
        <nav class="w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24 flex-shrink-0 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <a href="/" class="flex-shrink-0 flex items-center gap-4 hover:opacity-80 transition">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                    </a>
                </div>
            </div>
        </nav>

        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-[#222328] w-full max-w-lg rounded-2xl border border-gray-800 shadow-2xl p-8 md:p-12 relative overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-1 bg-blue-600"></div>

                <div class="text-center mb-8">
                    <h1 class="font-argentum text-3xl text-white uppercase mb-2">Password Reset</h1>
                    <p class="text-sm text-gray-400">
                        Forgot your password? No problem. Just let us know your email address and we will email you a secure password reset link.
                    </p>
                </div>

                <div v-if="status" class="mb-6 p-4 bg-green-900/20 border border-green-500/50 rounded-lg text-green-400 text-sm font-bold text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                        <input v-model="form.email" type="email" required autofocus
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.email }}</div>
                    </div>

                    <button type="submit" :disabled="form.processing" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest py-4 rounded-lg text-sm transition transform hover:-translate-y-1 shadow-lg disabled:opacity-50 disabled:hover:translate-y-0 cursor-pointer">
                        Email Password Reset Link
                    </button>

                    <div class="text-center mt-4">
                        <a :href="route('login')" class="text-gray-500 hover:text-white text-xs uppercase tracking-widest underline transition">
                            Return to Login
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
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
</style>