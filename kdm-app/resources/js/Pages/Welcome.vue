<template>
    <div class="min-h-screen bg-[#1c1d21] text-gray-200 font-sans selection:bg-white selection:text-black">
        
        <nav class="fixed top-0 w-full z-50 bg-[#1c1d21] border-b border-gray-800 h-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <div class="flex-shrink-0 flex items-center gap-4">
                        <img src="/images/logo.png" alt="KDM Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none';" />
                        <span class="font-azn tracking-widest text-4xl text-white">KDM</span>
                    </div>
                    
                    <div class="hidden md:flex space-x-8">
                        <a href="#about" class="text-gray-400 hover:text-white font-semibold transition">About KDM</a>
                        <a href="#team" class="text-gray-400 hover:text-white font-semibold transition">The Team</a>
                        <a href="#branches" class="text-gray-400 hover:text-white font-semibold transition">Branches</a>
                        <a href="#faq" class="text-gray-400 hover:text-white font-semibold transition">FAQ</a>
                        <a href="#contact" class="text-gray-400 hover:text-white font-semibold transition">Contact Us</a>
                    </div>

                    <div class="flex items-center space-x-6">
                        <a href="/register" class="text-gray-400 hover:text-white font-semibold transition">Sign Up</a>
                        <a href="/login" class="bg-white hover:bg-gray-200 text-black px-6 py-3 rounded font-bold shadow-lg shadow-white/10 transition transform hover:-translate-y-1">
                            Log-In / Reserve
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="h-24"></div>

        <section class="relative h-[70vh] w-full bg-black overflow-hidden animate-on-load">
            <div v-for="(banner, index) in banners" :key="index"
                 class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out"
                 :class="{ 'opacity-100 z-10': activeBanner === index, 'opacity-0 z-0': activeBanner !== index }"
                 :style="{ backgroundImage: `url(${banner})` }">
            </div>
        </section>

        <section class="py-10 bg-[#18191c] border-b border-gray-800 flex justify-center items-center animate-on-load">
            <a href="/login" class="bg-white hover:bg-gray-200 text-black px-12 py-5 ...">                  
                Select Branch & Reserve Now
             </a>
        </section>

        <section id="about" class="py-32 bg-[#18191c] border-b border-gray-800 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-trigger">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center mb-24">
                    <div class="flex justify-center items-center">
                        <img src="/images/logo.png" alt="Huge KDM Logo" class="w-full max-w-lg object-contain drop-shadow-2xl opacity-90">
                    </div>
                    
                    <div>
                        <h2 class="text-sm font-bold text-gray-500 tracking-widest uppercase mb-4">Our Origins</h2>
                        <h3 class="font-argentum text-5xl md:text-6xl text-white mb-8 uppercase leading-tight">About KDM</h3>
                        
                        <div class="space-y-6 text-gray-400 text-lg leading-relaxed">
                            <p>Welcome to Kadiliman Esports Cafe, where passion for gaming meets next-level community experience. Established with the vision of creating a premium space for both casual and competitive gamers, we offer a state-of-the-art environment designed to inspire, connect, and elevate every player who walks through our doors.</p>
                            <p>At Kadiliman, we believe gaming is more than just a pastime, it’s a lifestyle. Whether you’re grinding ranked games, practicing for a tournament, or just hanging out with friends, our high-spec PCs, fast internet, and gamer-friendly amenities ensure you stay immersed and on top of your game.</p>
                            <p>More than a cafe, we are a hub for esports, events, collaborations, and grassroots talent development. From hosting local tournaments to community watch parties and gaming workshops, Kadiliman Esports Cafe is the beating heart of your gaming journey.</p>
                        </div>

                        <div id="stats-container" ref="statsRef" class="grid grid-cols-3 gap-8 border-t border-gray-700 pt-10 mt-10">
                            <div>
                                <div class="font-argentum text-4xl md:text-5xl text-white">{{ stats.years }}+</div>
                                <div class="text-xs text-gray-500 mt-2 uppercase tracking-widest font-bold">Years Active</div>
                            </div>
                            <div>
                                <div class="font-argentum text-4xl md:text-5xl text-white">{{ stats.branches }}</div>
                                <div class="text-xs text-gray-500 mt-2 uppercase tracking-widest font-bold">Branches</div>
                            </div>
                            <div>
                                <div class="font-argentum text-4xl md:text-5xl text-white">{{ stats.customers }}k+</div>
                                <div class="text-xs text-gray-500 mt-2 uppercase tracking-widest font-bold">Customers</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="bg-[#222328] p-12 rounded-xl border border-gray-800 hover:border-white/30 transition duration-300">
                        <h4 class="font-argentum text-3xl text-white mb-6 uppercase">Mission</h4>
                        <p class="text-gray-400 text-xl leading-relaxed">We aim to build spaces that empower gamers to compete, connect, and celebrate the world of esports.</p>
                    </div>
                    <div class="bg-[#222328] p-12 rounded-xl border border-gray-800 hover:border-white/30 transition duration-300">
                        <h4 class="font-argentum text-3xl text-white mb-6 uppercase">Vision</h4>
                        <p class="text-gray-400 text-xl leading-relaxed">To become a leading name in the Philippine gaming community, fostering talent and creating opportunity for every gamer.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="team" class="py-32 bg-[#1c1d21]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center scroll-trigger">
                <h2 class="text-sm font-bold text-gray-500 tracking-widest uppercase mb-4">Leadership</h2>
                <h3 class="font-argentum text-5xl md:text-6xl text-white mb-20 uppercase">Team KDM</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                    
                    <div class="bg-[#222328] p-10 rounded-xl border border-gray-800 text-center transform hover:-translate-y-3 transition duration-500 shadow-2xl group">
                        <div class="w-48 h-48 mx-auto mb-8 relative">
                            <div class="absolute inset-0 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition"></div>
                            <img src="/images/narpim.png" alt="Clark Karoll Landrito" class="relative z-10 w-full h-full object-cover rounded-full border-4 border-gray-800 group-hover:border-white transition duration-500">
                        </div>
                        <h4 class="text-2xl font-bold text-white mb-2">Clark Karoll Landrito</h4>
                        <p class="text-gray-400 font-bold tracking-widest text-xs mb-6">CHIEF EXECUTIVE OFFICER</p>
                        <div class="flex justify-center space-x-6 items-center">
                            <a href="https://www.facebook.com/NarpimQck/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.instagram.com/kdmnarpim/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            <a href="https://www.tiktok.com/@narpimtv" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-[#222328] p-10 rounded-xl border border-gray-800 text-center transform hover:-translate-y-3 transition duration-500 shadow-2xl group">
                        <div class="w-48 h-48 mx-auto mb-8 relative">
                            <div class="absolute inset-0 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition"></div>
                            <img src="/images/abraham.png" alt="Abraham Soliven Otico" class="relative z-10 w-full h-full object-cover rounded-full border-4 border-gray-800 group-hover:border-white transition duration-500">
                        </div>
                        <h4 class="text-2xl font-bold text-white mb-2">Abraham Soliven Otico</h4>
                        <p class="text-gray-400 font-bold tracking-widest text-xs mb-6">CHIEF OPERATING OFFICER</p>
                        <div class="flex justify-center space-x-6 items-center">
                            <a href="https://www.facebook.com/abraham.otico/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.instagram.com/ar_otico/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            <a href="https://www.tiktok.com/@abeotico06" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-[#222328] p-10 rounded-xl border border-gray-800 text-center transform hover:-translate-y-3 transition duration-500 shadow-2xl group">
                        <div class="w-48 h-48 mx-auto mb-8 relative">
                            <div class="absolute inset-0 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition"></div>
                            <img src="/images/ria.png" alt="Ria Reyes Otico" class="relative z-10 w-full h-full object-cover rounded-full border-4 border-gray-800 group-hover:border-white transition duration-500">
                        </div>
                        <h4 class="text-2xl font-bold text-white mb-2">Ria Reyes Otico</h4>
                        <p class="text-gray-400 font-bold tracking-widest text-xs mb-6">CHIEF FINANCIAL OFFICER</p>
                        <div class="flex justify-center space-x-6 items-center">
                            <a href="https://www.facebook.com/rialoureyes/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.instagram.com/myboo_06/" target="_blank" class="text-gray-500 hover:text-white transition">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="branches" class="py-32 bg-[#18191c] border-t border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-trigger">
                <div class="text-center mb-16">
                    <h2 class="text-sm font-bold text-gray-500 tracking-widest uppercase mb-4">Network</h2>
                    <h3 class="font-argentum text-5xl md:text-6xl text-white mb-4 uppercase">Our Locations</h3>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6 text-center mb-10">
                    <button v-for="branch in branches" :key="branch.id" 
                            @click="activeBranch = branch"
                            class="bg-[#222328] py-5 px-2 rounded border border-gray-800 font-bold text-sm transition duration-300 shadow"
                            :class="activeBranch?.id === branch.id ? 'border-blue-500 bg-blue-600 text-white' : 'text-gray-400 hover:border-white hover:text-white'">
                        {{ branch.name }}
                    </button>
                </div>

                <div v-if="activeBranch" class="bg-[#222328] border border-gray-700 rounded-xl p-8 max-w-4xl mx-auto transform transition-all duration-500 shadow-2xl">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="font-argentum text-3xl text-white uppercase">{{ activeBranch.name }}</h4>
                            <p class="text-blue-500 font-bold tracking-widest text-sm mt-1">KDM STRATUS NODE</p>
                        </div>
                        <div class="bg-gray-900 px-4 py-2 rounded border border-gray-800 text-center">
                            <span class="block text-2xl font-bold text-white">{{ activeBranch.total_pcs }}</span>
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Total PCs</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-gray-300">
                            <svg class="w-6 h-6 mr-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ activeBranch.address }}
                        </div>
                        <div class="flex items-center text-gray-300">
                            <svg class="w-6 h-6 mr-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ activeBranch.operating_hours }}
                        </div>
                    </div>
                </div>

                <section id="faq" class="py-32 bg-[#1c1d21]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 scroll-trigger">
                <div class="text-center mb-16">
                    <h2 class="text-sm font-bold text-gray-500 tracking-widest uppercase mb-4">Learn More</h2>
                    <h3 class="font-argentum text-5xl md:text-6xl text-white mb-4 uppercase">FAQ</h3>
                </div>
                <div class="space-y-6">
                    <div class="bg-[#222328] rounded-xl border border-gray-800 p-8">
                        <h4 class="text-xl font-bold text-white mb-3">How does the PC Reservation work?</h4>
                        <p class="text-gray-400 text-lg leading-relaxed">Log into the dashboard, select your branch, and reserve a free PC. A ₱5.00 fee is deducted from your balance. You have a 15-minute grace period to log into the physical terminal before the reservation is forfeited.</p>
                    </div>
                    <div class="bg-[#222328] rounded-xl border border-gray-800 p-8">
                        <h4 class="text-xl font-bold text-white mb-3">How do I top up my balance?</h4>
                        <p class="text-gray-400 text-lg leading-relaxed">You can top up instantly using the integrated Scan-to-Pay feature on your dashboard. We support major e-wallets like GCash and Maya via PayMongo.</p>
                    </div>
                    <div class="bg-[#222328] rounded-xl border border-gray-800 p-8">
                        <h4 class="text-xl font-bold text-white mb-3">Are all branches running on diskless systems?</h4>
                        <p class="text-gray-400 text-lg leading-relaxed">Yes. All 28 KDM branches utilize CCBoot PXE technology for a pristine, high-performance Windows 11 environment upon every boot.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="py-24 bg-[#18191c] border-t border-gray-800 scroll-trigger">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                 <h2 class="text-sm font-bold text-gray-500 tracking-widest uppercase mb-4">Reach Out</h2>
                 <h3 class="font-argentum text-5xl text-white mb-8 uppercase">Contact Us</h3>
                 <p class="text-gray-400 mb-8 max-w-2xl mx-auto text-lg">Having concerns? Need to report hardware or send feedback? Send us a message.</p>
                 <div class="text-gray-300 font-bold tracking-wide">
                     Unit 2A H&L Santiago Building., Marulas, Valenzuela City <span class="mx-4 text-gray-700">|</span> franchise@kdmesports.ph <span class="mx-4 text-gray-700">|</span> 0968 855 1718
                 </div>
             </div>
        </section>

            </div>
        </section>

        </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';

// Accept the live database data from Laravel
const props = defineProps({
    branches: Array
});

const activeBanner = ref(0);
const banners = [
    '/images/banner1.jpg', 
    '/images/banner2.jpg', 
    '/images/banner3.jpg'
];

const activeBranch = ref(null);

// ... keep the rest of your scroll animation logic below ...

// Scroll Animation Logic
const statsRef = ref(null);
const statsAnimated = ref(false);
const stats = reactive({ years: 0, branches: 0, customers: 0 });

const animateValue = (key, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        stats[key] = Math.floor(progress * end);
        if (progress < 1) window.requestAnimationFrame(step);
    };
    window.requestAnimationFrame(step);
};

onMounted(() => {
    // Hero Banner Interval
    setInterval(() => {
        activeBanner.value = (activeBanner.value + 1) % banners.length;
    }, 5000);

    // Initial Load Animation (Fades in the Hero section immediately)
    setTimeout(() => {
        document.querySelectorAll('.animate-on-load').forEach(el => {
            el.classList.add('opacity-100');
            el.classList.remove('opacity-0');
        });
    }, 100);

    // Intersection Observer for Scroll Animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Fade in the section
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-10');
                
                // Trigger numbers only when stats container is visible
                if (entry.target.contains(statsRef.value) && !statsAnimated.value) {
                    statsAnimated.value = true;
                    animateValue('years', 4, 1500);
                    animateValue('branches', 28, 2000);
                    animateValue('customers', 300, 2500);
                }
            }
        });
    }, { threshold: 0.15 }); // Triggers when 15% of the section is visible

    // Attach observer to sections
    document.querySelectorAll('.scroll-trigger').forEach(el => {
        el.classList.add('opacity-0', 'translate-y-10', 'transition-all', 'duration-1000');
        observer.observe(el);
    });
});
</script>

<style>
/* Font Injections */
@font-face {
    font-family: 'ArgentumNovus';
    src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype');
    font-weight: 600;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'AZNUnified';
    src: url('/fonts/AZNUnified-Oblique-Trial.otf') format('opentype');
    font-weight: normal;
    font-style: italic;
    font-display: swap;
}

.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.font-azn { font-family: 'AZNUnified', sans-serif; }
</style>