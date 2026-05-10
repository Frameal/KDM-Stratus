<template>
    <div class="min-h-screen bg-[#101113] text-gray-200 font-sans flex selection:bg-blue-500 selection:text-white">
        
        <aside class="w-64 bg-[#18191c] border-r border-gray-800 flex flex-col shadow-2xl relative z-20 hidden md:flex">
            <div class="h-24 flex items-center px-6 border-b border-gray-800 bg-[#101113]">
                <img src="/images/logo.png" alt="KDM Logo" class="h-10 w-auto object-contain mr-3" onerror="this.style.display='none';" />
                <span class="font-azn tracking-widest text-xl text-white mt-1">KDM Admin</span>
            </div>

            <nav v-if="$page.props.auth.user.role === 'manager'" class="flex-1 px-4 space-y-2 overflow-y-auto mt-6 custom-scrollbar">
                <a :href="route('branch.dashboard')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/manager/dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Overview</a>
                <a :href="route('branch.terminals')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/manager/terminals') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Terminal Grid</a>
                <a :href="route('branch.reservations')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/manager/reservations') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Reservations</a>
                <a :href="route('branch.feedback')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/manager/feedback') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Local Feedback</a>
            </nav>

            <nav v-if="$page.props.auth.user.role === 'hq'" class="flex-1 px-4 space-y-2 overflow-y-auto mt-6 custom-scrollbar">
                <a :href="route('hq.dashboard')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/hq/dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Global Matrix</a>
                <a :href="route('hq.branches')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/hq/branches') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Branch Ops</a>
                <a :href="route('hq.users')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/hq/users') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Enterprise Users</a>
                <a :href="route('hq.reports')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-xs font-bold tracking-widest uppercase transition" :class="$page.url.startsWith('/hq/reports') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-[#1c1d21]'">Global Reports</a>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <div class="bg-[#1c1d21] rounded-lg p-4 shadow-inner">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $page.props.auth.user.role === 'hq' ? 'Executive Admin' : 'Branch Manager' }}</p>
                    <p class="text-sm font-bold text-white truncate mt-1">{{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}</p>
                    <a href="/force-logout" class="mt-4 block text-center text-xs font-bold bg-red-900/20 text-red-400 hover:text-white hover:bg-red-600 border border-red-500/30 py-2.5 rounded transition uppercase tracking-widest">Secure Logout</a>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#101113] relative">
            <header class="h-24 bg-[#101113]/90 backdrop-blur-md border-b border-gray-800 flex items-center px-8 z-10 flex-shrink-0">
                <h1 class="font-argentum text-2xl text-white uppercase tracking-widest">
                    <slot name="header"></slot>
                </h1>
            </header>
            <div class="flex-1 overflow-y-auto p-8 relative z-10 custom-scrollbar">
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<style>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
@font-face { font-family: 'AZNUnified'; src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype'); font-weight: normal; font-style: italic; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>