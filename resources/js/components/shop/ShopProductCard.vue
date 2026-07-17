<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ShopStarRating from '@/components/shop/ShopStarRating.vue';
import { useShopCart } from '@/composables/shop/useShopCart';
import { useShopUi } from '@/composables/shop/useShopUi';
import { useShopWishlist } from '@/composables/shop/useShopWishlist';
import { formatTaka } from '@/lib/shop/currency';
import { productShowUrl, savingsPercent } from '@/lib/shop/product';
import type { ShopProduct } from '@/types/shop';

const { product } = defineProps<{
    product: ShopProduct;
}>();

const { addToCart } = useShopCart();
const { showToast } = useShopUi();
const { isWishlisted, toggleWish } = useShopWishlist();

function handleAddToCart(): void {
    if (!product.id || !product.inStock) {
        return;
    }

    addToCart(product.id, 1, true);
}

function handleBuyNow(): void {
    if (!product.id || !product.inStock) {
        return;
    }

    addToCart(product.id, 1, true);
}

function handleToggleWishlist(): void {
    if (!product.id) {
        return;
    }

    toggleWish(product, showToast);
}
</script>

<template>
    <article
        class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/80 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-xl hover:ring-shop-primary-200"
    >
        <div class="relative aspect-square overflow-hidden bg-gray-50">
            <Link
                :href="productShowUrl(product)"
                :aria-label="`View ${product.name}`"
                class="block h-full w-full"
            >
                <img
                    :src="product.img"
                    :alt="product.name"
                    loading="lazy"
                    class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-110"
                    :class="{ grayscale: !product.inStock }"
                />
            </Link>
            <div
                v-if="!product.inStock"
                class="pointer-events-none absolute inset-0 flex items-center justify-center bg-white/30 backdrop-blur-[1px]"
            >
                <span
                    class="rounded-xl bg-gray-900/85 px-4 py-1.5 text-xs font-bold tracking-wider text-white uppercase shadow-lg"
                    >Stock Out</span
                >
            </div>
            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                <span
                    v-if="product.tag"
                    class="rounded-lg bg-shop-primary-600 px-2.5 py-1 text-[10px] font-bold tracking-wider text-white uppercase shadow-md"
                    >{{ product.tag }}</span
                >
                <span
                    v-if="product.oldPrice && product.inStock"
                    class="rounded-lg bg-red-500 px-2.5 py-1 text-[10px] font-bold tracking-wider text-white uppercase shadow-md"
                    >-{{
                        savingsPercent(product.price, product.oldPrice)
                    }}%</span
                >
            </div>

            <div
                class="absolute top-3 right-3 flex flex-col gap-1.5 opacity-0 transition-all duration-300 group-hover:opacity-100"
            >
                <button
                    v-if="product.id"
                    type="button"
                    :aria-label="
                        isWishlisted(product.id!)
                            ? `Remove ${product.name} from wishlist`
                            : `Add ${product.name} to wishlist`
                    "
                    class="inline-flex h-9 w-9 translate-y-1 items-center justify-center rounded-xl bg-white/95 shadow-lg transition-all duration-300 group-hover:translate-y-0 hover:scale-110 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none active:scale-95"
                    :class="
                        isWishlisted(product.id!)
                            ? 'text-red-500'
                            : 'text-gray-400 hover:text-red-500'
                    "
                    @click="handleToggleWishlist"
                >
                    <svg
                        class="h-4 w-4"
                        :fill="
                            isWishlisted(product.id!) ? 'currentColor' : 'none'
                        "
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        :stroke-width="isWishlisted(product.id!) ? '0' : '2'"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                        />
                    </svg>
                </button>
                <button
                    v-if="product.inStock && product.id"
                    type="button"
                    :aria-label="`Quick add ${product.name} to cart`"
                    class="inline-flex h-9 w-9 translate-y-1 items-center justify-center rounded-xl bg-white/95 text-gray-500 shadow-lg transition-all duration-300 group-hover:translate-y-0 hover:scale-110 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none active:scale-95"
                    @click="handleAddToCart"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex flex-1 flex-col p-4">
            <Link :href="productShowUrl(product)" class="block">
                <h3
                    class="line-clamp-2 text-sm font-semibold text-gray-900 transition-colors duration-200 group-hover:text-shop-primary-600 md:text-base"
                >
                    {{ product.name }}
                </h3>
            </Link>
            <div class="mt-2 flex items-center gap-1.5">
                <ShopStarRating :rating="product.rating" size="sm" />
                <span class="text-xs text-gray-400"
                    >({{ product.reviews }})</span
                >
            </div>
            <div class="mt-auto flex items-end justify-between pt-3">
                <div>
                    <span
                        class="text-lg font-bold text-shop-primary-600 md:text-xl"
                        >{{ formatTaka(product.price) }}</span
                    >
                    <span
                        v-if="product.oldPrice"
                        class="ml-1.5 text-xs text-gray-400 line-through"
                        >{{ formatTaka(product.oldPrice) }}</span
                    >
                </div>
                <span
                    v-if="product.inStock"
                    class="rounded-lg bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-700"
                    >In Stock</span
                >
            </div>
            <div class="mt-3 flex gap-2">
                <button
                    type="button"
                    :disabled="!product.inStock"
                    class="flex-1 rounded-xl bg-shop-primary-600 px-3 py-2 text-xs font-semibold text-white transition-all duration-200 hover:bg-shop-primary-700 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50"
                    @click="handleAddToCart"
                >
                    Add to Cart
                </button>
                <button
                    type="button"
                    :disabled="!product.inStock"
                    class="flex-1 rounded-xl bg-shop-dark px-3 py-2 text-xs font-semibold text-white transition-all duration-200 hover:bg-shop-dark-light active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50"
                    @click="handleBuyNow"
                >
                    Buy Now
                </button>
            </div>
        </div>
    </article>
</template>
