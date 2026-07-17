<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatTaka } from '@/lib/shop/currency';
import shop from '@/routes/shop';
import type { ShopPlacedOrder } from '@/types/shop';

const { status, order } = defineProps<{
    status: 'success' | 'failed' | 'cancelled';
    order: ShopPlacedOrder | null;
}>();

const content = computed(() => {
    if (status === 'success') {
        return {
            gradient: 'from-emerald-950 via-emerald-900 to-teal-800',
            title: 'Payment successful!',
            description:
                'Your payment has been confirmed and your order is being processed. A confirmation email is on its way.',
            iconPath: 'M5 13l4 4L19 7',
        };
    }

    if (status === 'failed') {
        return {
            gradient: 'from-rose-950 via-rose-900 to-red-800',
            title: 'Payment failed',
            description:
                'We could not process your payment. No charges were made. You can try again or choose a different payment method.',
            iconPath: 'M6 18L18 6M6 6l12 12',
        };
    }

    return {
        gradient: 'from-amber-950 via-amber-900 to-orange-800',
        title: 'Payment cancelled',
        description:
            'You cancelled the payment on the gateway page. Your order is saved but not paid yet.',
        iconPath:
            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    };
});

const hasInvoiceDetails = computed(
    () =>
        order && order.items && order.items.length > 0 && status === 'success',
);

const formattedDate = computed(() => {
    if (!order?.placedAt) {
        return '—';
    }

    return new Date(order.placedAt).toLocaleString('en-BD', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const totalQuantity = computed(() => {
    if (!order?.items) {
        return 0;
    }

    return order.items.reduce((sum, item) => sum + item.quantity, 0);
});

function printInvoice(): void {
    window.print();
}
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <!-- ─── Status Banner ──────────────────────────────────────────────── -->
        <div
            class="relative overflow-hidden rounded-t-3xl px-8 py-12 text-center md:px-12 print:rounded-none print:bg-none print:py-6 print:text-black"
            :class="`bg-gradient-to-br ${content.gradient}`"
        >
            <!-- Decorative backdrop elements -->
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,var(--tw-gradient-from),transparent_60%)] opacity-20"
            ></div>
            <div
                class="absolute -top-24 -left-20 h-48 w-48 rounded-full bg-white/5 blur-3xl"
            ></div>

            <div class="relative z-10">
                <div
                    class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 shadow-inner ring-1 ring-white/20 backdrop-blur-md transition-transform duration-500 hover:scale-105 print:hidden"
                >
                    <svg
                        class="h-11 w-11 animate-pulse text-white drop-shadow-md"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            :d="content.iconPath"
                        />
                    </svg>
                </div>
                <h1
                    class="text-3xl font-extrabold tracking-tight text-white md:text-4xl print:text-xl print:text-black"
                >
                    {{ content.title }}
                </h1>
                <p
                    class="mx-auto mt-3 max-w-md text-sm text-white/80 md:text-base print:text-xs print:text-gray-600"
                >
                    {{ content.description }}
                </p>
            </div>
        </div>

        <!-- ─── Invoice Body ───────────────────────────────────────────────── -->
        <div
            class="rounded-b-3xl border border-t-0 border-gray-100/50 bg-white shadow-xl ring-1 ring-gray-200/80 print:rounded-none print:border-0 print:shadow-none print:ring-0"
        >
            <!-- Invoice Header (only for success with full details) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="flex flex-col gap-4 border-b border-gray-100 px-8 py-6 sm:flex-row sm:items-center sm:justify-between md:px-12 print:px-6 print:py-4"
            >
                <div>
                    <div class="flex items-center gap-2.5">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-shop-primary-600 to-shop-primary-800 text-white shadow print:h-7 print:w-7"
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
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                        </span>
                        <span
                            class="text-lg font-extrabold tracking-tight text-gray-900 print:text-base"
                        >
                            Shop<span class="shop-gradient-text">Ease</span>
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400 print:text-[10px]">
                        Your trusted online store
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <p
                        class="text-xs font-bold tracking-widest text-gray-400 uppercase"
                    >
                        Invoice
                    </p>
                    <p
                        class="mt-0.5 text-lg font-extrabold text-gray-900 print:text-base"
                    >
                        {{ order.orderNumber }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-500">
                        {{ formattedDate }}
                    </p>
                </div>
            </div>

            <!-- Customer & Shipping Details (success with full data) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="grid grid-cols-1 gap-0 border-b border-gray-100/80 bg-gray-50/30 sm:grid-cols-2 print:grid-cols-2 print:bg-none"
            >
                <div
                    class="border-b border-gray-100 px-8 py-6 sm:border-r sm:border-b-0 sm:border-gray-100/80 md:px-12 print:px-6 print:py-3"
                >
                    <p
                        class="mb-2 text-[10px] font-bold tracking-widest text-shop-primary-600 uppercase print:text-[9px]"
                    >
                        Bill To
                    </p>
                    <p class="text-sm font-bold text-gray-900 print:text-xs">
                        {{ order.customerName }}
                    </p>
                    <p class="mt-1 text-xs text-gray-600">{{ order.phone }}</p>
                    <p class="text-xs text-gray-600">{{ order.email }}</p>
                </div>
                <div class="px-8 py-6 md:px-12 print:px-6 print:py-3">
                    <p
                        class="mb-2 text-[10px] font-bold tracking-widest text-shop-accent-600 uppercase print:text-[9px]"
                    >
                        Ship To
                    </p>
                    <p class="text-sm font-bold text-gray-900 print:text-xs">
                        {{ order.address }}
                    </p>
                    <p class="mt-1 text-xs text-gray-600">
                        {{ order.area }}, {{ order.district }}
                    </p>
                    <p
                        v-if="order.notes"
                        class="mt-2 rounded-lg border border-amber-100/50 bg-amber-50/60 px-3 py-1.5 text-xs text-amber-800 italic print:border-none print:bg-none print:p-0 print:text-amber-700"
                    >
                        Note: {{ order.notes }}
                    </p>
                </div>
            </div>

            <!-- Meta Chips (success with full data) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="grid grid-cols-2 gap-0 border-b border-gray-100 sm:grid-cols-4 print:grid-cols-4"
            >
                <div
                    class="border-r border-b border-gray-100/80 px-6 py-5 sm:border-b-0 print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Payment
                    </p>
                    <p
                        class="mt-1.5 text-sm font-bold text-gray-900 print:text-xs"
                    >
                        {{ order.paymentLabel }}
                    </p>
                </div>
                <div
                    class="border-b border-gray-100/80 px-6 py-5 sm:border-r sm:border-b-0 sm:border-gray-100/80 print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Payment Status
                    </p>
                    <p class="mt-1.5">
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold capitalize ring-1"
                            :class="
                                order.paymentStatus === 'paid'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                    : 'bg-amber-50 text-amber-700 ring-amber-600/20'
                            "
                        >
                            <span
                                class="mr-1 h-1.5 w-1.5 rounded-full"
                                :class="
                                    order.paymentStatus === 'paid'
                                        ? 'bg-emerald-500'
                                        : 'bg-amber-500'
                                "
                            ></span>
                            {{ order.paymentStatus ?? 'pending' }}
                        </span>
                    </p>
                </div>
                <div
                    class="border-r border-gray-100/80 px-6 py-5 print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Order Status
                    </p>
                    <p class="mt-1.5">
                        <span
                            class="inline-flex items-center rounded-full bg-shop-primary-50 px-2.5 py-0.5 text-[11px] font-bold text-shop-primary-700 capitalize ring-1 ring-shop-primary-600/10"
                        >
                            <span
                                class="mr-1 h-1.5 w-1.5 animate-pulse rounded-full bg-shop-primary-500"
                            ></span>
                            {{ order.status ?? 'pending' }}
                        </span>
                    </p>
                </div>
                <div class="px-6 py-5 print:px-4 print:py-2">
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Items
                    </p>
                    <p
                        class="mt-1.5 text-sm font-bold text-gray-900 print:text-xs"
                    >
                        {{ totalQuantity }}
                        {{ totalQuantity === 1 ? 'item' : 'items' }}
                    </p>
                </div>
            </div>

            <!-- Line Items Table (success with full data) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="px-8 pt-6 md:px-12 print:px-6 print:pt-4"
            >
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 print:rounded-none print:border-gray-300"
                >
                    <table class="w-full text-sm print:text-xs">
                        <thead>
                            <tr
                                class="bg-gradient-to-r from-shop-primary-600 to-shop-primary-800 text-white print:from-gray-700 print:to-gray-700"
                            >
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-bold tracking-wide uppercase print:px-3 print:py-2 print:text-[9px]"
                                >
                                    #
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-[11px] font-bold tracking-wide uppercase print:px-3 print:py-2 print:text-[9px]"
                                >
                                    Product
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-[11px] font-bold tracking-wide uppercase print:px-3 print:py-2 print:text-[9px]"
                                >
                                    Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-[11px] font-bold tracking-wide uppercase print:px-3 print:py-2 print:text-[9px]"
                                >
                                    Unit Price
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-[11px] font-bold tracking-wide uppercase print:px-3 print:py-2 print:text-[9px]"
                                >
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, idx) in order.items"
                                :key="item.id"
                                :class="[
                                    'border-b border-gray-100 transition-colors last:border-b-0 hover:bg-gray-50/50',
                                    idx % 2 === 1
                                        ? 'bg-shop-primary-50/15'
                                        : 'bg-white',
                                ]"
                            >
                                <td
                                    class="px-4 py-3 font-medium text-gray-400 print:px-3 print:py-2"
                                >
                                    {{ idx + 1 }}
                                </td>
                                <td
                                    class="px-4 py-3 font-semibold text-gray-900 print:px-3 print:py-2"
                                >
                                    {{ item.productName }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center print:px-3 print:py-2"
                                >
                                    <span
                                        class="inline-flex h-6 w-8 items-center justify-center rounded-lg bg-shop-primary-50 text-[11px] font-bold text-shop-primary-700 ring-1 ring-shop-primary-600/10 print:bg-gray-100 print:text-gray-700 print:ring-0"
                                    >
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-gray-600 print:px-3 print:py-2"
                                >
                                    {{ formatTaka(item.unitPrice) }}
                                </td>
                                <td
                                    class="px-4 py-3 text-right font-semibold text-gray-900 print:px-3 print:py-2"
                                >
                                    {{ formatTaka(item.lineTotal) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals (success with full data) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="px-8 py-6 md:px-12 print:px-6 print:py-4"
            >
                <div class="ml-auto max-w-xs space-y-2.5 print:max-w-[200px]">
                    <div
                        class="flex items-center justify-between text-sm text-gray-600 print:text-xs"
                    >
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900">
                            {{ formatTaka(order.subtotal ?? 0) }}
                        </span>
                    </div>
                    <div
                        class="flex items-center justify-between text-sm text-gray-600 print:text-xs"
                    >
                        <span>Delivery Charge</span>
                        <span class="font-semibold text-gray-900">
                            {{ formatTaka(order.deliveryCharge ?? 0) }}
                        </span>
                    </div>
                    <div
                        class="from-shop-primary-950 via-shop-primary-900 shadow-shop-primary-900/10 mt-3 flex items-center justify-between rounded-xl bg-gradient-to-r to-shop-primary-800 px-4 py-3.5 text-white shadow-md print:rounded-none print:from-gray-800 print:to-gray-800 print:px-3 print:py-2 print:shadow-none"
                    >
                        <span
                            class="text-sm font-extrabold tracking-wide print:text-xs"
                        >
                            Grand Total
                        </span>
                        <span
                            class="text-xl font-black tracking-tight text-white print:text-sm"
                        >
                            {{ formatTaka(order.total) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Simple summary (failed/cancelled or missing details) -->
            <div v-else class="px-8 py-8 md:px-12">
                <div
                    v-if="order"
                    class="animate-fade-in grid grid-cols-1 gap-4 rounded-2xl bg-gray-50 p-5 text-sm sm:grid-cols-3"
                >
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase">
                            Order number
                        </p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ order.orderNumber }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase">
                            Total
                        </p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ formatTaka(order.total) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase">
                            Payment
                        </p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ order.paymentLabel }}
                        </p>
                    </div>
                </div>

                <div
                    v-else
                    class="rounded-2xl bg-gray-50 p-5 text-center text-sm text-gray-600"
                >
                    Order details are unavailable. If you completed a payment,
                    check your email for confirmation.
                </div>
            </div>

            <!-- Footer (success with details) -->
            <div
                v-if="hasInvoiceDetails"
                class="border-t border-gray-100 bg-gray-50 px-8 py-5 text-center md:px-12 print:bg-gray-100 print:px-6 print:py-3"
            >
                <p class="text-xs text-gray-500 print:text-[10px]">
                    Thank you for shopping with
                    <span class="font-bold text-gray-700">ShopEase</span>! 🎉
                </p>
                <p class="mt-1 text-[11px] text-gray-400 print:text-[9px]">
                    Questions? Email us at
                    <span class="font-semibold text-gray-600">
                        support@shopease.com
                    </span>
                </p>
            </div>

            <!-- Action Buttons -->
            <div
                class="flex flex-col gap-3 border-t border-gray-100/80 bg-gray-50/50 px-8 py-6 sm:flex-row sm:justify-center md:px-12 print:hidden"
            >
                <Link
                    v-if="status !== 'success'"
                    :href="shop.checkout.url()"
                    class="transform rounded-xl bg-gradient-to-r from-shop-primary-600 to-shop-primary-700 px-6 py-3 text-center text-sm font-bold text-white shadow-md shadow-shop-primary-600/25 transition-all duration-300 hover:-translate-y-0.5 hover:from-shop-primary-700 hover:to-shop-primary-800 hover:shadow-lg hover:shadow-shop-primary-600/30"
                >
                    {{
                        status === 'cancelled'
                            ? 'Try Payment Again'
                            : 'Back to Checkout'
                    }}
                </Link>
                <Link
                    :href="shop.index.url()"
                    class="transform rounded-xl px-6 py-3 text-center text-sm font-bold transition-all duration-300 hover:-translate-y-0.5"
                    :class="
                        status === 'success'
                            ? 'bg-gradient-to-r from-shop-primary-600 to-shop-primary-700 text-white shadow-md shadow-shop-primary-600/25 hover:from-shop-primary-700 hover:to-shop-primary-800 hover:shadow-lg hover:shadow-shop-primary-600/30'
                            : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 hover:shadow-sm'
                    "
                >
                    Continue Shopping
                </Link>
                <button
                    v-if="status === 'success' && hasInvoiceDetails"
                    type="button"
                    class="transform rounded-xl bg-white px-6 py-3 text-sm font-bold text-gray-700 ring-1 ring-gray-200 transition-all duration-300 hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-sm"
                    @click="printInvoice"
                >
                    Print Invoice
                </button>
            </div>
        </div>
    </div>
</template>
