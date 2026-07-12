<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type ActiveCoupon = {
    code: string;
    discount_type: 'flat' | 'percentage';
    discount_value: number;
    min_order_amount: number;
    expires_at: string | null;
};

const page = usePage();
const dismissed = ref(false);

const coupons = computed<ActiveCoupon[]>(
    () => (page.props.activeCoupons as ActiveCoupon[]) ?? [],
);

const currentIndex = ref(0);

const currentCoupon = computed(() => coupons.value[currentIndex.value] ?? null);

function formatDiscount(coupon: ActiveCoupon): string {
    return coupon.discount_type === 'percentage'
        ? `${coupon.discount_value}% OFF`
        : `৳${coupon.discount_value} OFF`;
}

function nextCoupon(): void {
    if (coupons.value.length > 1) {
        currentIndex.value = (currentIndex.value + 1) % coupons.value.length;
    }
}

function prevCoupon(): void {
    if (coupons.value.length > 1) {
        currentIndex.value =
            (currentIndex.value - 1 + coupons.value.length) % coupons.value.length;
    }
}

function copyCode(code: string): void {
    navigator.clipboard.writeText(code);
}
</script>

<template>
    <div
        v-if="coupons.length > 0 && !dismissed"
        class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white"
    >
        <div class="mx-auto flex max-w-7xl items-center justify-center gap-3 px-4 py-2.5 text-center text-sm sm:px-6">
            <button
                v-if="coupons.length > 1"
                type="button"
                aria-label="Previous coupon"
                class="shrink-0 rounded p-0.5 transition hover:bg-white/20"
                @click="prevCoupon"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <div v-if="currentCoupon" class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1">
                <span class="font-semibold">🎉 {{ formatDiscount(currentCoupon) }}</span>
                <span class="hidden sm:inline">—</span>
                <span class="text-white/90">
                    Use code
                    <button
                        type="button"
                        class="mx-1 inline-flex items-center gap-1 rounded bg-white/20 px-2 py-0.5 font-mono font-bold tracking-wider transition hover:bg-white/30"
                        :title="`Copy code: ${currentCoupon.code}`"
                        @click="copyCode(currentCoupon.code)"
                    >
                        {{ currentCoupon.code }}
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    at checkout
                </span>
                <span
                    v-if="currentCoupon.min_order_amount > 0"
                    class="text-xs text-white/70"
                >
                    (min. order ৳{{ currentCoupon.min_order_amount }})
                </span>
            </div>

            <button
                v-if="coupons.length > 1"
                type="button"
                aria-label="Next coupon"
                class="shrink-0 rounded p-0.5 transition hover:bg-white/20"
                @click="nextCoupon"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <button
            type="button"
            aria-label="Dismiss coupon banner"
            class="absolute top-1/2 right-3 -translate-y-1/2 rounded p-1 transition hover:bg-white/20"
            @click="dismissed = true"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
