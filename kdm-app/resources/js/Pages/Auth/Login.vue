<template>
    <Head title="Secure Portal Login - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-blue-500 selection:text-white relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-green-900/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="flex justify-center mb-6">
                <a href="/" class="hover:scale-105 transition transform cursor-pointer">
                    <img src="/images/logo.png" alt="KDM Logo" class="h-20 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]" onerror="this.style.display='none';" />
                </a>
            </div>
            <h2 class="mt-2 text-center font-argentum text-3xl font-extrabold text-white uppercase tracking-widest">
                KDM Stratus
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400 font-bold tracking-widest uppercase">
                Secure Terminal Access
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-[#18191c] py-8 px-4 shadow-2xl sm:rounded-xl border border-gray-800 sm:px-10">
                
                <div v-if="$page.props.errors.oauth_error" class="mb-6 p-4 bg-red-900/20 border border-red-500/50 rounded-lg animate-fade-in text-center shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-bold text-red-500 uppercase tracking-widest text-sm">Authentication Failed</span>
                    </div>
                    <p class="text-xs text-red-400 font-bold uppercase tracking-wider mt-2">{{ $page.props.errors.oauth_error }}</p>
                </div>

                <div class="mb-6" v-if="loginStep === 1">
                    <a href="/auth/google" class="w-full flex justify-center items-center py-3 px-4 border border-gray-700 rounded-lg shadow-sm text-sm font-bold text-white bg-[#1c1d21] hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Sign in with Google
                    </a>
                </div>

                <div class="relative mb-6" v-if="loginStep === 1">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-700"></div></div>
                    <div class="relative flex justify-center text-sm"><span class="px-2 bg-[#18191c] text-gray-500 font-bold uppercase tracking-widest text-xs">Or authenticate manually</span></div>
                </div>


                <div v-if="status" class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded-lg text-center">
                    <p class="text-xs font-bold text-green-400 uppercase tracking-widest">{{ status }}</p>
                </div>
                <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded-lg text-center">
                    <p class="text-xs font-bold text-red-400 uppercase tracking-widest">{{ form.errors.username || form.errors.password }}</p>
                </div>


                <form v-if="loginStep === 1" @submit.prevent="submitCredentials" class="space-y-6">
                    <div>
                        <label for="username" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Username or Email</label>
                        <div class="mt-2">
                            <input id="username" type="text" v-model="form.username" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-700 rounded-lg shadow-sm placeholder-gray-500 bg-[#1c1d21] text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition font-bold"
                                :class="{'border-red-500': form.errors.username}" />
                        </div>
                        <div v-if="form.errors.username" class="mt-2 text-xs text-red-500 font-bold">{{ form.errors.username }}</div>
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password" required 
                                class="appearance-none block w-full px-4 py-3 border border-gray-700 rounded-lg shadow-sm placeholder-gray-500 bg-[#1c1d21] text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition font-bold pr-12"
                                :class="{'border-red-500': form.errors.password}" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-3 text-gray-500 hover:text-white transition">
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-2 text-xs text-red-500 font-bold">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" v-model="form.remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-[#1c1d21]">
                            <label for="remember" class="ml-2 block text-xs text-gray-400 font-bold uppercase tracking-wider">
                                Remember me
                            </label>
                        </div>
                        <div class="text-xs">
                            <Link :href="route('password.request')" class="font-bold text-blue-500 hover:text-blue-400 uppercase tracking-wider transition">
                                Forgot password?
                            </Link>
                        </div>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50">
                            Authenticate
                        </button>
                    </div>
                </form>

                <div v-if="loginStep === 2" class="space-y-6 animate-fade-in">
                    <div class="text-center mb-6">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-900/30 border border-blue-500/30 mb-4">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase tracking-widest">Email Verification</h3>
                        <p class="text-sm text-gray-400 mt-2">We sent a 6-digit code to <span class="text-white font-bold">{{ masked_email }}</span>.</p>
                    </div>

                    <form @submit.prevent="submitMfa">
                        
                        <div class="flex justify-center gap-2 mb-2">
                            <input v-for="(digit, index) in 6" :key="index" :id="`login-digit-${index}`" type="text" maxlength="1" 
                                v-model="codeArray[index]" 
                                @input="moveToNext($event, index)" 
                                @keydown="handleBackspace($event, index)"
                                @paste="handlePaste"
                                class="w-12 h-14 text-center text-2xl font-bold bg-[#1c1d21] border border-gray-700 rounded-lg text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition" />
                        </div>
                        
                        <div v-if="mfaForm.errors.code" class="text-center text-xs text-red-500 font-bold mt-2 mb-4">{{ mfaForm.errors.code }}</div>

                        <button type="submit" :disabled="mfaForm.processing || !isCodeComplete" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold uppercase tracking-widest text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed mt-6">
                            Verify Code
                        </button>
                    </form>
                    
                    <div class="text-center mt-6">
                        <button @click="resetToStep1" class="text-xs font-bold text-gray-500 hover:text-white uppercase tracking-widest transition">
                            &larr; Back to login
                        </button>
                    </div>
                </div>

            </div>
            
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">
                    Don't have an account? 
                    <a href="/register" class="text-blue-500 hover:text-blue-400 ml-1">Create one here</a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    requires_mfa: Boolean,
    masked_email: String,
});

const loginStep = ref(props.requires_mfa ? 2 : 1);
const codeArray = ref(['', '', '', '', '', '']);
const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const mfaForm = useForm({
    code: ''
});

watch(() => props.requires_mfa, (newVal) => {
    if (newVal) loginStep.value = 2;
});

const isCodeComplete = computed(() => {
    return codeArray.value.join('').length === 6;
});

const moveToNext = (event, index) => {
    codeArray.value[index] = event.target.value.replace(/\D/g, '');
    if (event.target.value && index < 5) {
        document.getElementById(`login-digit-${index + 1}`).focus();
    }
};

const handleBackspace = (event, index) => {
    if (event.key === 'Backspace' && !codeArray.value[index] && index > 0) {
        document.getElementById(`login-digit-${index - 1}`).focus();
    }
};

// THE PASTE HANDLER
const handlePaste = (event) => {
    event.preventDefault();
    const pastedData = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    
    if (pastedData) {
        for (let i = 0; i < pastedData.length; i++) {
            codeArray.value[i] = pastedData[i];
        }
        const nextFocus = Math.min(pastedData.length, 5);
        document.getElementById(`login-digit-${nextFocus}`).focus();
    }
};

const resetToStep1 = () => {
    loginStep.value = 1;
    form.password = '';
    mfaForm.reset();
    form.clearErrors();
    mfaForm.clearErrors();
};

const submitCredentials = () => {
    form.post(route('login'), {
        preserveScroll: true,
        onError: () => {
            form.reset('password');
        }
    });
};

const submitMfa = () => {
    mfaForm.code = codeArray.value.join('');
    mfaForm.post(route('mfa.verify'), {
        preserveScroll: true,
        onError: () => {
            codeArray.value = ['', '', '', '', '', ''];
            document.getElementById('login-digit-0').focus();
        }
    });
};
</script>

<style scoped>
@font-face {
    font-family: 'ArgentumNovus';
    src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype');
    font-weight: 600;
}
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>