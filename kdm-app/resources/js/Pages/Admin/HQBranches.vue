<template>
    <Head title="Branch Operations - KDM Stratus" />

    <AdminLayout>
        <template #header>Global Branch Operations</template>

        <transition name="toast-slide">
            <div v-if="toast.show" class="fixed top-28 right-8 z-[200] bg-[#1c1d21] border-l-4 px-6 py-4 rounded shadow-2xl flex items-center gap-4" :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'">
                <p class="text-white font-bold tracking-wide text-sm">{{ toast.message }}</p>
            </div>
        </transition>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-1/4 bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden sticky top-28">
                <div class="p-5 border-b border-gray-800 bg-[#222328]">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-argentum text-white uppercase tracking-widest text-sm">Network Nodes</h3>
                        <button @click="showAddBranchModal = true" class="text-blue-400 hover:text-white transition bg-blue-900/20 p-1.5 rounded border border-blue-500/30" title="Add New Branch">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input v-model="searchQuery" type="text" placeholder="Search branch or region..." class="w-full bg-[#1c1d21] border border-gray-700 rounded pl-9 pr-3 py-2 text-white text-xs outline-none focus:border-blue-500 transition" />
                    </div>
                </div>

                <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div v-if="filteredBranches.length === 0" class="p-6 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">
                        No branches found.
                    </div>
                    <button v-for="b in filteredBranches" :key="b.id" @click="selectBranch(b.id)" 
                        class="w-full text-left px-6 py-4 border-b border-gray-800 transition hover:bg-[#222328]"
                        :class="selectedBranch?.id === b.id ? 'bg-[#222328] border-l-4 border-l-blue-500' : ''">
                        <div class="font-bold uppercase tracking-widest text-xs" :class="selectedBranch?.id === b.id ? 'text-white' : 'text-gray-400'">{{ b.name }}</div>
                    </button>
                </div>
            </div>

            <div class="w-full lg:w-3/4 space-y-8" v-if="selectedBranch">
                
                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden p-6 flex justify-between items-start">
                    <div>
                        <h2 class="font-argentum text-2xl text-white uppercase tracking-widest">{{ selectedBranch.name }} Node</h2>
                        <p class="text-sm text-gray-400 mt-1 font-mono">{{ selectedBranch.address }}</p>
                        <p class="text-xs text-blue-400 mt-2 font-bold uppercase tracking-widest">Total Hardware Units: {{ selectedBranch.total_pcs }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button @click="showEditBranchModal = true" class="bg-[#1c1d21] border border-gray-700 hover:border-blue-500 text-gray-300 hover:text-white px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Edit Settings
                        </button>
                        <button @click="scrollToReports" class="bg-[#1c1d21] border border-gray-700 hover:border-orange-500 text-gray-300 hover:text-orange-400 px-4 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition shadow-lg">
                            Jump to Feedback
                        </button>
                    </div>
                </div>

                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl p-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Branch Schematics</h3>
                    <div class="flex overflow-x-auto gap-6 pb-4 snap-x custom-scrollbar">
                        <div v-for="image in getBranchImages(selectedBranch.name)" :key="image" 
                             class="flex-shrink-0 w-[400px] h-[250px] bg-[#101113] rounded-xl border border-gray-700 overflow-hidden group cursor-pointer snap-center relative shadow-lg hover:border-blue-500 transition-colors"
                             @click="openModal(image)">
                            <img :src="image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500" />
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                                <span class="bg-blue-600 border border-blue-400 text-white font-bold px-6 py-3 rounded-lg uppercase tracking-widest shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg> Inspect
                                </span>
                            </div>
                        </div>
                        <div v-if="getBranchImages(selectedBranch.name).length === 0" class="w-full py-8 text-center text-gray-600 border border-gray-800 border-dashed rounded-xl font-bold uppercase tracking-widest text-xs">
                            No schematics uploaded.
                        </div>
                    </div>
                </div>

                <div class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                        <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Hardware Grid Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <div v-for="pc in pcs" :key="pc.id" class="bg-[#101113] border rounded-lg p-4 flex flex-col items-center justify-center h-24 transition-all" :class="getGridStyle(pc)">
                                <div class="text-center">
                                    <div class="font-bold text-sm tracking-widest mb-1 text-white">{{ pc.pc_number }}</div>
                                    <div v-if="pc.status === 'occupied'" class="text-blue-400 font-bold text-[10px] uppercase">In Use</div>
                                    <div v-else-if="pc.status === 'reserved'" class="text-orange-400 font-bold text-[10px] uppercase">Reserved</div>
                                    <div v-else-if="pc.status === 'broken'" class="text-red-500 font-bold text-[10px] uppercase">Offline</div>
                                    <div v-else class="text-green-400 font-bold text-[10px] uppercase">Ready</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="reports-section" class="bg-[#18191c] rounded-2xl border border-gray-800 shadow-2xl overflow-hidden scroll-mt-32">
                    <div class="px-6 py-5 border-b border-gray-800 bg-[#222328]">
                        <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Local Branch Feedback</h3>
                    </div>
                    <div class="p-6 overflow-x-auto">
                        <div v-if="reports.length === 0" class="text-center text-gray-500 font-bold uppercase tracking-widest py-8">No reports filed.</div>
                        <table v-else class="w-full text-left text-sm min-w-[700px]">
                            <thead>
                                <tr class="text-gray-500 font-bold uppercase tracking-widest text-[10px] border-b border-gray-800">
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Customer</th>
                                    <th class="pb-3">Type</th>
                                    <th class="pb-3 w-1/3">Details</th>
                                    <th class="pb-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="report in reports" :key="report.id" class="border-b border-gray-800/50 hover:bg-[#222328] transition">
                                    <td class="py-4 text-xs text-gray-500 font-mono">{{ new Date(report.created_at).toLocaleDateString() }}</td>
                                    <td class="py-4 text-white font-bold text-xs">{{ report.is_anonymous ? 'Anonymous' : (report.user ? '@' + report.user.username : 'Unknown') }}</td>
                                    <td class="py-4 text-blue-400 text-xs font-bold uppercase">{{ report.concern_type }}</td>
                                    <td class="py-4 text-xs text-gray-400 pr-4">{{ report.details }}</td>
                                    <td class="py-4 text-right">
                                        <span class="px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest border"
                                            :class="{
                                                'bg-red-900/40 text-red-400 border-red-500/30': (report.status || 'pending') === 'pending',
                                                'bg-orange-900/40 text-orange-400 border-orange-500/30': report.status === 'investigating',
                                                'bg-green-900/40 text-green-400 border-green-500/30': report.status === 'resolved'
                                            }">{{ report.status || 'pending' }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        <div v-if="showAddBranchModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showAddBranchModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative">
                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Initialize New Node & Manager</h3>
                </div>
                <div class="p-6 overflow-y-auto max-h-[80vh] custom-scrollbar">
                    <form @submit.prevent="submitAddBranch">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Branch Name</label>
                                    <input type="text" v-model="addBranchForm.name" placeholder="e.g., KDM Stratus - Pasig" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 transition" :class="{'border-red-500': addBranchForm.errors.name}" />
                                    <p v-if="addBranchForm.errors.name" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Physical Address</label>
                                    <input type="text" v-model="addBranchForm.address" placeholder="Full street address..." required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 transition" :class="{'border-red-500': addBranchForm.errors.address}" />
                                    <p v-if="addBranchForm.errors.address" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.address }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Initial Terminal Count</label>
                                    <input type="number" v-model.number="addBranchForm.initial_pcs" min="1" max="100" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 transition" :class="{'border-red-500': addBranchForm.errors.initial_pcs}" />
                                    <p v-if="addBranchForm.errors.initial_pcs" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.initial_pcs }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Upload Schematics (Optional)</label>
                                    <input type="file" @input="addBranchForm.schema_image = $event.target.files[0]" accept="image/*" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-1.5 text-white text-xs outline-none focus:border-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition" :class="{'border-red-500': addBranchForm.errors.schema_image}" />
                                    <p v-if="addBranchForm.errors.schema_image" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.schema_image }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 md:border-l border-gray-800 md:pl-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Manager Username</label>
                                    <input type="text" v-model="addBranchForm.manager_username" placeholder="e.g., mgr_pasig" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-orange-500 transition" :class="{'border-red-500': addBranchForm.errors.manager_username}" />
                                    <p v-if="addBranchForm.errors.manager_username" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.manager_username }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Manager Email</label>
                                    <input type="email" v-model="addBranchForm.manager_email" placeholder="pasig@kdmstratus.com" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-orange-500 transition" :class="{'border-red-500': addBranchForm.errors.manager_email}" />
                                    <p v-if="addBranchForm.errors.manager_email" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.manager_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Account Password</label>
                                    <input type="password" v-model="addBranchForm.manager_password" placeholder="Minimum 8 characters" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-orange-500 transition" :class="{'border-red-500': addBranchForm.errors.manager_password}" />
                                    <p v-if="addBranchForm.errors.manager_password" class="text-red-500 text-[9px] mt-1 font-bold">{{ addBranchForm.errors.manager_password }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 border-t border-gray-800 pt-6">
                            <button type="button" @click="showAddBranchModal = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Cancel</button>
                            <button type="submit" :disabled="addBranchForm.processing" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(37,99,235,0.4)] text-xs disabled:opacity-50">
                                {{ addBranchForm.processing ? 'Deploying...' : 'Generate Branch & Account' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="showEditBranchModal" class="fixed inset-0 z-[999] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showEditBranchModal = false">
            <div class="bg-[#18191c] border border-gray-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative">
                <div class="bg-[#101113] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Branch Settings & Adjustments</h3>
                    <button @click="showDeleteConfirm = true" class="text-red-500 hover:text-red-400 bg-red-900/20 px-3 py-1 rounded text-[10px] font-bold uppercase tracking-widest border border-red-500/30 transition">Delete Entire Branch</button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[80vh] custom-scrollbar">
                    <form @submit.prevent="submitEditBranch">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-white uppercase tracking-widest mb-3 border-b border-gray-800 pb-2">Network Data</h4>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Branch Name</label>
                                    <input type="text" v-model="editBranchForm.name" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 text-sm transition" :class="{'border-red-500': editBranchForm.errors.name}" />
                                    <p v-if="editBranchForm.errors.name" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Physical Address</label>
                                    <input type="text" v-model="editBranchForm.address" required class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 text-sm transition" :class="{'border-red-500': editBranchForm.errors.address}" />
                                    <p v-if="editBranchForm.errors.address" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.address }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Add Hardware Units</label>
                                    <input type="number" v-model.number="editBranchForm.add_pcs" min="0" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-blue-500 text-sm transition" :class="{'border-red-500': editBranchForm.errors.add_pcs}" />
                                    <p v-if="editBranchForm.errors.add_pcs" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.add_pcs }}</p>
                                    <p class="text-[9px] text-gray-500 mt-1">Generates additional PCs. To remove PCs, use the Terminal Grid manually.</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Replace Schematics</label>
                                    <input type="file" @input="editBranchForm.schema_image = $event.target.files[0]" accept="image/*" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-1.5 text-white text-xs outline-none focus:border-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition" :class="{'border-red-500': editBranchForm.errors.schema_image}" />
                                    <p v-if="editBranchForm.errors.schema_image" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.schema_image }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 md:border-l border-gray-800 md:pl-6">
                                <h4 class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-3 border-b border-gray-800 pb-2">Manager Access</h4>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Username</label>
                                    <input type="text" v-model="editBranchForm.manager_username" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-orange-500 text-sm transition" :class="{'border-red-500': editBranchForm.errors.manager_username}" />
                                    <p v-if="editBranchForm.errors.manager_username" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.manager_username }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Force New Password</label>
                                    <input type="password" v-model="editBranchForm.manager_password" placeholder="Leave blank to keep current" class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-2 text-white outline-none focus:border-orange-500 text-sm transition" :class="{'border-red-500': editBranchForm.errors.manager_password}" />
                                    <p v-if="editBranchForm.errors.manager_password" class="text-red-500 text-[9px] mt-1 font-bold">{{ editBranchForm.errors.manager_password }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 border-t border-gray-800 pt-6">
                            <button type="button" @click="showEditBranchModal = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Cancel</button>
                            <button type="submit" :disabled="editBranchForm.processing" class="flex-1 bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-lg text-xs disabled:opacity-50">Save Operations Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="showDeleteConfirm" class="fixed inset-0 z-[1000] bg-black/95 flex items-center justify-center p-4 backdrop-blur-md" @click.self="showDeleteConfirm = false">
            <div class="bg-[#18191c] border border-red-900/50 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative">
                <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-center items-center"><h3 class="font-argentum text-xl text-red-500 uppercase tracking-widest">CRITICAL WARNING</h3></div>
                <div class="p-6 text-center space-y-6">
                    <p class="text-gray-300 font-bold tracking-wide">Purge the entire branch from the network?</p>
                    <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest bg-red-900/10 p-3 rounded border border-red-900/30">Destroys all localized hardware and the manager account instantly.</p>
                    <div class="flex gap-4 pt-2">
                        <button @click="showDeleteConfirm = false" class="flex-1 bg-[#1c1d21] hover:bg-gray-800 border border-gray-700 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition text-xs">Abort</button>
                        <button @click="executeDeleteBranch" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg uppercase tracking-widest transition shadow-[0_0_15px_rgba(220,38,38,0.4)] text-xs">Confirm Purge</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[1000] bg-black/95 flex items-center justify-center backdrop-blur-md overflow-hidden" 
             @mousemove="onDrag" @mouseup="stopDrag" @mouseleave="stopDrag" @wheel.prevent="handleWheel">
            
            <button @click="closeModal" class="absolute top-6 right-6 z-[1001] text-gray-400 hover:text-white bg-[#18191c] border border-gray-700 hover:border-gray-500 rounded-full p-3 transition shadow-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" 
                 @mousedown.self="closeModal" @mousedown="startDrag">
                <img :src="activeImage" draggable="false"
                     class="max-w-[90vw] max-h-[90vh] object-contain transition-transform duration-100 ease-out origin-center" 
                     :style="{ transform: `translate(${pan.x}px, ${pan.y}px) scale(${zoomLevel})` }" />
            </div>

            <div class="absolute bottom-10 right-10 flex bg-[#18191c] border border-gray-700 rounded-lg shadow-[0_0_30px_rgba(0,0,0,0.8)] overflow-hidden z-[1001] select-none">
                <button @click="zoomOut" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 focus:outline-none" title="Zoom Out"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg></button>
                <button @click="resetZoom" class="px-6 py-4 text-xs font-bold text-gray-400 hover:text-white hover:bg-gray-800 transition border-r border-gray-800 tracking-widest uppercase focus:outline-none" title="Reset Zoom">Reset</button>
                <button @click="zoomIn" class="px-5 py-4 text-gray-400 hover:text-white hover:bg-gray-800 transition focus:outline-none" title="Zoom In"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg></button>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    branches: Array, selectedBranch: Object, manager: Object, pcs: Array, reports: Array, reservations: Array, floorplans: Array
});

// TOASTS
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 4000);
};

// --- SEARCH & FILTER LOGIC FOR SIDEBAR ---
const searchQuery = ref('');
const regionMap = {
    'cavite': ['imus', 'bacoor'], 'bulacan': ['baliwag', 'malolos', 'sjdm'],
    'manila': ['anonas', 'españa', 'taft', 'gastambide', 'morayta'],
    'qc': ['novaliches', 'lagro', 'tandang sora'], 'makati': ['comembo', 'evangelista', 'guadalupe']
};

const filteredBranches = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.branches;
    const targets = regionMap[query] ? regionMap[query] : [query];
    return props.branches.filter(b => targets.some(k => b.name.toLowerCase().includes(k) || b.address.toLowerCase().includes(k)));
});

// SELECT BRANCH
const selectBranch = (id) => {
    router.get(route('hq.branches'), { branch_id: id }, { preserveState: true, preserveScroll: true });
};

// SCROLL TO REPORTS
const scrollToReports = () => {
    document.getElementById('reports-section').scrollIntoView({ behavior: 'smooth' });
};

// GRID STYLES
const getGridStyle = (pc) => {
    if (pc.status === 'free') return 'border-green-500/50 hover:border-green-400';
    if (pc.status === 'occupied') return 'border-blue-500/50 bg-[#1c1d21]';
    if (pc.status === 'reserved') return 'border-orange-500/50 bg-[#1c1d21]';
    return 'border-red-900/50 opacity-50 bg-black';
};

// ADD BRANCH
const showAddBranchModal = ref(false);
const addBranchForm = useForm({ 
    name: '', address: '', initial_pcs: 20,
    manager_username: '', manager_email: '', manager_password: '',
    schema_image: null 
});

const submitAddBranch = () => {
    addBranchForm.post(route('hq.branches.store'), {
        preserveScroll: true,
        onSuccess: () => { showAddBranchModal.value = false; showToast("New branch network deployed."); addBranchForm.reset(); },
        onError: () => { showToast("Validation failed. Check the highlighted fields.", "error"); }
    });
};

// EDIT BRANCH
const showEditBranchModal = ref(false);
const showDeleteConfirm = ref(false);

const editBranchForm = useForm({ 
    name: '', address: '', manager_username: '', manager_password: '', add_pcs: 0, schema_image: null 
});

watch(() => props.selectedBranch, (newVal) => {
    if (newVal) { 
        editBranchForm.name = newVal.name; 
        editBranchForm.address = newVal.address; 
        editBranchForm.manager_username = props.manager ? props.manager.username : '';
        editBranchForm.add_pcs = 0;
        editBranchForm.schema_image = null;
    }
}, { immediate: true });

const submitEditBranch = () => {
    editBranchForm.post(route('hq.branches.full_update', props.selectedBranch.id), {
        preserveScroll: true,
        onSuccess: () => { showEditBranchModal.value = false; showToast("Branch settings synchronized."); },
        onError: () => { showToast("Validation failed. Check the highlighted fields.", "error"); }
    });
};

const executeDeleteBranch = () => {
    router.delete(route('hq.branches.destroy', props.selectedBranch.id), {
        onSuccess: () => { showDeleteConfirm.value = false; showEditBranchModal.value = false; showToast("Branch completely wiped."); }
    });
};

// SCHEMATICS ZOOM/PAN LOGIC
const showModal = ref(false);
const activeImage = ref('');
const zoomLevel = ref(1);
const pan = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const getBranchImages = (branchName) => {
    if (!props.floorplans || !branchName) return [];
    const formattedName = branchName.toLowerCase().replace(/ /g, '_');
    return props.floorplans.filter(path => path.toLowerCase().includes(formattedName));
};

const openModal = (imgSrc) => { activeImage.value = imgSrc; resetZoom(); showModal.value = true; };
const closeModal = () => { showModal.value = false; activeImage.value = ''; resetZoom(); };
const zoomIn = () => { if (zoomLevel.value < 4) zoomLevel.value += 0.25; };
const zoomOut = () => { if (zoomLevel.value > 1) zoomLevel.value -= 0.25; if (zoomLevel.value === 1) pan.value = { x: 0, y: 0 }; };
const resetZoom = () => { zoomLevel.value = 1; pan.value = { x: 0, y: 0 }; };
const handleWheel = (e) => { e.deltaY < 0 ? zoomIn() : zoomOut(); };
const startDrag = (e) => { if (zoomLevel.value <= 1) return; isDragging.value = true; dragStart.value = { x: e.clientX - pan.value.x, y: e.clientY - pan.value.y }; };
const onDrag = (e) => { if (!isDragging.value || zoomLevel.value <= 1) return; pan.value = { x: e.clientX - dragStart.value.x, y: e.clientY - dragStart.value.y }; };
const stopDrag = () => { isDragging.value = false; };
</script>

<style scoped>
@font-face { font-family: 'ArgentumNovus'; src: url('/fonts/ArgentumNovus-SemiBold.ttf') format('truetype'); font-weight: 600; }
.font-argentum { font-family: 'ArgentumNovus', sans-serif; }
.custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(50px); }
</style>