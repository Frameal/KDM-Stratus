<template>
    <div class="flex h-screen w-screen overflow-hidden bg-[#1c1d21] text-gray-200 font-sans selection:bg-blue-500 selection:text-white">
        
        <aside class="w-72 bg-[#18191c] border-r border-gray-800 flex flex-col justify-between flex-shrink-0 shadow-2xl z-20 h-full">
            <div class="flex flex-col h-full">
                
                <div class="p-6 border-b border-gray-800 bg-[#101113] flex-shrink-0">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-10 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-2xl text-white">KDM</span>
                    </div>
                    <div class="font-bold text-white tracking-wide">{{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}</div>
                    
                    <div v-if="$page.props.auth.user.role === 'hq'" class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-1">Global Executive Admin</div>
                    <div v-else class="text-[10px] font-bold text-green-500 uppercase tracking-widest mt-1">Branch Manager</div>
                </div>

                <nav class="flex-1 overflow-y-auto p-4 space-y-1 hide-scrollbar">
                    
                    <template v-if="$page.props.auth.user.role === 'hq'">
                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-4 mb-2 pl-3">Command Center</p>
                        <Link :href="route('hq.dashboard')" :class="{'bg-blue-600/10 text-blue-400 border-blue-500/20': $page.url === '/hq-dashboard'}" class="block w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition border border-transparent">
                            Macro Telemetry
                        </Link>
                        
                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-6 mb-2 pl-3">Network Topology</p>
                        <Link :href="route('hq.branches')" :class="{'bg-blue-600/10 text-blue-400 border-blue-500/20': $page.url.startsWith('/hq-branches')}" class="block w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition border border-transparent">
                            Branch Management
                        </Link>

                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-6 mb-2 pl-3">Identity Access</p>
                        <Link :href="route('hq.users')" :class="{'bg-blue-600/10 text-blue-400 border-blue-500/20': $page.url.startsWith('/hq-users')}" class="block w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition border border-transparent">
                            Account & Staff Audit
                        </Link>
                    </template>

                    <template v-if="$page.props.auth.user.role === 'manager'">
                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-4 mb-2 pl-3">Local Operations</p>
                        <Link :href="route('branch.dashboard')" :class="{'bg-green-600/10 text-green-400 border-green-500/20': $page.url === '/branch-dashboard'}" class="block w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition border border-transparent">
                            Local Telemetry
                        </Link>
                        
                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-6 mb-2 pl-3">Hardware Control</p>
                        <Link :href="route('branch.terminals')" :class="{'bg-green-600/10 text-green-400 border-green-500/20': $page.url.startsWith('/branch-terminals')}" class="block w-full text-left px-4 py-3 rounded-lg text-gray-400 hover:bg-[#222328] hover:text-white font-bold tracking-wide transition border border-transparent">
                            Terminal Grid
                        </Link>
                    </template>

                </nav>

                <div class="p-4 border-t border-gray-800 flex-shrink-0">
                    <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-900/20 hover:bg-red-500 text-red-500 hover:text-white rounded-lg font-bold tracking-widest uppercase transition text-xs border border-red-900/50 hover:border-red-500">
                        Secure Log Out
                    </Link>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col bg-[#1c1d21] h-full overflow-hidden relative">
            <header class="bg-[#222328] border-b border-gray-800 px-8 py-6 flex-shrink-0 shadow-sm flex justify-between items-center z-10">
                <h1 class="font-argentum text-2xl text-white uppercase tracking-widest">
                    <slot name="header">System Dashboard</slot>
                </h1>
            </header>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <slot />
            </div>
        </main>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #1c1d21; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>