<template>
    <Head title="Create Account - KDM Stratus" />

    <div class="min-h-screen bg-[#101113] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-blue-500 selection:text-white relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-20 w-72 h-72 bg-green-900/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-2xl relative z-10">
            <div class="flex justify-center mb-6">
                <a href="/" class="hover:scale-105 transition transform cursor-pointer">
                    <img src="/images/logo.png" alt="KDM Logo" class="h-20 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]" onerror="this.style.display='none';" />
                </a>
            </div>
            <h2 class="mt-2 text-center font-argentum text-3xl font-extrabold text-white uppercase tracking-widest">
                Create Account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400 font-bold tracking-widest uppercase">
                Join the KDM Stratus network
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl relative z-10">
            <div class="bg-[#18191c] py-8 px-6 shadow-2xl sm:rounded-xl border border-gray-800 sm:px-10">
                
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                First Name 
                                <span v-if="firstNameValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <input v-model="form.first_name" type="text" required
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        </div>
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                Last Name 
                                <span v-if="lastNameValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <input v-model="form.last_name" type="text" required
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                Username 
                                <svg v-if="checkingUsername" class="animate-spin ml-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span v-else-if="usernameValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else-if="usernameValid === false" class="ml-2 text-red-500 text-lg font-bold leading-none">!</span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <input v-model="form.username" type="text" required @blur="checkUsername" @input="resetUsernameCheck"
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                                :class="{'border-red-500': usernameAvailable === false}">
                            
                            <div v-if="usernameAvailable === false" class="text-red-500 text-xs mt-1 font-bold">This username is already taken.</div>
                        </div>
                        
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                Email Address 
                                <svg v-if="checkingEmail" class="animate-spin ml-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span v-else-if="emailValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else-if="emailValid === false" class="ml-2 text-red-500 text-lg font-bold leading-none">!</span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <input v-model="form.email" type="email" required placeholder="name@example.com" @blur="checkEmail" @input="resetEmailCheck"
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                                :class="{'border-red-500': (!isEmailFormatValid && form.email.length > 0) || emailAvailable === false}">
                            
                            <div v-if="!isEmailFormatValid && form.email.length > 0" class="text-red-500 text-xs mt-1 font-bold">Please enter a valid email address.</div>
                            <div v-else-if="emailAvailable === false" class="text-red-500 text-xs mt-1 font-bold">This email is already registered.</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                Contact Number 
                                <span v-if="phoneValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else-if="phoneValid === false" class="ml-2 text-red-500 text-lg font-bold leading-none">!</span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <div class="flex items-center bg-[#1c1d21] border border-gray-700 rounded-lg overflow-hidden focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition"
                                 :class="{'border-red-500': contactInput.length > 0 && !phoneValid}">
                                <span class="px-4 py-3 bg-[#101113] text-gray-500 border-r border-gray-700 font-bold select-none">+63</span>
                                <input v-model="contactInput" @input="formatPhone" type="text" required placeholder="9123456789"
                                    class="w-full bg-transparent px-4 py-3 text-white focus:outline-none tracking-widest">
                            </div>
                            <div v-if="contactInput.length > 0 && !phoneValid" class="text-red-500 text-xs mt-1">Requires exactly 10 digits.</div>
                        </div>
                        <div class="relative">
                            <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                Date of Birth 
                                <span v-if="dobValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                <span v-else-if="dobValid === false" class="ml-2 text-red-500 text-lg font-bold leading-none">!</span>
                                <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                            </label>
                            <input v-model="form.dob" type="date" required :max="maxAllowedDate"
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition [color-scheme:dark]"
                                :class="{'border-red-500': dobValid === false}">
                            <div v-if="dobValid === false" class="text-red-500 text-xs mt-1 font-bold">You must be at least 5 years old.</div>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                            Password 
                            <span v-if="isPasswordValid === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                            <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                        </label>
                        <div class="relative">
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition pr-12">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-3 text-gray-500 hover:text-white transition">
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 bg-[#101113] border border-gray-700 rounded-lg p-5 shadow-inner" :class="{'border-green-500/50 bg-green-900/10': isPasswordValid}">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Password Requirements</p>
                            <div class="grid grid-cols-2 gap-3 text-sm font-semibold">
                                <span :class="passwordLength ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                    <svg v-if="passwordLength" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-5 h-5 border-2 border-gray-600 rounded-full"></span> 8+ Characters
                                </span>
                                <span :class="passwordUpper ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                    <svg v-if="passwordUpper" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-5 h-5 border-2 border-gray-600 rounded-full"></span> Uppercase Letter
                                </span>
                                <span :class="passwordNumber ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                    <svg v-if="passwordNumber" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-5 h-5 border-2 border-gray-600 rounded-full"></span> One Number
                                </span>
                                <span :class="passwordSymbol ? 'text-green-400' : 'text-gray-600'" class="flex items-center gap-2 transition-colors">
                                    <svg v-if="passwordSymbol" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span v-else class="w-5 h-5 border-2 border-gray-600 rounded-full"></span> Special Symbol
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                            Confirm Password 
                            <span v-if="passwordsMatch === true" class="ml-2 text-green-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                            <span v-else-if="passwordsMatch === false" class="ml-2 text-red-500 text-lg font-bold leading-none">!</span>
                            <span v-else class="ml-2 text-red-500 text-lg leading-none">*</span>
                        </label>
                        <div class="relative">
                            <input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" required
                                class="w-full bg-[#1c1d21] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition pr-12"
                                :class="{'border-red-500': form.password_confirmation.length > 0 && !passwordsMatch}">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-3 text-gray-500 hover:text-white transition">
                                <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center mt-6 p-4 border border-gray-700 rounded-lg transition-colors" :class="{'bg-green-900/20 border-green-500/50': form.agreed, 'bg-[#222328]': !form.agreed}">
                        <div class="flex-shrink-0 relative">
                            <div v-if="form.agreed" class="h-5 w-5 rounded-full bg-green-500 flex items-center justify-center shadow-[0_0_10px_rgba(34,197,94,0.5)]">
                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div v-else class="h-5 w-5 rounded border-2 border-gray-500 bg-[#1c1d21]"></div>
                        </div>
                        <div class="ml-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
                            <button type="button" @click="showPrivacyModal = true" class="text-blue-400 hover:text-blue-300 transition underline decoration-blue-500/50 underline-offset-4">
                                Read Data & Privacy Agreement
                            </button>
                            <span class="ml-1 text-gray-500">(Required)</span>
                        </div>
                    </div>
                    <div v-if="form.errors.agreed" class="text-xs text-red-500 font-bold mt-1">{{ form.errors.agreed }}</div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing || !isFormFullyValid" 
                            class="w-full font-argentum uppercase tracking-widest py-4 rounded-lg text-lg transition-all duration-300"
                            :class="isFormFullyValid ? 'bg-white hover:bg-gray-200 text-black shadow-[0_0_20px_rgba(255,255,255,0.2)] transform hover:-translate-y-1 cursor-pointer' : 'bg-gray-800 text-gray-500 cursor-not-allowed'">
                            Register Account
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">
                    Already have an account? 
                    <a href="/login" class="text-blue-500 hover:text-blue-400 ml-1">Log in here</a>
                </p>
            </div>
        </div>

        <div v-if="showPrivacyModal" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-[#18191c] border border-gray-800 rounded-xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[80vh]">
                
                <div class="bg-[#222328] px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="font-argentum text-lg text-white uppercase tracking-widest">Data & Privacy</h3>
                    <div class="flex gap-3">
                        <button type="button" @click="showPrivacyModal = false" class="px-4 py-2 text-[10px] font-bold text-gray-400 hover:text-white uppercase transition tracking-widest">Cancel</button>
                        <button type="button" @click="acceptAgreement" :disabled="!hasReachedBottom" class="bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold px-6 py-2 rounded uppercase tracking-widest transition disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ hasReachedBottom ? 'I Agree' : 'Scroll to Agree' }}
                        </button>
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4 text-sm text-gray-400 leading-relaxed" @scroll="checkScroll">
                    <p class="text-white font-bold uppercase tracking-widest mb-4 border-b border-gray-800 pb-2">KDM Stratus Network - User Agreement</p>
                    
                    <p>By registering for an account on the KDM Stratus Network, you agree to the collection, processing, and storage of your personal and telemetry data as outlined below.</p>
                    
                    <p class="font-bold text-gray-300">1. Data Collection</p>
                    <p>We collect essential identification data including your full name, email address, username, and contact number. We also record network activity, terminal reservation history, session durations, and wallet transaction logs.</p>
                    
                    <p class="font-bold text-gray-300">2. Usage of Data</p>
                    <p>Your data is strictly utilized for core operational purposes: authenticating terminal access, calculating reservation and usage fees, verifying account ownership, and providing localized branch support.</p>
                    
                    <p class="font-bold text-gray-300">3. Network Telemetry</p>
                    <p>To maintain infrastructure health, KDM Stratus actively monitors the physical status and software activity of the terminal you occupy. This data is transmitted to our Executive HQ for capacity planning.</p>
                    
                    <p class="font-bold text-gray-300">4. Data Protection & Deletion</p>
                    <p>Your credentials are cryptographically secured. We do not sell your data to third-party advertisers. You may submit a request to your local Branch Manager to permanently delete your account and wipe your network history.</p>

                    <p class="font-bold text-gray-300">5. Multi-Factor Authentication</p>
                    <p>You acknowledge that KDM Stratus employs Email OTPs and Recovery Codes to secure your digital wallet and terminal access. It is your responsibility to safely secure these secondary backup codes immediately upon account generation.</p>

                    <div class="mt-8 p-4 bg-blue-900/20 border border-blue-500/30 rounded text-center">
                        <p class="text-blue-400 font-bold uppercase tracking-widest text-xs animate-pulse">End of Agreement. You may now click 'I Agree' above.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const contactInput = ref('');

// MODAL & SCROLL STATE
const showPrivacyModal = ref(false);
const hasReachedBottom = ref(false);

const form = useForm({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    contact_number: '', 
    dob: '',
    password: '',
    password_confirmation: '',
    agreed: false, // Added validation field
});

// SCROLL TRACKER FIX
const checkScroll = (e) => {
    const el = e.target;
    // Using Math.ceil to fix fractional pixel rounding issues
    if (Math.ceil(el.scrollTop + el.clientHeight) >= el.scrollHeight - 5) {
        hasReachedBottom.value = true;
    }
};

const acceptAgreement = () => {
    form.agreed = true;
    showPrivacyModal.value = false;
};

// Dynamic Max Date Calculation
const calculateMaxDate = () => {
    const today = new Date();
    today.setFullYear(today.getFullYear() - 5);
    return today.toISOString().split('T')[0];
};
const maxAllowedDate = ref(calculateMaxDate());

// Database Async State
const usernameAvailable = ref(null);
const emailAvailable = ref(null);
const checkingUsername = ref(false);
const checkingEmail = ref(false);

const formatPhone = () => { contactInput.value = contactInput.value.replace(/\D/g, '').slice(0, 10); };

// Background Verification Logic
const checkUsername = async () => {
    if (form.username.length < 3) return;
    checkingUsername.value = true;
    try {
        const response = await axios.post('/check-username', { username: form.username });
        usernameAvailable.value = response.data.available;
    } catch (e) { console.error(e); }
    checkingUsername.value = false;
};
const resetUsernameCheck = () => { usernameAvailable.value = null; };

const checkEmail = async () => {
    if (!isEmailFormatValid.value) return;
    checkingEmail.value = true;
    try {
        const response = await axios.post('/check-email', { email: form.email });
        emailAvailable.value = response.data.available;
    } catch (e) { console.error(e); }
    checkingEmail.value = false;
};
const resetEmailCheck = () => { emailAvailable.value = null; };

// Inline Validation Computeds
const firstNameValid = computed(() => form.first_name.length > 0 ? true : null);
const lastNameValid = computed(() => form.last_name.length > 0 ? true : null);
const usernameValid = computed(() => (form.username.length > 0 && usernameAvailable.value === true) ? true : (usernameAvailable.value === false ? false : null));
const isEmailFormatValid = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email));
const emailValid = computed(() => (isEmailFormatValid.value && emailAvailable.value === true) ? true : (emailAvailable.value === false || (!isEmailFormatValid.value && form.email.length > 0) ? false : null));
const phoneValid = computed(() => contactInput.value.length === 10 ? true : (contactInput.value.length > 0 ? false : null));

const dobValid = computed(() => {
    if (!form.dob) return null;
    return form.dob <= maxAllowedDate.value ? true : false;
});

const passwordLength = computed(() => form.password.length >= 8);
const passwordUpper = computed(() => /[A-Z]/.test(form.password));
const passwordNumber = computed(() => /[0-9]/.test(form.password));
const passwordSymbol = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(form.password));
const isPasswordValid = computed(() => passwordLength.value && passwordUpper.value && passwordNumber.value && passwordSymbol.value ? true : (form.password.length > 0 ? false : null));
const passwordsMatch = computed(() => (form.password_confirmation.length > 0 && form.password === form.password_confirmation) ? true : (form.password_confirmation.length > 0 ? false : null));

// FULL FORM VALIDATION INCLUDING AGREEMENT
const isFormFullyValid = computed(() => {
    return firstNameValid.value === true && 
           lastNameValid.value === true && 
           usernameValid.value === true && 
           emailValid.value === true && 
           phoneValid.value === true &&
           dobValid.value === true &&
           isPasswordValid.value === true && 
           passwordsMatch.value === true &&
           form.agreed === true; // Requires the checkbox to be checked!
});

const submit = () => {
    form.contact_number = '+63' + contactInput.value;
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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

/* Custom Scrollbar for the Privacy Text */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>