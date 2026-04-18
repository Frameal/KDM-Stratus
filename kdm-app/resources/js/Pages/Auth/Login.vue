<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans selection:bg-white selection:text-black flex flex-col">
        
        <nav class="w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24 flex-shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <a href="/" class="flex-shrink-0 flex items-center gap-4 hover:opacity-80 transition">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                    </a>
                    <div class="flex items-center space-x-6">
                        <span class="text-gray-500 hidden md:inline">Need an account?</span>
                        <a href="/register" class="bg-transparent border border-gray-600 hover:border-white text-white px-6 py-2.5 rounded font-bold transition">
                            Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow flex items-center justify-center p-6">
            <div class="bg-[#222328] w-full max-w-md rounded-2xl border border-gray-800 shadow-2xl p-8 md:p-12">
                
                <div class="text-center mb-10">
                    <h1 class="font-argentum text-4xl text-white uppercase mb-2">Welcome Back</h1>
                    <p class="text-gray-400">Log in to the KDM Stratus network.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username</label>
                        <input v-model="form.username" type="text" required autofocus
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition">
                        <div v-if="form.errors.username" class="text-red-500 text-xs mt-2">{{ form.errors.username }}</div>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition pr-12">
                        
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-10 text-gray-500 hover:text-white transition">
                            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="form.processing" 
                            class="w-full bg-white hover:bg-gray-200 text-black font-argentum uppercase tracking-widest py-4 rounded-lg text-lg transition transform hover:-translate-y-1 shadow-[0_0_20px_rgba(255,255,255,0.1)] disabled:opacity-50">
                            Log In
                        </button>
                    </div>

                </form>
            </div>
        </main>
        
        <footer class="bg-[#101113] py-6 text-center border-t border-gray-800">
            <p class="text-gray-600 font-semibold text-sm tracking-wide">© 2026 KDM Esports Cafe. Secure SD-WAN Portal.</p>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
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