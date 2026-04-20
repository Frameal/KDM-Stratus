<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans flex flex-col">
        
        <nav class="w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24 flex-shrink-0 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <a :href="route('dashboard')" class="flex-shrink-0 flex items-center gap-4 hover:opacity-80 transition">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                    </a>
                    
                    <div class="flex items-center space-x-4">
                        <a :href="route('dashboard')" class="text-gray-400 hover:text-white text-sm font-bold uppercase tracking-widest transition">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow max-w-4xl w-full mx-auto p-6 my-8 space-y-6">
            
            <div class="bg-[#222328] rounded-2xl border border-gray-800 p-8 shadow-lg flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-argentum text-white uppercase tracking-widest mb-1">Account Identity</h2>
                    <p class="text-gray-400 text-sm">Manage your personal information and contact details.</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-1">Member Since</p>
                    <p class="text-blue-400 font-bold text-lg">{{ formattedCreationDate }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ membershipDuration }}</p>
                </div>
            </div>

            <div class="bg-[#222328] rounded-2xl border border-gray-800 p-8 shadow-lg">
                
                <div v-if="status === 'profile-updated'" class="mb-6 p-4 bg-green-900/20 border border-green-500/50 rounded-lg text-green-400 text-sm font-bold tracking-wide">
                    Profile details updated successfully.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">First Name</label>
                            <input v-model="form.first_name" type="text" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <div v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Last Name</label>
                            <input v-model="form.last_name" type="text" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <div v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username</label>
                            <input v-model="form.username" type="text" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                            <input v-model="form.email" type="email" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">Warning: Changing this will lock your account until re-verified.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Contact Number</label>
                            <input v-model="form.contact_number" type="text" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <div v-if="form.errors.contact_number" class="text-red-500 text-xs mt-1">{{ form.errors.contact_number }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Date of Birth</label>
                            <input v-model="form.dob" type="date" required class="w-full bg-[#18191c] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition [color-scheme:dark]">
                            <div v-if="form.errors.dob" class="text-red-500 text-xs mt-1">{{ form.errors.dob }}</div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest px-8 py-3 rounded-lg text-sm transition shadow-lg disabled:opacity-50">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    username: user.username,
    email: user.email,
    contact_number: user.contact_number,
    dob: user.dob,
});

// Calculate Membership Display Strings
const formattedCreationDate = computed(() => {
    return new Date(user.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

const membershipDuration = computed(() => {
    const created = new Date(user.created_at);
    const now = new Date();
    const diffTime = Math.abs(now - created);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return '(Joined today)';
    if (diffDays < 30) return `(${diffDays} days ago)`;
    const diffMonths = Math.floor(diffDays / 30);
    return `(${diffMonths} months ago)`;
});

const submit = () => {
    form.patch(route('profile.update'));
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