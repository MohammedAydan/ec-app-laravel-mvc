<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-8 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-5xl mx-auto flex flex-col gap-2">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Instant delivery</p>
                <h1 class="text-3xl sm:text-4xl font-black leading-tight">Checkout</h1>
                <p class="text-slate-300 text-sm sm:text-base">Secure payment for your digital items. Files unlock immediately after payment.</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 sm:p-8 space-y-6">
                        <div class="space-y-2">
                            <h2 class="text-xl font-semibold text-slate-900">Payment details</h2>
                            <p class="text-sm text-slate-500">We use industry-standard encryption. No card data is stored on our servers.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex gap-3 text-xs text-slate-500">
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Visa</span>
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold">Mastercard</span>
                                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">Amex</span>
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">Apple Pay</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Card holder</label>
                                    <input type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3" placeholder="Jane Doe">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Email for delivery</label>
                                    <input type="email" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3" placeholder="you@example.com">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Card number</label>
                                <div class="mt-2 relative">
                                    <input type="text" inputmode="numeric" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3 pr-12" placeholder="4242 4242 4242 4242">
                                    <div class="absolute inset-y-0 right-3 flex items-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Expiry</label>
                                    <input type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3" placeholder="MM / YY">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">CVC</label>
                                    <input type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3" placeholder="123">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Zip</label>
                                    <input type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3" placeholder="90210">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 text-white px-6 py-4 text-base font-semibold hover:bg-indigo-600 transition">
                                Complete payment & download
                            </button>
                            <p class="text-xs text-slate-500">By paying you agree to our terms. Instant access, no shipping needed.</p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 sm:p-7 space-y-6">
                        <div class="space-y-1">
                            <h3 class="text-lg font-semibold text-slate-900">Order summary</h3>
                            <p class="text-sm text-slate-500">Digital delivery to your email.</p>
                        </div>

                        <div class="space-y-4 divide-y divide-slate-100">
                            <div class="flex items-start justify-between pt-1">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Pro Icon Pack</p>
                                    <p class="text-xs text-slate-500">600 SVG files • Lifetime updates</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">$24.00</span>
                            </div>
                            <div class="flex items-start justify-between pt-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">UI Template</p>
                                    <p class="text-xs text-slate-500">Figma file • Commercial license</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">$39.00</span>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span>$63.00</span>
                            </div>
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Instant delivery</span>
                                <span>Free</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-base font-semibold text-slate-900">
                            <span>Total</span>
                            <span>$63.00</span>
                        </div>

                        <div class="space-y-2 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Files delivered instantly after payment</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                                <span>Secure checkout • 256-bit SSL</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                                <span>Lifetime updates included</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
