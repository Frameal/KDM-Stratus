<template>
    <Head title="Branch Operations - KDM Stratus" />

    <div class="flex h-screen bg-[#1c1d21] text-gray-200 font-sans overflow-hidden">
        
        <aside class="w-72 bg-[#18191c] border-r border-gray-800 flex flex-col justify-between flex-shrink-0 shadow-2xl z-20">
            <div>
                <div class="p-6 border-b border-gray-800 bg-[#101113]">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-10 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-2xl text-white">KDM</span>
                    </div>
                    <div class="font-bold text-white tracking-wide">{{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}</div>
                    
                    <div class="mt-2 inline-block bg-green-900/30 border border-green-500/30 px-3 py-1 rounded text-[10px] font-bold text-green-400 uppercase tracking-widest shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-1 animate-pulse"></span>
                        Branch: {{ branch ? branch.name : 'Unknown' }}
                    </div>
                </div>

                <nav class="p-4 space-y-2">
                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-2 pl-3">Shift Overview</p>
                    <button class="w-full text-left px-4 py-3 rounded-lg bg-green-600/10 text-green-400 font-bold tracking-wide transition border border-green-500/20">
                        Local Telemetry (Capacity & Revenue)
                    </button>

                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-6 mb-2 pl-3">Hardware Control</p>
                    <button class="w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition">
                        Terminal Grid (Status/Add/Delete)
                    </button>

                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-6 mb-2 pl-3">Customer Service</p>
                    <button class="w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition flex justify-between items-center">
                        Active Sessions 
                        <span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full">New</span>
                    </button>
                    <button class="w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition">
                        Local Feedback Tickets
                    </button>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-800">
                <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-900/20 hover:bg-red-500 text-red-500 hover:text-white rounded-lg font-bold tracking-widest uppercase transition text-xs border border-red-900/50 hover:border-red-500">
                    Secure Log Out
                </Link>
            </div>
        </aside>

        <main class="flex-grow flex flex-col bg-[#1c1d21] relative overflow-y-auto">
            <header class="bg-[#222328] border-b border-gray-800 px-8 py-6 sticky top-0 z-10 shadow-sm flex justify-between items-center">
                <h1 class="font-argentum text-2xl text-white uppercase tracking-widest">{{ branch ? branch.name : '' }} Operations</h1>
            </header>

            <div class="p-8">
                <div class="border-2 border-dashed border-gray-700 rounded-2xl p-16 text-center">
                    <p class="text-gray-500 font-bold uppercase tracking-widest">Local Branch Interface Established.</p>
                    <p class="text-sm text-gray-600 mt-2">Ready to implement local terminal controls and session management.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    branch: Object
});
</script>

<style>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
@font-face { font-family: 'AZNUnified'; src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype'); font-weight: normal; font-style: italic; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }
</style>