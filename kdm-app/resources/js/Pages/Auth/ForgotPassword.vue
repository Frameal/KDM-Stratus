<template>
    <Head title="Forgot Password - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-blue-500 selection:text-white relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-orange-900/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-900/30 border border-orange-500/30 mb-6">
                <svg class="h-8 w-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4v-4l5.659-5.659C9.374 10.097 9 9.088 9 8a6 6 0 0112 0z"></path></svg>
            </div>
            <h2 class="font-argentum text-3xl font-extrabold text-white uppercase tracking-widest">
                Account Recovery
            </h2>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-[#18191c] py-8 px-6 shadow-2xl sm:rounded-xl border border-gray-800">
                
                <div v-if="status" class="mb-4 text-xs font-bold text-green-400 bg-green-900/20 p-4 rounded border border-green-500/30 text-center uppercase tracking-widest">
                    {{ status }}
                </div>

                <div class="flex gap-2 mb-8 bg-[#101113] p-1 rounded-lg border border-gray-800">
                    <button @click="recoveryMethod = 'email'" :class="recoveryMethod === 'email' ? 'bg-[#222328] text-white shadow' : 'text-gray-500 hover:text-gray-300'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded transition">
                        Via Email Link
                    </button>
                    <button @click="recoveryMethod = 'code'" :class="recoveryMethod === 'code' ? 'bg-[#222328] text-white shadow' : 'text-gray-500 hover:text-gray-300'" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-widest rounded transition">
                        Via Recovery Code
                    </button>
                </div>

                <form v-if="recoveryMethod === 'email'" @submit.prevent="submitEmail">
                    <p class="text-sm text-gray-400 mb-6 leading-relaxed text-center">
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
                    </p>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Registered Email</label>
                        <input v-model="emailForm.email" type="email" required autofocus class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <div v-if="emailForm.errors.email" class="text-red-500 text-xs mt-2 font-bold">{{ emailForm.errors.email }}</div>
                    </div>

                    <button type="submit" :disabled="emailForm.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-500 transition disabled:opacity-50">
                        Email Password Reset Link
                    </button>
                </form>

                <form v-if="recoveryMethod === 'code'" @submit.prevent="submitCode">
                    <p class="text-sm text-gray-400 mb-6 leading-relaxed text-center">
                        Lost access to your email? Use your permanent Recovery Code generated during registration to instantly bypass and secure your account.
                    </p>

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Username or Email</label>
                            <input v-model="codeForm.identifier" type="text" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition text-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Permanent Recovery Code</label>
                            <input v-model="codeForm.recovery_code" type="text" required placeholder="KDM-XXXXXXXX" class="w-full bg-[#101113] border border-gray-700 rounded-lg px-4 py-3 text-white font-mono tracking-widest uppercase focus:outline-none focus:border-orange-500 transition text-sm text-center">
                            <div v-if="codeForm.errors.recovery_code" class="text-red-500 text-xs mt-2 font-bold text-center">{{ codeForm.errors.recovery_code }}</div>
                        </div>

                        <div class="border-t border-gray-800 my-4 pt-4"></div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">New Password</label>
                            <input v-model="codeForm.password" type="password" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition text-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Confirm New Password</label>
                            <input v-model="codeForm.password_confirmation" type="password" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition text-sm"
                                :class="{'border-red-500': codeForm.password_confirmation && codeForm.password !== codeForm.password_confirmation}">
                            <div v-if="codeForm.errors.password" class="text-red-500 text-xs mt-2 font-bold">{{ codeForm.errors.password }}</div>
                        </div>
                    </div>

                    <button type="submit" :disabled="codeForm.processing || (codeForm.password !== codeForm.password_confirmation)" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-orange-600 hover:bg-orange-500 transition disabled:opacity-50">
                        Verify & Update Password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="/login" class="text-xs font-bold text-gray-500 hover:text-white uppercase tracking-widest transition">
                        &larr; Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const recoveryMethod = ref('email');

const emailForm = useForm({
    email: '',
});

const codeForm = useForm({
    identifier: '',
    recovery_code: '',
    password: '',
    password_confirmation: '',
});

const submitEmail = () => {
    emailForm.post(route('password.email'));
};

const submitCode = () => {
    codeForm.post(route('password.recover.code'), {
        onSuccess: () => codeForm.reset(),
    });
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
</style>