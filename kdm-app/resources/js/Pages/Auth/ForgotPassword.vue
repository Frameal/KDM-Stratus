<template>
    <Head title="Forgot Password - KDM Stratus" />

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
                System Recovery
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400 font-bold tracking-widest uppercase px-4">
                Forgot your password? Let us know your email address and we will email you a password reset link.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-[#18191c] py-8 px-4 shadow-2xl sm:rounded-xl border border-gray-800 sm:px-10">
                
                <div v-if="status" class="mb-6 p-4 bg-green-900/20 border border-green-500/50 rounded-lg text-sm font-bold text-green-400 uppercase tracking-widest text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                        <input id="email" type="email" v-model="form.email" required autofocus
                            class="appearance-none block w-full px-4 py-3 border border-gray-700 rounded-lg shadow-sm placeholder-gray-500 bg-[#1c1d21] text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition font-bold" />
                        <div v-if="form.errors.email" class="mt-2 text-xs text-red-500 font-bold">{{ form.errors.email }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <Link :href="route('login')" class="text-xs font-bold text-gray-500 hover:text-white uppercase tracking-widest transition">
                            &larr; Back to Login
                        </Link>
                        <button type="submit" :disabled="form.processing" 
                            class="flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50">
                            Email Reset Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

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

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>