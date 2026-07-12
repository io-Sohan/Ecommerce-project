<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    items: { label: string; href?: string }[];
}>();
</script>

<template>
    <nav class="mb-6 md:mb-8" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm">
            <li class="flex items-center gap-1.5">
                <Link
                    :href="home()"
                    class="font-medium text-gray-400 transition hover:text-shop-primary-600"
                >
                    Home
                </Link>
                <svg
                    class="h-3.5 w-3.5 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </li>
            <li
                v-for="(item, index) in items"
                :key="item.label"
                class="flex items-center gap-1.5"
            >
                <Link
                    v-if="item.href && index < items.length - 1"
                    :href="item.href"
                    class="font-medium text-gray-400 transition hover:text-shop-primary-600"
                >
                    {{ item.label }}
                </Link>
                <span
                    v-else
                    class="font-semibold text-gray-900"
                    aria-current="page"
                    >{{ item.label }}</span
                >
                <svg
                    v-if="index < items.length - 1"
                    class="h-3.5 w-3.5 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </li>
        </ol>
    </nav>
</template>
