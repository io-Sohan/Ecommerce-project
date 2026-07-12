<script setup lang="ts">
import { useShopCatalog } from '@/composables/shop/useShopCatalog';

const { sort, setSort, total, openFilterDrawer } = useShopCatalog();
</script>

<template>
    <div
        class="mb-6 flex items-center justify-between gap-3"
    >
        <p class="text-sm text-gray-500">
            <span class="font-bold text-gray-900">{{ total }}</span>
            products found
        </p>
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex h-10 items-center gap-2 rounded-xl bg-white px-4 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50 focus:ring-2 focus:ring-shop-primary-500 focus:outline-none lg:hidden"
                @click="openFilterDrawer"
            >
                <svg
                    class="h-4 w-4 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                    />
                </svg>
                Filters
            </button>
            <div class="relative">
                <label for="sortSelect" class="sr-only">Sort by</label>
                <select
                    id="sortSelect"
                    :value="sort"
                    class="h-10 appearance-none rounded-xl bg-white py-0 pr-9 pl-4 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition focus:ring-2 focus:ring-shop-primary-500 focus:outline-none"
                    @change="setSort(($event.target as HTMLSelectElement).value as 'newest' | 'best' | 'price-asc' | 'price-desc')"
                >
                    <option value="newest">Newest</option>
                    <option value="best">Best Selling</option>
                    <option value="price-asc">Price: Low → High</option>
                    <option value="price-desc">Price: High → Low</option>
                </select>
                <svg
                    class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </div>
        </div>
    </div>
</template>
