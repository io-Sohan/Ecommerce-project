<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useShopCart } from '@/composables/shop/useShopCart';
import { formatTaka } from '@/lib/shop/currency';
import shop from '@/routes/shop';

const {
    cart,
    cartQty,
    cartSubtotal,
    isCartOpen,
    closeCart,
    updateQty,
    removeItem,
} = useShopCart();
</script>

<template>
    <div>
        <div
            class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-sm"
            :class="isCartOpen ? 'block' : 'hidden'"
            @click="closeCart"
        />

        <aside
            class="fixed inset-y-0 right-0 z-[70] flex w-[26rem] max-w-[90%] flex-col bg-white shadow-2xl transition-transform duration-300 ease-in-out"
            :class="isCartOpen ? 'translate-x-0' : 'translate-x-full'"
            role="dialog"
            aria-modal="true"
            aria-label="Shopping cart"
        >
            <div
                class="flex h-[4.25rem] shrink-0 items-center justify-between border-b border-gray-100 px-5"
            >
                <h2 class="text-lg font-extrabold text-gray-900">
                    Your Cart
                    <span class="ml-1 text-sm font-medium text-gray-400">({{ cartQty }})</span>
                </h2>
                <button
                    type="button"
                    aria-label="Close cart"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                    @click="closeCart"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div
                v-if="cart.length === 0"
                class="flex flex-1 flex-col items-center justify-center px-6 text-center"
            >
                <div
                    class="mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-shop-primary-50 text-shop-primary-600"
                >
                    <svg
                        class="h-9 w-9"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                </div>
                <p class="text-base font-bold text-gray-900">Your cart is empty</p>
                <p class="mt-1.5 text-sm text-gray-500">
                    Discover products and add them to your cart.
                </p>
                <button
                    type="button"
                    class="mt-6 rounded-xl bg-shop-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-shop-primary-700 hover:shadow-xl"
                    @click="closeCart"
                >
                    Continue shopping
                </button>
            </div>

            <template v-else>
                <div class="flex-1 overflow-y-auto px-5">
                    <div
                        v-for="item in cart"
                        :key="item.productId"
                        class="flex gap-4 border-b border-gray-100 py-4"
                    >
                        <div
                            class="h-18 w-18 shrink-0 overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200/80"
                        >
                            <img
                                :src="item.img"
                                :alt="item.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div
                                class="flex items-start justify-between gap-2"
                            >
                                <p
                                    class="line-clamp-2 text-sm font-semibold text-gray-900"
                                >
                                    {{ item.name }}
                                </p>
                                <button
                                    type="button"
                                    aria-label="Remove"
                                    class="shrink-0 rounded-lg p-1 text-gray-300 transition hover:bg-red-50 hover:text-red-500"
                                    @click="removeItem(item.productId)"
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
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="mt-2.5 flex items-center justify-between"
                            >
                                <div
                                    class="inline-flex items-center rounded-xl ring-1 ring-gray-200"
                                >
                                    <button
                                        type="button"
                                        aria-label="Decrease"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-l-xl text-gray-500 transition hover:bg-gray-50 disabled:opacity-40"
                                        :disabled="item.qty <= 1"
                                        @click="updateQty(item.productId, item.qty - 1)"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M20 12H4"
                                            />
                                        </svg>
                                    </button>
                                    <span
                                        class="w-9 text-center text-sm font-bold text-gray-900"
                                        >{{ item.qty }}</span
                                    >
                                    <button
                                        type="button"
                                        aria-label="Increase"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-r-xl text-gray-500 transition hover:bg-gray-50"
                                        @click="updateQty(item.productId, item.qty + 1)"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <span
                                    class="text-sm font-bold text-gray-900"
                                >
                                    {{ formatTaka(item.price * item.qty) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-100 p-5">
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Subtotal</span>
                        <span class="text-xl font-extrabold text-gray-900">{{
                            formatTaka(cartSubtotal)
                        }}</span>
                    </div>
                    <p class="mb-4 text-xs text-gray-400">
                        Delivery calculated at checkout.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <Link
                            :href="shop.cart()"
                            class="rounded-xl bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50"
                            @click="closeCart"
                            >View Cart</Link
                        >
                        <Link
                            :href="shop.checkout()"
                            class="rounded-xl bg-shop-primary-600 px-4 py-2.5 text-center text-sm font-bold text-white shadow-lg transition hover:bg-shop-primary-700 hover:shadow-xl"
                            @click="closeCart"
                            >Checkout</Link
                        >
                    </div>
                </div>
            </template>
        </aside>
    </div>
</template>
