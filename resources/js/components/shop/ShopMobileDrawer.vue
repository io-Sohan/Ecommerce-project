<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ShopLogo from '@/components/shop/ShopLogo.vue';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';
import { useShopUi } from '@/composables/shop/useShopUi';
import { home, login, register, logout, dashboard } from '@/routes';
import shop from '@/routes/shop';

const page = usePage();
const { isMobileMenuOpen, closeMobileMenu } = useShopUi();
const { search, setSearch } = useShopCatalog();

const isShopPage = computed(() => page.component === 'shop/Shop');
const isAuthenticated = computed(() => !!page.props.auth?.user);
const isAdmin = computed(
    () => page.props.auth?.user?.role === 'admin',
);

function handleSearchInput(event: Event): void {
    if (!isShopPage.value) {
        return;
    }

    setSearch((event.target as HTMLInputElement).value);
}
</script>

<template>
    <div>
        <div
            class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm lg:hidden"
            :class="isMobileMenuOpen ? 'block' : 'hidden'"
            aria-hidden="true"
            @click="closeMobileMenu"
        />

        <aside
            id="mobileDrawer"
            class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85%] bg-white shadow-2xl transition-transform duration-300 ease-in-out lg:hidden"
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            role="dialog"
            aria-modal="true"
            aria-label="Menu"
        >
            <div
                class="flex h-[4.25rem] items-center justify-between border-b border-gray-100 px-5"
            >
                <ShopLogo />
                <button
                    type="button"
                    aria-label="Close menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none"
                    @click="closeMobileMenu"
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
            <div class="p-5">
                <label for="searchMobile" class="sr-only"
                    >Search products</label
                >
                <div class="relative mb-5">
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
                        id="searchMobile"
                        type="search"
                        :value="isShopPage ? search : ''"
                        placeholder="Search for products…"
                        class="w-full rounded-xl border-0 bg-gray-100 py-2.5 pr-4 pl-10 text-sm ring-1 ring-gray-200 transition focus:bg-white focus:ring-2 focus:ring-shop-primary-500 focus:outline-none"
                        @input="handleSearchInput"
                    />
                </div>
                <nav class="flex flex-col gap-0.5" aria-label="Mobile">
                    <Link
                        :href="home()"
                        class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                        @click="closeMobileMenu"
                    >
                        Home
                    </Link>
                    <Link
                        :href="shop.index()"
                        class="rounded-xl px-4 py-3 text-sm font-semibold transition"
                        :class="
                            isShopPage
                                ? 'bg-shop-primary-50 text-shop-primary-700'
                                : 'text-gray-700 hover:bg-shop-primary-50 hover:text-shop-primary-700'
                        "
                        @click="closeMobileMenu"
                    >
                        Shop
                    </Link>
                    <a
                        href="/#categories"
                        class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                        @click="closeMobileMenu"
                    >
                        Categories
                    </a>
                    <a
                        href="/#bestselling"
                        class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                        @click="closeMobileMenu"
                    >
                        Best Selling
                    </a>
                    <a
                        href="/#newcollection"
                        class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                        @click="closeMobileMenu"
                    >
                        New Collection
                    </a>

                    <div class="my-3 border-t border-gray-100" />

                    <!-- Authenticated links -->
                    <template v-if="isAuthenticated">
                        <Link
                            v-if="isAdmin"
                            :href="dashboard()"
                            class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                            @click="closeMobileMenu"
                        >
                            Admin Dashboard
                        </Link>
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-gray-700 transition hover:bg-red-50 hover:text-red-600"
                            @click="closeMobileMenu"
                        >
                            Log out
                        </Link>
                    </template>
                    <!-- Guest links -->
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-shop-primary-50 hover:text-shop-primary-700"
                            @click="closeMobileMenu"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-xl bg-gradient-to-r from-shop-primary-600 to-purple-600 px-4 py-3 text-center text-sm font-bold text-white shadow-lg shadow-shop-primary-600/25"
                            @click="closeMobileMenu"
                        >
                            Create Account
                        </Link>
                    </template>
                </nav>
            </div>
        </aside>
    </div>
</template>
