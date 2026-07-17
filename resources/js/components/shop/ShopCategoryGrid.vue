<script setup lang="ts">
import shop from '@/routes/shop';
import type { ShopCategory } from '@/types/shop';

defineProps<{
    categories: ShopCategory[];
}>();
</script>

<template>
    <section id="categories" class="bg-white py-14 md:py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <span
                        class="text-xs font-bold tracking-widest text-shop-primary-600 uppercase"
                        >Browse</span
                    >
                    <h2
                        class="mt-1 text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl"
                    >
                        Shop by category
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 md:text-base">
                        Find exactly what you're looking for.
                    </p>
                </div>
                <a
                    :href="shop.index.url()"
                    class="hidden items-center gap-1.5 rounded-xl bg-shop-primary-50 px-4 py-2 text-sm font-semibold text-shop-primary-700 transition hover:bg-shop-primary-100 sm:inline-flex"
                >
                    All categories
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
                </a>
            </div>

            <div
                class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:gap-5 lg:grid-cols-6"
            >
                <a
                    v-for="category in categories"
                    :key="category.name"
                    :href="category.href"
                    class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/80 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-xl hover:ring-shop-primary-200"
                >
                    <div
                        class="relative aspect-square overflow-hidden bg-gray-50"
                    >
                        <img
                            :src="category.img"
                            :alt="category.name"
                            loading="lazy"
                            class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-110"
                        />
                        <div
                            class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 transition duration-300 group-hover:opacity-100"
                        />
                        <div
                            class="pointer-events-none absolute inset-0 flex items-end justify-end p-3 opacity-0 transition duration-300 group-hover:opacity-100"
                        >
                            <span
                                class="inline-flex h-9 w-9 translate-y-2 items-center justify-center rounded-xl bg-white text-shop-primary-600 shadow-lg transition duration-300 group-hover:translate-y-0"
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
                                        d="M5 12h14M13 6l6 6-6 6"
                                    />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <span
                        class="block px-3 py-3.5 text-center text-sm font-semibold text-gray-900 transition group-hover:text-shop-primary-600"
                        >{{ category.name }}</span
                    >
                </a>
            </div>
        </div>
    </section>
</template>
