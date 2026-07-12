<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

const { variant = 'light' } = defineProps<{
    variant?: 'light' | 'dark';
}>();
</script>

<template>
    <Link
        :href="home()"
        class="group flex items-center gap-2.5"
        aria-label="ShopEase home"
    >
        <span
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-shop-primary-600 to-purple-600 text-white shadow-lg shadow-shop-primary-600/25 transition-transform duration-200 group-hover:scale-105"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                />
            </svg>
        </span>
        <span
            class="text-xl font-extrabold tracking-tight"
            :class="variant === 'dark' ? 'text-white' : 'text-gray-900'"
        >
            Shop<span class="shop-gradient-text">Ease</span>
        </span>
    </Link>
</template>
