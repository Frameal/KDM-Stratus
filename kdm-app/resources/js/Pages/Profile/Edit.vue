<template>
    <Head title="Account Details - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] text-gray-200 font-sans selection:bg-blue-500 selection:text-white pb-20 relative">
        
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-900/10 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-40 left-0 w-72 h-72 bg-green-900/5 rounded-full blur-[80px]"></div>
        </div>

        <nav class="w-full z-50 bg-[#101113]/90 backdrop-blur-md border-b border-gray-800 h-24 flex-shrink-0 shadow-lg sticky top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <a href="/dashboard" class="flex items-center gap-4 hover:scale-105 transition transform cursor-pointer">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-12 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-2xl text-white">KDM</span>
                    </a>
                    <a href="/dashboard" class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-widest transition flex items-center gap-2">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8 relative z-10">
            
            <div class="mb-10 flex justify-between items-end">
                <div>
                    <h2 class="font-argentum text-3xl text-white uppercase tracking-widest">Account Details</h2>
                    <p class="text-sm text-gray-400 mt-2 font-bold tracking-wide">Manage your Stratus network profile and security settings.</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Member Since</p>
                    <p class="text-sm font-bold text-white tracking-widest">{{ formattedCreationDate }}</p>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
                <div class="p-8">
                    <h3 class="text-lg font-argentum text-white uppercase tracking-widest mb-1">Profile Information</h3>
                    <p class="text-sm text-gray-400 mb-6">Update your account's profile information and email address.</p>

                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">First Name</label>
                                <input v-model="profileForm.first_name" type="text" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition font-bold" />
                                <div v-if="profileForm.errors.first_name" class="mt-1 text-xs text-red-500 font-bold">{{ profileForm.errors.first_name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Last Name</label>
                                <input v-model="profileForm.last_name" type="text" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition font-bold" />
                                <div v-if="profileForm.errors.last_name" class="mt-1 text-xs text-red-500 font-bold">{{ profileForm.errors.last_name }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username</label>
                                <input v-model="profileForm.username" type="text" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition font-bold" />
                                <div v-if="profileForm.errors.username" class="mt-1 text-xs text-red-500 font-bold">{{ profileForm.errors.username }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                                <input v-model="profileForm.email" type="email" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition font-bold" />
                                <div v-if="profileForm.errors.email" class="mt-1 text-xs text-red-500 font-bold">{{ profileForm.errors.email }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Contact Number</label>
                            <input v-model="profileForm.contact_number" type="text" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition font-bold" />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" :disabled="profileForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-bold uppercase tracking-widest px-6 py-3 rounded-lg text-xs transition shadow-lg disabled:opacity-50">
                                Save Changes
                            </button>
                            <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                <p v-if="profileForm.recentlySuccessful" class="text-sm text-green-400 font-bold uppercase tracking-widest">Saved.</p>
                            </transition>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
                <div class="p-8">
                    <h3 class="text-lg font-argentum text-white uppercase tracking-widest mb-1">Update Password</h3>
                    <p class="text-sm text-gray-400 mb-6">Ensure your account is using a long, random password to stay secure.</p>

                    <form @submit.prevent="updatePassword" class="space-y-6 max-w-xl">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Current Password</label>
                            <input v-model="passwordForm.current_password" type="password" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition" />
                            <div v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-500 font-bold">{{ passwordForm.errors.current_password }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">New Password</label>
                            <input v-model="passwordForm.password" type="password" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition" />
                            
                            <div class="mt-4 bg-[#101113] border border-gray-700 rounded-lg p-5 shadow-inner" :class="{'border-green-500/50 bg-green-900/10': isPasswordValid}">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Security Requirements</p>
                                <div class="grid grid-cols-2 gap-3 text-xs font-bold">
                                    <span :class="passwordLength ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                        <svg v-if="passwordLength" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> 8+ Characters
                                    </span>
                                    <span :class="passwordUpper ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                        <svg v-if="passwordUpper" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> Uppercase Letter
                                    </span>
                                    <span :class="passwordNumber ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                        <svg v-if="passwordNumber" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> One Number
                                    </span>
                                    <span :class="passwordSymbol ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                        <svg v-if="passwordSymbol" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span v-else class="w-4 h-4 border-2 border-gray-600 rounded-full"></span> Special Symbol
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Confirm Password</label>
                            <input v-model="passwordForm.password_confirmation" type="password" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 transition" />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" :disabled="passwordForm.processing || (passwordForm.password.length > 0 && !isPasswordValid)" class="bg-gray-700 hover:bg-gray-600 text-white font-bold uppercase tracking-widest px-6 py-3 rounded-lg text-xs transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                Update Security
                            </button>
                            <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                <p v-if="passwordForm.recentlySuccessful" class="text-sm text-green-400 font-bold uppercase tracking-widest">Secured.</p>
                            </transition>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

// Format the date dynamically
const formattedCreationDate = computed(() => {
    return new Date(user.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

const profileForm = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    username: user.username,
    email: user.email,
    contact_number: user.contact_number,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Password Validation Logic
const passwordLength = computed(() => passwordForm.password.length >= 8);
const passwordUpper = computed(() => /[A-Z]/.test(passwordForm.password));
const passwordNumber = computed(() => /[0-9]/.test(passwordForm.password));
const passwordSymbol = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(passwordForm.password));
const isPasswordValid = computed(() => passwordLength.value && passwordUpper.value && passwordNumber.value && passwordSymbol.value);

const updateProfile = () => {
    profileForm.patch(route('profile.update'));
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
        },
    });
};
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
@font-face { font-family: 'AZNUnified'; src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype'); font-weight: normal; font-style: italic; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }
</style>