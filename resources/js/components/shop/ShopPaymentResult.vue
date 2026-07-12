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
            gradient: 'from-green-500 to-emerald-600',
            title: 'Payment successful!',
            description:
                'Your payment has been confirmed and your order is being processed. A confirmation email is on its way.',
            iconPath: 'M5 13l4 4L19 7',
        };
    }

    if (status === 'failed') {
        return {
            gradient: 'from-red-500 to-rose-600',
            title: 'Payment failed',
            description:
                'We could not process your payment. No charges were made. You can try again or choose a different payment method.',
            iconPath: 'M6 18L18 6M6 6l12 12',
        };
    }

    return {
        gradient: 'from-amber-500 to-orange-500',
        title: 'Payment cancelled',
        description:
            'You cancelled the payment on the gateway page. Your order is saved but not paid yet.',
        iconPath:
            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    };
});

const hasInvoiceDetails = computed(
    () => order && order.items && order.items.length > 0 && status === 'success',
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
            class="overflow-hidden rounded-t-3xl px-8 py-10 text-center md:px-12 print:rounded-none print:py-6"
            :class="`bg-gradient-to-r ${content.gradient}`"
        >
            <div
                class="mx-auto mb-4 flex h-18 w-18 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm print:hidden"
            >
                <svg
                    class="h-10 w-10 text-white"
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
                class="text-2xl font-extrabold text-white md:text-3xl print:text-xl"
            >
                {{ content.title }}
            </h1>
            <p class="mt-2 text-sm text-white/80 md:text-base print:text-xs">
                {{ content.description }}
            </p>
        </div>

        <!-- ─── Invoice Body ───────────────────────────────────────────────── -->
        <div
            class="rounded-b-3xl bg-white shadow-xl ring-1 ring-gray-200/80 print:rounded-none print:shadow-none print:ring-0"
        >
            <!-- Invoice Header (only for success with full details) -->
            <div
                v-if="hasInvoiceDetails && order"
                class="flex flex-col gap-4 border-b border-gray-100 px-8 py-6 sm:flex-row sm:items-center sm:justify-between md:px-12 print:px-6 print:py-4"
            >
                <div>
                    <div class="flex items-center gap-2.5">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-shop-primary-600 to-purple-600 text-white shadow print:h-7 print:w-7"
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
                class="grid grid-cols-1 gap-0 border-b border-gray-100 sm:grid-cols-2 print:grid-cols-2"
            >
                <div
                    class="border-b border-gray-100 px-8 py-5 sm:border-b-0 sm:border-r md:px-12 print:px-6 print:py-3"
                >
                    <p
                        class="mb-2 text-[10px] font-bold tracking-widest text-indigo-600 uppercase print:text-[9px]"
                    >
                        Bill To
                    </p>
                    <p class="text-sm font-semibold text-gray-900 print:text-xs">
                        {{ order.customerName }}
                    </p>
                    <p class="mt-1 text-xs text-gray-600">{{ order.phone }}</p>
                    <p class="text-xs text-gray-600">{{ order.email }}</p>
                </div>
                <div class="px-8 py-5 md:px-12 print:px-6 print:py-3">
                    <p
                        class="mb-2 text-[10px] font-bold tracking-widest text-orange-600 uppercase print:text-[9px]"
                    >
                        Ship To
                    </p>
                    <p class="text-sm font-semibold text-gray-900 print:text-xs">
                        {{ order.address }}
                    </p>
                    <p class="mt-1 text-xs text-gray-600">
                        {{ order.area }}, {{ order.district }}
                    </p>
                    <p
                        v-if="order.notes"
                        class="mt-1 text-xs text-amber-600 italic"
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
                    class="border-b border-r border-gray-100 px-6 py-4 sm:border-b-0 print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Payment
                    </p>
                    <p
                        class="mt-1 text-sm font-semibold text-gray-900 print:text-xs"
                    >
                        {{ order.paymentLabel }}
                    </p>
                </div>
                <div
                    class="border-b border-gray-100 px-6 py-4 sm:border-b-0 sm:border-r print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Payment Status
                    </p>
                    <p class="mt-1">
                        <span
                            class="inline-block rounded-full px-2 py-0.5 text-[11px] font-bold capitalize"
                            :class="
                                order.paymentStatus === 'paid'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-amber-100 text-amber-700'
                            "
                        >
                            {{ order.paymentStatus ?? 'pending' }}
                        </span>
                    </p>
                </div>
                <div
                    class="border-r border-gray-100 px-6 py-4 print:px-4 print:py-2"
                >
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Order Status
                    </p>
                    <p class="mt-1">
                        <span
                            class="inline-block rounded-full bg-indigo-100 px-2 py-0.5 text-[11px] font-bold capitalize text-indigo-700"
                        >
                            {{ order.status ?? 'pending' }}
                        </span>
                    </p>
                </div>
                <div class="px-6 py-4 print:px-4 print:py-2">
                    <p
                        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
                    >
                        Items
                    </p>
                    <p
                        class="mt-1 text-sm font-semibold text-gray-900 print:text-xs"
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
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white print:from-gray-700 print:to-gray-700"
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
                                    'border-b border-gray-100 last:border-b-0',
                                    idx % 2 === 1
                                        ? 'bg-indigo-50/40'
                                        : 'bg-white',
                                ]"
                            >
                                <td
                                    class="px-4 py-3 text-gray-400 print:px-3 print:py-2"
                                >
                                    {{ idx + 1 }}
                                </td>
                                <td
                                    class="px-4 py-3 font-medium text-gray-900 print:px-3 print:py-2"
                                >
                                    {{ item.productName }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center print:px-3 print:py-2"
                                >
                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-[11px] font-bold text-indigo-700 print:bg-gray-200 print:text-gray-700"
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
                <div
                    class="ml-auto max-w-xs space-y-2 print:max-w-[200px]"
                >
                    <div
                        class="flex items-center justify-between text-sm text-gray-600 print:text-xs"
                    >
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900">
                            {{ formatTaka(order.subtotal ?? 0) }}
                        </span>
                    </div>
                    <div
                        class="flex items-center justify-between text-sm text-gray-600 print:text-xs"
                    >
                        <span>Delivery Charge</span>
                        <span class="font-medium text-gray-900">
                            {{ formatTaka(order.deliveryCharge ?? 0) }}
                        </span>
                    </div>
                    <div
                        class="mt-2 flex items-center justify-between rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-3 text-white print:rounded-none print:from-gray-800 print:to-gray-800 print:px-3 print:py-2"
                    >
                        <span class="text-sm font-bold print:text-xs">
                            Grand Total
                        </span>
                        <span class="text-lg font-extrabold print:text-sm">
                            {{ formatTaka(order.total) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Simple summary (failed/cancelled or missing details) -->
            <div v-else class="px-8 py-8 md:px-12">
                <div
                    v-if="order"
                    class="grid grid-cols-1 gap-4 rounded-2xl bg-gray-50 p-5 text-sm sm:grid-cols-3"
                >
                    <div>
                        <p
                            class="text-xs font-medium text-gray-400 uppercase"
                        >
                            Order number
                        </p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ order.orderNumber }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs font-medium text-gray-400 uppercase"
                        >
                            Total
                        </p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ formatTaka(order.total) }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs font-medium text-gray-400 uppercase"
                        >
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
                    Order details are unavailable. If you completed a
                    payment, check your email for confirmation.
                </div>
            </div>

            <!-- Footer (success with details) -->
            <div
                v-if="hasInvoiceDetails"
                class="border-t border-gray-100 bg-gray-50 px-8 py-5 text-center md:px-12 print:bg-gray-100 print:px-6 print:py-3"
            >
                <p class="text-xs text-gray-500 print:text-[10px]">
                    Thank you for shopping with
                    <span class="font-bold text-gray-700">ShopEase</span>!
                    🎉
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
                class="flex flex-col gap-3 border-t border-gray-100 px-8 py-6 sm:flex-row sm:justify-center md:px-12 print:hidden"
            >
                <Link
                    v-if="status !== 'success'"
                    :href="shop.checkout.url()"
                    class="rounded-xl bg-gradient-to-r from-shop-primary-600 to-purple-600 px-6 py-2.5 text-center text-sm font-bold text-white shadow-lg shadow-shop-primary-600/25 transition hover:shadow-xl"
                >
                    {{
                        status === 'cancelled'
                            ? 'Try Payment Again'
                            : 'Back to Checkout'
                    }}
                </Link>
                <Link
                    :href="shop.index.url()"
                    class="rounded-xl px-6 py-2.5 text-center text-sm font-semibold transition"
                    :class="
                        status === 'success'
                            ? 'bg-gradient-to-r from-shop-primary-600 to-purple-600 text-white shadow-lg shadow-shop-primary-600/25 hover:shadow-xl'
                            : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50'
                    "
                >
                    Continue Shopping
                </Link>
                <button
                    v-if="status === 'success' && hasInvoiceDetails"
                    type="button"
                    class="rounded-xl bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50"
                    @click="printInvoice"
                >
                    Print Invoice
                </button>
            </div>
        </div>
    </div>
</template>
