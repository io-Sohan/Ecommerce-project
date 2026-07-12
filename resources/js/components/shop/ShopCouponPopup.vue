<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

type ActiveCoupon = {
    code: string;
    discount_type: 'flat' | 'percentage';
    discount_value: number;
    min_order_amount: number;
    expires_at: string | null;
};

const page = usePage();
const visible = ref(false);
let autoCloseTimer: ReturnType<typeof setTimeout> | null = null;

const coupons = computed<ActiveCoupon[]>(
    () => (page.props.activeCoupons as ActiveCoupon[]) ?? [],
);

const hasCoupons = computed(() => coupons.value.length > 0);

function formatDiscount(coupon: ActiveCoupon): string {
    return coupon.discount_type === 'percentage'
        ? `${coupon.discount_value}% OFF`
        : `৳${coupon.discount_value} OFF`;
}

function dismiss(): void {
    visible.value = false;

    if (autoCloseTimer) {
        clearTimeout(autoCloseTimer);
        autoCloseTimer = null;
    }
}

function copyCode(code: string): void {
    navigator.clipboard.writeText(code);
}

onMounted(() => {
    if (!hasCoupons.value) {
        return;
    }

    // Show after a small delay so the page renders first
    setTimeout(() => {
        visible.value = true;

        // Auto-dismiss after 10 seconds
        autoCloseTimer = setTimeout(() => {
            dismiss();
        }, 10000);
    }, 500);
});

onUnmounted(() => {
    if (autoCloseTimer) {
        clearTimeout(autoCloseTimer);
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="visible"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                @click.self="dismiss"
            >
                <Transition
                    appear
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                >
                    <div
                        v-if="visible"
                        class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
                    >
                        <!-- Close button -->
                        <button
                            type="button"
                            aria-label="Close"
                            class="absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/10 text-gray-600 transition hover:bg-black/20 hover:text-gray-900"
                            @click="dismiss"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Header -->
                        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-8 text-center text-white">
                            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-3xl backdrop-blur-sm">
                                🎉
                            </div>
                            <h2 class="text-xl font-extrabold">Special Offer!</h2>
                            <p class="mt-1 text-sm text-white/80">
                                Use these coupon codes to save on your order
                            </p>
                        </div>

                        <!-- Coupons list -->
                        <div class="max-h-64 space-y-3 overflow-y-auto px-6 py-5">
                            <div
                                v-for="coupon in coupons"
                                :key="coupon.code"
                                class="flex items-center justify-between rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 px-4 py-3"
                            >
                                <div>
                                    <p class="text-lg font-extrabold text-indigo-700">
                                        {{ formatDiscount(coupon) }}
                                    </p>
                                    <p v-if="coupon.min_order_amount > 0" class="text-xs text-gray-500">
                                        Min. order ৳{{ coupon.min_order_amount }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    :title="`Copy code: ${coupon.code}`"
                                    @click="copyCode(coupon.code)"
                                >
                                    <span class="font-mono tracking-wider">{{ coupon.code }}</span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-100 px-6 py-4 text-center">
                            <button
                                type="button"
                                class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:shadow-xl"
                                @click="dismiss"
                            >
                                Start Shopping 🛍️
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
