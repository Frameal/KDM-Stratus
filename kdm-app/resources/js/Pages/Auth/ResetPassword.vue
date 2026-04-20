<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans flex flex-col">
        
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

        <main class="flex-grow flex items-center justify-center p-6 my-8">
            <div class="bg-[#222328] w-full max-w-lg rounded-2xl border border-gray-800 shadow-2xl p-8 md:p-12 relative overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-1 bg-blue-600"></div>

                <div class="text-center mb-8">
                    <h1 class="font-argentum text-3xl text-white uppercase mb-2">Create New Password</h1>
                    <p class="text-sm text-gray-400">Please enter your new secure password below.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Account Email</label>
                        <div class="flex items-center bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 opacity-70 cursor-not-allowed">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <input v-model="form.email" type="email" readonly required
                                class="w-full bg-transparent text-gray-400 focus:outline-none cursor-not-allowed select-none pointer-events-none">
                        </div>
                        <p class="text-xs text-gray-500 mt-1 font-semibold">Email cannot be changed during recovery.</p>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">New Password</label>
                        <div class="relative">
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required autofocus
                                class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition pr-12">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-3 text-gray-500 hover:text-white transition">
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.password }}</div>
                        
                        <div class="mt-4 bg-[#101113] border border-gray-700 rounded-lg p-4 shadow-inner" :class="{'border-green-500/50 bg-green-900/10': isPasswordValid}">
                            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                                <span :class="passwordLength ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2">
                                    <svg v-if="passwordLength" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> 8+ Chars
                                </span>
                                <span :class="passwordUpper ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2">
                                    <svg v-if="passwordUpper" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> Uppercase
                                </span>
                                <span :class="passwordNumber ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2">
                                    <svg v-if="passwordNumber" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> Number
                                </span>
                                <span :class="passwordSymbol ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2">
                                    <svg v-if="passwordSymbol" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> Symbol
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Confirm Password</label>
                        <input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" required
                            class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition pr-12"
                            :class="{'border-red-500': form.password_confirmation.length > 0 && !passwordsMatch}">
                        
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-10 text-gray-500 hover:text-white transition">
                            <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing || !isFormFullyValid" 
                            class="w-full font-argentum uppercase tracking-widest py-4 rounded-lg text-lg transition-all duration-300"
                            :class="isFormFullyValid ? 'bg-blue-600 hover:bg-blue-500 text-white shadow-lg transform hover:-translate-y-1 cursor-pointer' : 'bg-gray-800 text-gray-500 cursor-not-allowed'">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Reactive Password Rules
const passwordLength = computed(() => form.password.length >= 8);
const passwordUpper = computed(() => /[A-Z]/.test(form.password));
const passwordNumber = computed(() => /[0-9]/.test(form.password));
const passwordSymbol = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(form.password));
const isPasswordValid = computed(() => passwordLength.value && passwordUpper.value && passwordNumber.value && passwordSymbol.value);
const passwordsMatch = computed(() => form.password_confirmation.length > 0 && form.password === form.password_confirmation);

const isFormFullyValid = computed(() => isPasswordValid.value && passwordsMatch.value);

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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