<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ShopLogo from '@/components/shop/ShopLogo.vue';
import { useShopCart } from '@/composables/shop/useShopCart';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';
import { useShopUi } from '@/composables/shop/useShopUi';
import { useShopWishlist } from '@/composables/shop/useShopWishlist';
import { home, login, dashboard } from '@/routes';
import shop from '@/routes/shop';

const page = usePage();
const { openMobileMenu } = useShopUi();
const { cartQty } = useShopCart();
const { wishCount } = useShopWishlist();
const { search, setSearch } = useShopCatalog();

const isShopPage = computed(() => page.component === 'shop/Shop');
const isHomePage = computed(() => page.component === 'shop/Home');
const isCartPage = computed(() => page.component === 'shop/Cart');
const isWishlistPage = computed(() => page.component === 'shop/Wishlist');
const isCheckoutPage = computed(() => page.component === 'shop/Checkout');
const isAuthenticated = computed(() => !!page.props.auth?.user);
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const showMobileSearch = ref(false);

function toggleMobileSearch(): void {
    showMobileSearch.value = !showMobileSearch.value;
}

function handleSearchInput(event: Event): void {
    if (!isShopPage.value) {
        return;
    }

    setSearch((event.target as HTMLInputElement).value);
}
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
        <!-- Top accent bar -->
        <div class="h-1 w-full bg-shop-primary-600" />

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-[4.25rem] items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        aria-label="Open menu"
                        aria-expanded="false"
                        aria-controls="mobileDrawer"
                        class="-ml-1 inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-shop-primary-50 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none lg:hidden"
                        @click="openMobileMenu"
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
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>

                    <ShopLogo />
                </div>

                <nav
                    class="hidden items-center gap-0.5 lg:flex"
                    aria-label="Primary"
                >
                    <Link
                        :href="home()"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition duration-200"
                        :class="
                            isHomePage
                                ? 'bg-shop-primary-50 text-shop-primary-700'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        "
                    >
                        Home
                    </Link>
                    <Link
                        :href="shop.index()"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition duration-200"
                        :class="
                            isShopPage
                                ? 'bg-shop-primary-50 text-shop-primary-700'
                                : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-700'
                        "
                    >
                        Shop
                    </Link>
                    <a
                        href="/#categories"
                        class="rounded-xl px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 hover:bg-shop-primary-50 hover:text-shop-primary-700"
                    >
                        Categories
                    </a>
                    <a
                        href="/#bestselling"
                        class="rounded-xl px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 hover:bg-shop-primary-50 hover:text-shop-primary-700"
                    >
                        Best Selling
                    </a>
                </nav>

                <div class="hidden max-w-sm flex-1 md:block">
                    <label for="searchDesktop" class="sr-only"
                        >Search products</label
                    >
                    <div class="relative">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-gray-400"
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
                                    d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                                />
                            </svg>
                        </span>
                        <input
                            id="searchDesktop"
                            type="search"
                            :value="isShopPage ? search : ''"
                            placeholder="Search products…"
                            class="w-full rounded-xl border-0 bg-gray-100 py-2.5 pr-4 pl-10 text-sm text-gray-900 ring-1 ring-gray-200 transition placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-shop-primary-500 focus:outline-none"
                            @input="handleSearchInput"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-0.5">
                    <button
                        type="button"
                        aria-label="Search"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-shop-primary-50 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none md:hidden"
                        @click="toggleMobileSearch"
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
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                            />
                        </svg>
                    </button>
                    <!-- Authenticated: Dashboard (admin) or Settings link -->
                    <Link
                        v-if="isAuthenticated && isAdmin"
                        :href="dashboard()"
                        aria-label="Admin Dashboard"
                        class="hidden h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-shop-primary-50 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none sm:inline-flex"
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </Link>
                    <Link
                        v-else-if="isAuthenticated"
                        :href="home()"
                        aria-label="My Account"
                        class="hidden h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-shop-primary-50 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none sm:inline-flex"
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </Link>
                    <!-- Guest: Login link -->
                    <Link
                        v-else
                        :href="login()"
                        aria-label="Log in"
                        class="hidden h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-shop-primary-50 hover:text-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none sm:inline-flex"
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </Link>
                    <Link
                        :href="shop.wishlist()"
                        :aria-label="`Wishlist, ${wishCount} items`"
                        class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl transition focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                        :class="
                            isWishlistPage
                                ? 'bg-shop-primary-50 text-shop-primary-600'
                                : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-600'
                        "
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
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </svg>
                        <span
                            v-if="wishCount > 0"
                            class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-shop-primary-600 px-1 text-[10px] font-bold text-white shadow-sm"
                        >
                            {{ wishCount }}
                        </span>
                    </Link>
                    <Link
                        :href="shop.cart()"
                        :aria-label="`Cart, ${cartQty} items`"
                        class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl transition focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                        :class="
                            isCartPage || isCheckoutPage
                                ? 'bg-shop-primary-50 text-shop-primary-600'
                                : 'text-gray-600 hover:bg-shop-primary-50 hover:text-shop-primary-600'
                        "
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
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            />
                        </svg>
                        <span
                            v-if="cartQty > 0"
                            class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-shop-primary-600 px-1 text-[10px] font-bold text-white shadow-sm"
                        >
                            {{ cartQty }}
                        </span>
                    </Link>
                </div>
            </div>

            <div v-show="showMobileSearch" class="pb-3 md:hidden">
                <label for="searchMobileHeader" class="sr-only"
                    >Search products</label
                >
                <div class="relative">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-gray-400"
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
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                            />
                        </svg>
                    </span>
                    <input
                        id="searchMobileHeader"
                        type="search"
                        :value="isShopPage ? search : ''"
                        placeholder="Search products…"
                        class="w-full rounded-xl border-0 bg-gray-100 py-2.5 pr-4 pl-10 text-sm ring-1 ring-gray-200 transition focus:bg-white focus:ring-2 focus:ring-shop-primary-500 focus:outline-none"
                        @input="handleSearchInput"
                    />
                </div>
            </div>
        </div>
    </header>
</template>
