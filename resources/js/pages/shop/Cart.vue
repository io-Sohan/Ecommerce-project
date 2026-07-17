<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ShopCartLineItem from '@/components/shop/ShopCartLineItem.vue';
import ShopCartSummary from '@/components/shop/ShopCartSummary.vue';
import ShopPageBreadcrumb from '@/components/shop/ShopPageBreadcrumb.vue';
import { useShopCart } from '@/composables/shop/useShopCart';
import { useShopUi } from '@/composables/shop/useShopUi';
import shop from '@/routes/shop';

const {
    cart,
    cartQty,
    cartSubtotal,
    updateQty,
    removeItem,
    clearCart,
} = useShopCart();
const { showToast } = useShopUi();

function handleIncrement(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);

    if (item) {
        updateQty(productId, item.qty + 1);
    }
}

function handleDecrement(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);

    if (item && item.qty > 1) {
        updateQty(productId, item.qty - 1);
    }
}

function handleRemove(productId: number): void {
    const item = cart.value.find((i) => i.productId === productId);
    const name = item?.name ?? 'item';
    removeItem(productId);
    showToast(`Removed: ${name}`);
}

function handleClearCart(): void {
    clearCart();
    showToast('Cart cleared');
}
</script>

<template>
    <Head title="Shopping Cart">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="bg-gray-50/50 py-8 md:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <ShopPageBreadcrumb :items="[{ label: 'Cart' }]" />

            <h1 class="mb-8 text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
                Shopping Cart
            </h1>

            <div
                v-if="cart.length > 0"
                class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8"
            >
                <div class="lg:col-span-2">
                    <div
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/80"
                    >
                        <div
                            class="hidden grid-cols-12 gap-4 border-b border-gray-100 px-5 py-3.5 text-[11px] font-bold tracking-widest text-gray-400 uppercase sm:grid"
                        >
                            <span class="col-span-6">Product</span>
                            <span class="col-span-2 text-center">Price</span>
                            <span class="col-span-2 text-center"
                                >Quantity</span
                            >
                            <span class="col-span-2 text-right">Total</span>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <ShopCartLineItem
                                v-for="item in cart"
                                :key="item.productId"
                                :item="item"
                                @increment="handleIncrement"
                                @decrement="handleDecrement"
                                @remove="handleRemove"
                            />
                        </ul>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <Link
                            :href="shop.index()"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-shop-primary-600 hover:text-shop-primary-700"
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
                                    d="M19 12H5M11 18l-6-6 6-6"
                                />
                            </svg>
                            Continue shopping
                        </Link>
                        <button
                            type="button"
                            class="rounded-xl px-3 py-1.5 text-sm font-semibold text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                            @click="handleClearCart"
                        >
                            Clear cart
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <ShopCartSummary
                        :item-count="cartQty"
                        :subtotal="cartSubtotal"
                    />
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200/80"
            >
                <div
                    class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-shop-primary-50 to-purple-50 text-shop-primary-400"
                >
                    <svg
                        class="h-10 w-10"
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
                <h2 class="text-lg font-bold text-gray-900">
                    Your cart is empty
                </h2>
                <p class="mt-1.5 text-sm text-gray-500">
                    Looks like you haven't added anything yet.
                </p>
                <Link
                    :href="shop.index()"
                    class="mt-6 inline-block rounded-xl bg-gradient-to-r from-shop-primary-600 to-shop-primary-800 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-shop-primary-600/25 transition hover:shadow-xl"
                >
                    Start Shopping
                </Link>
            </div>
        </div>
    </div>
</template>
