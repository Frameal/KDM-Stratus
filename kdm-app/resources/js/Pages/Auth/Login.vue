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
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username or Email</label>
                        <input v-model="form.username" type="text" required autofocus
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <div v-if="form.errors.username" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.username }}</div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Password</label>
                            <a :href="route('password.request')" class="text-xs text-blue-500 hover:text-blue-400 font-bold tracking-wide transition">
                                Forgot your password?
                            </a>
                        </div>
                        <input v-model="form.password" type="password" required
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.password }}</div>
                    </div>

                    <div class="block">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.remember" class="rounded border-gray-700 bg-[#18191c] text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ml-2 text-sm text-gray-400 font-semibold tracking-wide">Keep me securely logged in</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest py-4 rounded-lg text-lg transition transform hover:-translate-y-1 shadow-lg disabled:opacity-50 disabled:hover:translate-y-0 cursor-pointer">
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