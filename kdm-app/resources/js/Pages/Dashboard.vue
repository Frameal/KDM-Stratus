<template>
    <Head title="KDM Stratus Dashboard" />

    <div class="min-h-screen bg-[#101113] text-gray-200 font-sans selection:bg-blue-500 selection:text-white flex flex-col scroll-smooth relative">
        
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-green-900/10 rounded-full blur-[80px]"></div>
        </div>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4"
                 :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'">
                <svg v-if="toast.type === 'success'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <svg v-else class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <nav class="w-full z-50 bg-[#101113]/90 backdrop-blur-md border-b border-gray-800 h-24 flex-shrink-0 shadow-lg sticky top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    
                    <div class="flex items-center gap-8">
                        <a href="/" class="flex-shrink-0 flex items-center gap-4 hover:scale-105 transition transform cursor-pointer">
                            <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]" onerror="this.style.display='none';" />
                            <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                        </a>
                        
                        <div class="hidden md:flex space-x-6 border-l border-gray-700 pl-8">
                            <button @click="scrollTo('reservation')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Reserve PC</button>
                            <button @click="scrollTo('complaint')" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition">Report Issue</button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="bg-[#1c1d21] border border-gray-700 text-gray-300 px-4 py-1.5 rounded-lg font-bold text-sm tracking-widest shadow-inner">
                            @{{ $page.props.auth.user.username }}
                        </div>
                        
                        <div class="bg-blue-900/20 border border-blue-500/30 text-blue-400 px-4 py-1.5 rounded-lg font-mono font-bold text-sm tracking-widest shadow-inner flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ calculatedTimeRemaining }}
                        </div>

                        <div class="text-green-400 font-bold font-azn text-lg px-2 drop-shadow-[0_0_10px_rgba(74,222,128,0.2)]"
                             :class="{'text-red-400': Number($page.props.auth.user.balance) < 5.00}">
                            ₱{{ Number($page.props.auth.user.balance).toFixed(2) }}
                        </div>
                        
                        <button @click="showTopUpModal = true" class="bg-green-600 hover:bg-green-500 text-white px-4 py-1.5 rounded font-bold text-sm transition shadow-[0_0_15px_rgba(22,163,74,0.3)]">Top Up</button>
                        
                        <a :href="route('profile.edit')" class="bg-[#1c1d21] border border-gray-700 hover:border-blue-500 text-white px-4 py-1.5 rounded font-bold text-sm transition shadow-lg">Account Details</a>
                        <Link :href="route('logout')" method="post" as="button" class="border border-red-500/50 hover:border-red-500 hover:bg-red-500/10 text-red-400 hover:text-red-300 px-4 py-1.5 rounded font-bold text-sm transition">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12 relative z-10">
            
            <div v-if="activeSession" class="bg-orange-900/20 border border-orange-500/50 rounded-2xl p-6 shadow-[0_0_30px_rgba(249,115,22,0.15)] flex justify-between items-center animate-fade-in">
                <div>
                    <h3 class="font-argentum text-xl text-white uppercase tracking-widest mb-1">Active Reservation: <span class="text-orange-400">{{ activeSession.pc?.pc_number.replace('-', ' ') }}</span></h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Please log into the physical terminal before the timer expires.</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="font-mono text-3xl text-orange-400 font-bold">{{ formattedTimer }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Grace Period</div>
                    </div>
                    <button @click="showCancelModal = true" class="bg-red-600/20 hover:bg-red-600 border border-red-500/50 text-red-400 hover:text-white px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition">
                        Forfeit Session
                    </button>
                </div>
            </div>

            <section id="reservation" class="scroll-mt-32">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                    
                    <div class="p-6 border-b border-gray-800 bg-[#101113] flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Network Telemetry</h2>
                            <p class="text-sm text-gray-400 mt-1 font-bold">Select a branch to establish telemetry and reserve hardware.</p>
                        </div>
                        
                        <div class="relative w-full md:w-72" ref="dropdownContainer">
                            <div class="relative">
                                <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input v-model="searchQuery" @focus="openSearch" type="text" placeholder="Search branch or region..." 
                                    class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition font-bold tracking-wide">
                            </div>
                            
                            <div v-if="showDropdown" class="absolute z-50 w-full mt-2 bg-[#1c1d21] border border-gray-700 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">
                                <div v-if="filteredBranches.length === 0" class="p-4 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">No branches found</div>
                                <button v-for="branch in filteredBranches" :key="branch.id" @mousedown.prevent="selectBranch(branch)"
                                    class="w-full text-left px-4 py-3 hover:bg-blue-600 hover:text-white transition border-b border-gray-800 last:border-0 cursor-pointer">
                                    <div class="font-bold uppercase tracking-widest">{{ branch.name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ branch.address }}</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedBranch" class="p-6 space-y-8">
                        
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Branch Schematics</h3>
                            <div class="flex overflow-x-auto gap-6 pb-4 snap-x custom-scrollbar">
                                <div v-for="image in getBranchImages(selectedBranch.name)" :key="image" 
                                     class="flex-shrink-0 w-[600px] h-[350px] bg-[#101113] rounded-xl border border-gray-700 overflow-hidden group cursor-pointer snap-center relative shadow-lg hover:border-blue-500 transition-colors"
                                     @click="openModal(image)">
                                    <img :src="image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500" onerror="this.style.display='none'" />
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                                        <span class="bg-blue-600 border border-blue-400 text-white font-bold px-6 py-3 rounded-lg uppercase tracking-widest shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg> Inspect Schematic
                                        </span>
                                    </div>
                                </div>
                                <div v-if="getBranchImages(selectedBranch.name).length === 0" class="w-full py-12 text-center text-gray-600 border border-gray-800 border-dashed rounded-xl font-bold uppercase tracking-widest">
                                    No schematics uploaded to the server for this branch yet.
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center justify-between">
                                Live Terminal Status ({{ filteredPcs.length }} Terminals)
                                <div class="flex gap-4 text-[10px] bg-[#1c1d21] px-4 py-2 rounded-lg border border-gray-800">
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></span> Available</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-500 shadow-[0_0_5px_#f97316]"></span> Reserved</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-[0_0_5px_#3b82f6]"></span> Occupied</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_5px_#ef4444]"></span> Maintenance</span>
                                </div>
                            </h3>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                <div v-for="pc in filteredPcs" :key="pc.id" 
                                    class="bg-[#101113] border rounded-lg p-4 flex flex-col items-center justify-between h-36 transition-all duration-300 hover:-translate-y-1"
                                    :class="{
                                        'border-green-500/50 shadow-[0_0_15px_rgba(34,197,94,0.15)] bg-[#101113]': pc.status === 'free',
                                        'border-blue-500/50 bg-[#1c1d21]': pc.status === 'occupied',
                                        'border-orange-500/50 bg-[#1c1d21]': pc.status === 'reserved',
                                        'border-red-900/50 opacity-40 bg-black': pc.status === 'broken'
                                    }">
                                    
                                    <div class="text-center">
                                        <div class="font-bold text-lg tracking-widest mb-1 text-white">{{ pc.pc_number.replace('-', ' ') }}</div>
                                        <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-xs uppercase tracking-wider animate-pulse">In Use</div>
                                        <div v-else-if="pc.status === 'reserved'" class="text-orange-400 font-bold text-xs uppercase tracking-wider animate-pulse">Reserved</div>
                                        <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-xs uppercase tracking-wider">Offline</div>
                                        <div v-else class="text-green-400 font-bold text-xs uppercase tracking-wider">Ready</div>
                                    </div>

                                    <button v-if="pc.status === 'free' && !activeSession" @click="openReserveModal(pc)" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold py-2 rounded transition mt-2 tracking-wider uppercase shadow-[0_0_10px_rgba(37,99,235,0.3)] hover:shadow-[0_0_15px_rgba(37,99,235,0.6)]">
                                        Reserve Terminal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-20 text-center text-gray-500 relative overflow-hidden">
                        <svg class="w-20 h-20 mx-auto mb-6 opacity-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="font-argentum text-2xl uppercase tracking-widest text-gray-400">Awaiting Branch Selection</p>
                        <p class="text-sm mt-3 font-bold tracking-wide">Select a branch from the search bar above to establish telemetry or reserve a PC.</p>
                    </div>
                </div>
            </section>

            <section id="complaint" class="scroll-mt-32 max-w-3xl mx-auto w-full">
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 p-8 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full blur-[40px] pointer-events-none"></div>

                    <div class="mb-8 relative z-10 text-center">
                        <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">Submit a Report</h2>
                    </div>

                    <form @submit.prevent="submitComplaint" class="space-y-6 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Location</label>
                                <select v-model="reportForm.branch_name" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition cursor-pointer">
                                    <option value="" disabled>Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.name">{{ branch.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Concern Type</label>
                                <select v-model="reportForm.concern_type" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition cursor-pointer">
                                    <option value="" disabled>Select Category</option>
                                    <option>Hardware Failure</option>
                                    <option>Software Issue</option>
                                    <option>Network Latency</option>
                                    <option>Facility Cleanliness</option>
                                    <option>Customer Service</option>
                                    <option>Others</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Details</label>
                            <textarea v-model="reportForm.details" required rows="5" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition resize-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-800 pt-8">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" v-model="reportForm.is_anonymous" class="mr-3">
                                <span class="text-sm text-gray-400 font-bold uppercase tracking-widest group-hover:text-white transition">Submit Anonymously</span>
                            </label>

                            <button type="submit" :disabled="reportForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest px-8 py-3 rounded-lg text-sm transition shadow-[0_0_15px_rgba(37,99,235,0.4)] disabled:opacity-50">
                                Transmit Report
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>

        <div v-if="showTopUpModal" class="fixed inset-0 z-[150] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showTopUpModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-lg overflow-hidden shadow-2xl">
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Top Up Wallet</h3>
                    <button @click="showTopUpModal = false" class="text-gray-500 hover:text-white transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <button v-for="rate in rates" :key="rate.amount" @click="selectedAmount = rate.amount"
                            class="border rounded-lg p-3 text-center transition-all duration-200"
                            :class="selectedAmount === rate.amount ? 'bg-blue-600 border-blue-500 text-white' : 'bg-[#101113] border-gray-700 text-gray-400 hover:border-blue-500'">
                            <div class="font-mono text-lg font-bold mb-1">₱{{ rate.amount }}</div>
                            <div class="text-[10px] uppercase tracking-widest font-bold" :class="{'text-orange-400': rate.promo}">{{ rate.time }}</div>
                        </button>
                    </div>

                    <div class="mb-6 relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Or Custom Amount (₱)</label>
                        <input v-model.number="selectedAmount" type="number" min="1" step="1" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white font-mono text-lg outline-none focus:border-blue-500" placeholder="Enter whole number" />
                    </div>

                    <button @click="processTopUp" :disabled="isProcessing || selectedAmount < 1" class="w-full bg-green-600 hover:bg-green-500 text-white text-sm font-bold py-4 rounded-lg uppercase tracking-widest transition flex justify-center items-center gap-2">
                        {{ isProcessing ? 'Generating Secure Link...' : `Pay ₱${selectedAmount || 0}` }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showReserveModal" class="fixed inset-0 z-[100] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showReserveModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative">
                
                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800 flex justify-between items-center relative z-10">
                    <h3 class="font-argentum text-xl text-white uppercase tracking-widest">Reserve Terminal</h3>
                    <button @click="showReserveModal = false" class="text-gray-500 hover:text-white transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-8 space-y-6 relative z-10">
                    <div class="text-center">
                        <h4 class="text-3xl font-bold text-white tracking-widest">{{ activePcToReserve?.pc_number.replace('-', ' ') }}</h4>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Select Grace Period Duration</p>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <button @click="reserveForm.duration = 15" :class="reserveForm.duration === 15 ? 'bg-orange-600 border-orange-500 text-white' : 'bg-[#1c1d21] border-gray-700 text-gray-400 hover:border-orange-500'" class="border rounded-lg p-3 text-center transition">
                            <div class="font-bold mb-1">15m</div>
                            <div class="text-[10px] font-mono tracking-widest">₱5.00</div>
                        </button>
                        <button @click="reserveForm.duration = 30" :class="reserveForm.duration === 30 ? 'bg-orange-600 border-orange-500 text-white' : 'bg-[#1c1d21] border-gray-700 text-gray-400 hover:border-orange-500'" class="border rounded-lg p-3 text-center transition">
                            <div class="font-bold mb-1">30m</div>
                            <div class="text-[10px] font-mono tracking-widest">₱10.00</div>
                        </button>
                        <button @click="reserveForm.duration = 60" :class="reserveForm.duration === 60 ? 'bg-orange-600 border-orange-500 text-white' : 'bg-[#1c1d21] border-gray-700 text-gray-400 hover:border-orange-500'" class="border rounded-lg p-3 text-center transition">
                            <div class="font-bold mb-1">1h</div>
                            <div class="text-[10px] font-mono tracking-widest">₱25.00</div>
                        </button>
                    </div>

                    <div v-if="Number($page.props.auth.user.balance) < (reserveForm.duration === 15 ? 5 : (reserveForm.duration === 30 ? 10 : 25))" class="bg-red-900/20 border border-red-500/50 p-4 rounded-lg text-center">
                        <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest">Insufficient Funds for this duration.</p>
                    </div>
                    
                    <button v-else @click="confirmReservation" :disabled="reserveForm.processing" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-argentum uppercase tracking-widest py-3.5 rounded-lg text-sm transition shadow-[0_0_15px_rgba(37,99,235,0.4)] disabled:opacity-50">
                        Authorize Deduction
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showCancelModal" class="fixed inset-0 z-[200] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showCancelModal = false">
            <div class="bg-[#18191c] border border-red-900/50 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-center items-center">
                    <h3 class="font-argentum text-xl text-red-500 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Warning
                    </h3>
                </div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">Are you absolutely sure you want to forfeit this session?</p>
                    <p class="text-xs text-red-400 font-bold uppercase tracking-widest bg-red-900/10 p-3 rounded border border-red-900/30">Your reservation fee is non-refundable.</p>
                    <div class="flex gap-4 pt-2">
                        <button @click="showCancelModal = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition">Go Back</button>
                        <button @click="executeCancelReservation" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(220,38,38,0.4)]">Yes, Forfeit</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] bg-black/95 flex items-center justify-center backdrop-blur-md overflow-hidden" 
             @mousemove="onDrag" @mouseup="stopDrag" @mouseleave="stopDrag" @wheel.prevent="handleWheel">
            
            <button @click="closeModal" class="absolute top-6 right-6 z-50 text-gray-400 hover:text-white bg-[#18191c] border border-gray-700 hover:border-gray-500 rounded-full p-3 transition shadow-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" 
                 @mousedown.self="closeModal" @mousedown="startDrag">
                <img :src="activeImage" draggable="false"
                     class="max-w-[90vw] max-h-[90vh] object-contain transition-transform duration-100 ease-out origin-center" 
                     :style="{ transform: `translate(${pan.x}px, ${pan.y}px) scale(${zoomLevel})` }" />
            </div>

            <div class="absolute bottom-10 right-10 flex bg-[#18191c] border border-gray-700 rounded-lg shadow-[0_0_30px_rgba(0,0,0,0.8)] overflow-hidden z-50 select-none">
                <button @click="zoomOut" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 focus:outline-none" title="Zoom Out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                </button>
                <button @click="resetZoom" class="px-6 py-4 text-xs font-bold text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 tracking-widest uppercase focus:outline-none" title="Reset Zoom">
                    Reset
                </button>
                <button @click="zoomIn" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition focus:outline-none" title="Zoom In">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </button>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const props = defineProps({
    branches: Array,
    pcs: Array,
    floorplans: Array,
    activeSession: Object 
});

// TOAST NOTIFICATIONS
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 4000);
};

// REPORT SYSTEM
const reportForm = useForm({
    branch_name: '', concern_type: '', details: '', is_anonymous: false
});

const submitComplaint = () => {
    reportForm.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast("Report successfully transmitted to HQ.");
            reportForm.reset();
        },
        onError: (errors) => {
            // Catches the Anti-Spam error from Laravel and shows a red toast!
            showToast(errors.report || "Failed to submit report.", "error");
        }
    });
};

// TOP UP LOGIC
const showTopUpModal = ref(false);
const selectedAmount = ref(50);
const isProcessing = ref(false);
const rates = [
    { amount: 20, time: '1 Hour', promo: false }, { amount: 40, time: '2 Hours', promo: false },
    { amount: 50, time: '3 Hours', promo: true }, { amount: 70, time: '4 Hours', promo: false },
    { amount: 90, time: '5 Hours', promo: false }, { amount: 100, time: '6 Hours', promo: true },
];

const calculatedTimeRemaining = computed(() => {
    let balance = parseFloat(page.props.auth.user.balance || 0);
    if (balance <= 0) return '00:00:00';
    let totalMinutes = 0;
    const promoChunks = Math.floor(balance / 50); totalMinutes += promoChunks * 180; balance = balance % 50;
    const hourChunks = Math.floor(balance / 20); totalMinutes += hourChunks * 60; balance = balance % 20;
    const halfHourChunks = Math.floor(balance / 10); totalMinutes += halfHourChunks * 30;
    const hours = Math.floor(totalMinutes / 60).toString().padStart(2, '0');
    const mins = (totalMinutes % 60).toString().padStart(2, '0');
    return `${hours}:${mins}:00`;
});

const processTopUp = async () => {
    if (selectedAmount.value < 1 || !Number.isInteger(selectedAmount.value)) {
        showToast("Invalid amount. Minimum is ₱1.", "error"); return;
    }
    isProcessing.value = true;
    try {
        const response = await axios.post('/topup/generate', { amount: selectedAmount.value });
        if (response.data.checkout_url) window.location.href = response.data.checkout_url;
    } catch (error) {
        showToast("Failed to generate payment link.", "error");
        isProcessing.value = false;
    }
};

// SEARCH & BRANCH SELECTION
const searchQuery = ref('');
const showDropdown = ref(false);
const selectedBranch = ref(null);
const dropdownContainer = ref(null);

const regionMap = {
    'cavite': ['imus', 'bacoor'], 'bulacan': ['baliwag', 'malolos', 'sjdm'],
    'manila': ['anonas', 'españa', 'taft', 'gastambide', 'morayta'],
    'qc': ['novaliches', 'lagro', 'tandang sora'], 'makati': ['comembo', 'evangelista', 'guadalupe']
};

const openSearch = () => { searchQuery.value = ''; showDropdown.value = true; };
const closeDropdown = (e) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(e.target)) {
        showDropdown.value = false;
        if (selectedBranch.value) searchQuery.value = selectedBranch.value.name;
    }
};
onMounted(() => document.addEventListener('mousedown', closeDropdown));
onUnmounted(() => document.removeEventListener('mousedown', closeDropdown));

const filteredBranches = computed(() => {
    if (!props.branches) return [];
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.branches;
    const targets = regionMap[query] ? regionMap[query] : [query];
    return props.branches.filter(b => targets.some(k => b.name.toLowerCase().includes(k) || b.address.toLowerCase().includes(k)));
});

const filteredPcs = computed(() => {
    if (!selectedBranch.value || !props.pcs) return [];
    return props.pcs.filter(pc => pc.branch_id === selectedBranch.value.id);
});

const selectBranch = (branch) => { selectedBranch.value = branch; searchQuery.value = branch.name; showDropdown.value = false; };

const getBranchImages = (branchName) => {
    if (!props.floorplans) return [];
    const formattedName = branchName.toLowerCase().replace(/ /g, '_');
    return props.floorplans.filter(path => path.toLowerCase().includes(formattedName));
};

// ZOOM, AND PAN STATE
const showModal = ref(false);
const activeImage = ref('');
const zoomLevel = ref(1);
const pan = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const openModal = (imgSrc) => {
    activeImage.value = imgSrc;
    resetZoom();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    activeImage.value = '';
    resetZoom();
};

const zoomIn = () => { if (zoomLevel.value < 4) zoomLevel.value += 0.25; };
const zoomOut = () => { 
    if (zoomLevel.value > 1) zoomLevel.value -= 0.25; 
    if (zoomLevel.value === 1) pan.value = { x: 0, y: 0 };
};
const resetZoom = () => { zoomLevel.value = 1; pan.value = { x: 0, y: 0 }; };

const handleWheel = (e) => {
    e.deltaY < 0 ? zoomIn() : zoomOut();
};

const startDrag = (e) => {
    if (zoomLevel.value <= 1) return;
    isDragging.value = true;
    dragStart.value = { x: e.clientX - pan.value.x, y: e.clientY - pan.value.y };
};

const onDrag = (e) => {
    if (!isDragging.value || zoomLevel.value <= 1) return;
    pan.value = { x: e.clientX - dragStart.value.x, y: e.clientY - dragStart.value.y };
};

const stopDrag = () => { isDragging.value = false; };

// RESERVATION DATABASE LOGIC
const showReserveModal = ref(false);
const showCancelModal = ref(false);
const activePcToReserve = ref(null);
const reservationTimeLeft = ref(0);
let timerInterval = null;

const reserveForm = useForm({
    pc_id: null,
    duration: 15
});

const formattedTimer = computed(() => {
    if (reservationTimeLeft.value <= 0) return '00:00';
    const minutes = Math.floor(reservationTimeLeft.value / 60).toString().padStart(2, '0');
    const seconds = (reservationTimeLeft.value % 60).toString().padStart(2, '0');
    return `${minutes}:${seconds}`;
});

const calculateDbTimer = () => {
    if (props.activeSession) {
        const safeDateString = props.activeSession.expires_at.replace(' ', 'T');
        const expiryTime = new Date(safeDateString).getTime();
        const now = new Date().getTime();
        const diffInSeconds = Math.floor((expiryTime - now) / 1000);
        
        if (diffInSeconds > 0) {
            reservationTimeLeft.value = diffInSeconds;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                reservationTimeLeft.value--;
                if (reservationTimeLeft.value <= 0) {
                    clearInterval(timerInterval);
                    
                    // FIXED: Silently tell the backend to free the PC and mark as expired
                    axios.post(route('reserve.expire'), { reservation_id: props.activeSession.id }).then(() => {
                        router.reload({ only: ['activeSession', 'pcs'] });
                        showToast("Your reservation has expired.", "error");
                    });
                }
            }, 1000);
        }
    }
};

watch(() => props.activeSession, calculateDbTimer, { immediate: true });

const openReserveModal = (pc) => {
    activePcToReserve.value = pc;
    reserveForm.pc_id = pc.id;
    reserveForm.duration = 15;
    showReserveModal.value = true;
};

const confirmReservation = () => {
    reserveForm.post(route('reserve.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showReserveModal.value = false;
            showToast(`Terminal successfully secured for ${reserveForm.duration} mins.`);
        },
        onError: (errors) => {
            showToast(errors.reservation || "Failed to secure terminal.", "error");
        }
    });
};

const cancelReservation = () => {
    showCancelModal.value = true; // Trigger Custom UI Instead of Browser Alert
};

const executeCancelReservation = () => {
    showCancelModal.value = false;
    router.post(route('reserve.cancel'), { reservation_id: props.activeSession.id }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast("Reservation cancelled.");
            clearInterval(timerInterval);
        }
    });
};

const scrollTo = (id) => { const el = document.getElementById(id); if (el) el.scrollIntoView({ behavior: 'smooth' }); };
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

.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}
a
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>