<script setup lang="ts">
import { useShopUi } from '@/composables/shop/useShopUi';

const { showToast } = useShopUi();

function handleSubmit(event: Event): void {
    event.preventDefault();
    showToast('Subscribed! Check your inbox 🎉');
}
</script>

<template>
    <section class="relative overflow-hidden bg-shop-primary-600">
        <!-- Decorative blobs -->
        <div
            class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/5"
        />
        <div
            class="pointer-events-none absolute -right-24 -bottom-24 h-72 w-72 rounded-full bg-black/10"
        />

        <div
            class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8"
        >
            <div
                class="flex flex-col items-center gap-8 text-center md:flex-row md:justify-between md:text-left"
            >
                <div class="max-w-lg">
                    <span
                        class="inline-block rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold tracking-wider text-white uppercase"
                    >
                        🎉 Exclusive offer
                    </span>
                    <h2
                        class="mt-4 text-3xl font-extrabold tracking-tight text-white md:text-4xl"
                    >
                        Get 10% off your first order
                    </h2>
                    <p class="mt-3 text-base leading-relaxed text-white/80">
                        Subscribe for new arrivals, exclusive deals, and offers
                        delivered to your inbox.
                    </p>
                </div>
                <form
                    class="flex w-full max-w-md flex-col gap-3 sm:flex-row"
                    @submit="handleSubmit"
                >
                    <label for="newsletterEmail" class="sr-only"
                        >Email address</label
                    >
                    <input
                        id="newsletterEmail"
                        type="email"
                        required
                        placeholder="Enter your email"
                        class="w-full rounded-xl border-0 bg-white/95 px-5 py-3 text-sm font-medium text-gray-900 shadow-lg placeholder:text-gray-400 focus:ring-2 focus:ring-white focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="shrink-0 rounded-xl bg-shop-dark px-6 py-3 text-sm font-bold text-white shadow-lg transition duration-200 hover:bg-shop-dark-light hover:shadow-xl focus:ring-2 focus:ring-white focus:outline-none"
                    >
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
