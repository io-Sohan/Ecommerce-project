<script setup lang="ts">
import { useShopUi } from '@/composables/shop/useShopUi';

const { toastMessage, isToastVisible } = useShopUi();
</script>

<template>
    <div
        class="pointer-events-none fixed top-20 right-4 z-[80] flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-shop-primary-600 to-shop-primary-800 px-5 py-3.5 text-sm font-bold text-white shadow-2xl shadow-shop-primary-600/25 transition-all duration-300"
        :class="
            isToastVisible
                ? 'translate-y-0 opacity-100'
                : 'translate-y-2 opacity-0'
        "
    >
        <svg
            class="h-5 w-5 shrink-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M5 13l4 4L19 7"
            />
        </svg>
        {{ toastMessage }}
    </div>
</template>
