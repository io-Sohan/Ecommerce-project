<script setup lang="ts">
import { computed } from 'vue';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';

const { page, totalPages, setPage } = useShopCatalog();

const pages = computed(() =>
    Array.from({ length: totalPages.value }, (_, i) => i + 1),
);
</script>

<template>
    <nav
        v-if="totalPages > 1"
        class="mt-12 flex items-center justify-center gap-1.5"
        aria-label="Pagination"
    >
        <button
            type="button"
            :disabled="page === 1"
            aria-label="Previous page"
            class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-sm font-semibold transition duration-200"
            :class="
                page === 1
                    ? 'cursor-not-allowed text-gray-300'
                    : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-600'
            "
            @click="setPage(page - 1)"
        >
            <svg
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
        </button>

        <button
            v-for="pageNumber in pages"
            :key="pageNumber"
            type="button"
            class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-sm font-semibold transition duration-200"
            :class="
                pageNumber === page
                    ? 'bg-gradient-to-r from-shop-primary-600 to-purple-600 text-white shadow-lg shadow-shop-primary-600/25'
                    : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-600'
            "
            @click="setPage(pageNumber)"
        >
            {{ pageNumber }}
        </button>

        <button
            type="button"
            :disabled="page === totalPages"
            aria-label="Next page"
            class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-sm font-semibold transition duration-200"
            :class="
                page === totalPages
                    ? 'cursor-not-allowed text-gray-300'
                    : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-600'
            "
            @click="setPage(page + 1)"
        >
            <svg
                class="h-4 w-4"
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
        </button>
    </nav>
</template>
