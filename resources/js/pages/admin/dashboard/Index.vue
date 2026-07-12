<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Eye,
    Heart,
    Package,
    ShoppingCart,
    TrendingUp,
    Users,
} from '@lucide/vue';
import DashboardRevenueChart from '@/components/admin/DashboardRevenueChart.vue';
import DashboardStatCard from '@/components/admin/DashboardStatCard.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatTaka } from '@/lib/shop/currency';
import { dashboard } from '@/routes';
import { index as ordersIndex, show as orderShow } from '@/routes/admin/orders';
import { index as productsIndex } from '@/routes/admin/products';
import type {
    AdminDashboardOverview,
    AdminDashboardPaymentMethodBreakdown,
    AdminDashboardRecentOrder,
    AdminDashboardRevenuePoint,
    AdminDashboardStatusBreakdown,
    AdminDashboardTopCategory,
    AdminDashboardTopProduct,
} from '@/types/admin';

defineProps<{
    overview: AdminDashboardOverview;
    revenue_chart: AdminDashboardRevenuePoint[];
    orders_by_status: AdminDashboardStatusBreakdown[];
    payment_status_breakdown: AdminDashboardStatusBreakdown[];
    payment_method_breakdown: AdminDashboardPaymentMethodBreakdown[];
    top_products: AdminDashboardTopProduct[];
    top_categories: AdminDashboardTopCategory[];
    recent_orders: AdminDashboardRecentOrder[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

function orderStatusVariant(
    status: AdminDashboardRecentOrder['status'],
): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (status) {
        case 'delivered':
            return 'default';
        case 'cancelled':
            return 'destructive';
        case 'pending':
            return 'secondary';
        default:
            return 'outline';
    }
}

function paymentStatusVariant(
    status: AdminDashboardRecentOrder['payment_status'],
): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (status) {
        case 'paid':
            return 'default';
        case 'failed':
        case 'cancelled':
            return 'destructive';
        default:
            return 'secondary';
    }
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString('en-BD', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}

function breakdownPercent(count: number, total: number): number {
    if (total <= 0) {
        return 0;
    }

    return Math.round((count / total) * 100);
}
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6"
    >
        <Heading
            title="Dashboard"
            description="Store performance and operational overview"
        />

        <!-- Stat Cards -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <DashboardStatCard
                title="Total Revenue"
                :value="formatTaka(overview.total_revenue)"
                :change-percent="overview.revenue_change_percent"
                :icon="TrendingUp"
                icon-class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
            />
            <DashboardStatCard
                title="Total Orders"
                :value="overview.total_orders.toLocaleString()"
                :change-percent="overview.orders_change_percent"
                :icon="ShoppingCart"
                icon-class="bg-blue-500/10 text-blue-600 dark:text-blue-400"
            />
            <DashboardStatCard
                title="Avg. Order Value"
                :value="formatTaka(overview.average_order_value)"
                description="Based on paid orders"
                :icon="TrendingUp"
                icon-class="bg-violet-500/10 text-violet-600 dark:text-violet-400"
            />
            <DashboardStatCard
                title="Customers"
                :value="overview.total_customers.toLocaleString()"
                :description="`${overview.new_customers_this_month} new this month`"
                :icon="Users"
                icon-class="bg-amber-500/10 text-amber-600 dark:text-amber-400"
            />
        </div>

        <!-- Revenue Chart + Quick Stats -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <DashboardRevenueChart :data="revenue_chart" />
            </div>

            <div
                class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm"
            >
                <div class="border-b border-border/40 px-6 py-5">
                    <h3 class="text-sm font-semibold text-foreground">
                        Quick Stats
                    </h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        Operational snapshot
                    </p>
                </div>
                <div class="grid gap-0 divide-y divide-border/40">
                    <div class="flex items-center justify-between gap-3 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400"
                            >
                                <ShoppingCart class="size-4" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Pending orders</p>
                                <p class="text-xs text-muted-foreground">
                                    Awaiting fulfillment
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">{{
                            overview.pending_orders
                        }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
                            >
                                <Package class="size-4" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Active products</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ overview.total_products }} total listed
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">{{
                            overview.active_products
                        }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-red-500/10 text-red-600 dark:text-red-400"
                            >
                                <AlertTriangle class="size-4" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Out of stock</p>
                                <p class="text-xs text-muted-foreground">
                                    Products needing restock
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">{{
                            overview.out_of_stock_products
                        }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-pink-500/10 text-pink-600 dark:text-pink-400"
                            >
                                <Heart class="size-4" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Wishlist items</p>
                                <p class="text-xs text-muted-foreground">
                                    Customer demand signals
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">{{
                            overview.total_wishlists
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Cards -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                <div class="border-b border-border/40 px-6 py-5">
                    <h3 class="text-sm font-semibold text-foreground">Orders by Status</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">Fulfillment pipeline</p>
                </div>
                <div class="grid gap-3 px-6 py-5">
                    <div
                        v-for="item in orders_by_status"
                        :key="item.status"
                        class="grid gap-1.5"
                    >
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ item.label }}</span>
                            <span class="font-medium">{{ item.count }}</span>
                        </div>
                        <div
                            class="h-1.5 overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full rounded-full bg-indigo-500 transition-all"
                                :style="{
                                    width: `${breakdownPercent(item.count, overview.total_orders)}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                <div class="border-b border-border/40 px-6 py-5">
                    <h3 class="text-sm font-semibold text-foreground">Payment Status</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">Collection health</p>
                </div>
                <div class="grid gap-3 px-6 py-5">
                    <div
                        v-for="item in payment_status_breakdown"
                        :key="item.status"
                        class="grid gap-1.5"
                    >
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ item.label }}</span>
                            <span class="font-medium">{{ item.count }}</span>
                        </div>
                        <div
                            class="h-1.5 overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full rounded-full bg-emerald-500 transition-all"
                                :style="{
                                    width: `${breakdownPercent(item.count, overview.total_orders)}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                <div class="border-b border-border/40 px-6 py-5">
                    <h3 class="text-sm font-semibold text-foreground">Payment Methods</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">How customers pay</p>
                </div>
                <div class="grid gap-3 px-6 py-5">
                    <div
                        v-for="item in payment_method_breakdown"
                        :key="item.method"
                        class="grid gap-1.5"
                    >
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ item.label }}</span>
                            <span class="font-medium">{{ item.count }}</span>
                        </div>
                        <div
                            class="h-1.5 overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full rounded-full bg-violet-500 transition-all"
                                :style="{
                                    width: `${breakdownPercent(item.count, overview.total_orders)}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders + Top Products -->
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                <div
                    class="flex items-center justify-between gap-4 border-b border-border/40 px-6 py-5"
                >
                    <div>
                        <h3 class="text-sm font-semibold text-foreground">Recent Orders</h3>
                        <p class="mt-0.5 text-xs text-muted-foreground">Latest customer activity</p>
                    </div>
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="ordersIndex()">View all</Link>
                    </Button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30 text-left text-muted-foreground">
                                <th class="px-6 py-3 text-xs font-medium uppercase tracking-wide">Order</th>
                                <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide">Customer</th>
                                <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide">Total</th>
                                <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide">Placed</th>
                                <th class="px-4 py-3">
                                    <span class="sr-only">View</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="order in recent_orders"
                                :key="order.id"
                                class="border-b border-border/30 last:border-0 transition-colors hover:bg-muted/20"
                            >
                                <td class="px-6 py-3.5 font-medium">
                                    {{ order.order_number }}
                                </td>
                                <td class="px-4 py-3.5">
                                    {{ order.customer_name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    {{ formatTaka(order.total) }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        <Badge
                                            :variant="
                                                orderStatusVariant(order.status)
                                            "
                                        >
                                            {{ order.status }}
                                        </Badge>
                                        <Badge
                                            :variant="
                                                paymentStatusVariant(
                                                    order.payment_status,
                                                )
                                            "
                                        >
                                            {{ order.payment_status }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-muted-foreground">
                                    {{ formatDate(order.placed_at) }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        as-child
                                    >
                                        <Link :href="orderShow(order.id)">
                                            <Eye class="size-4" />
                                            <span class="sr-only">View order</span>
                                        </Link>
                                    </Button>
                                </td>
                            </tr>
                            <tr v-if="recent_orders.length === 0">
                                <td
                                    colspan="6"
                                    class="py-8 text-center text-muted-foreground"
                                >
                                    No orders yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-6">
                <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                    <div
                        class="flex items-center justify-between gap-4 border-b border-border/40 px-6 py-5"
                    >
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Top Products</h3>
                            <p class="mt-0.5 text-xs text-muted-foreground">By units sold</p>
                        </div>
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="productsIndex()">View all</Link>
                        </Button>
                    </div>
                    <div class="grid gap-0 divide-y divide-border/30 px-6">
                        <div
                            v-for="(product, index) in top_products"
                            :key="product.id"
                            class="flex items-center justify-between gap-3 py-4"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/10 text-xs font-bold text-indigo-600 dark:text-indigo-400"
                                >
                                    {{ index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium">
                                        {{ product.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ product.category_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold">
                                    {{ product.sold_count }} sold
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatTaka(product.price) }}
                                </p>
                            </div>
                        </div>
                        <p
                            v-if="top_products.length === 0"
                            class="py-6 text-center text-sm text-muted-foreground"
                        >
                            No product sales data yet.
                        </p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
                    <div class="border-b border-border/40 px-6 py-5">
                        <h3 class="text-sm font-semibold text-foreground">Top Categories</h3>
                        <p class="mt-0.5 text-xs text-muted-foreground">By product count</p>
                    </div>
                    <div class="grid gap-0 divide-y divide-border/30 px-6">
                        <div
                            v-for="category in top_categories"
                            :key="category.id"
                            class="flex items-center justify-between gap-3 py-3.5 text-sm"
                        >
                            <span class="font-medium">{{ category.name }}</span>
                            <span class="text-muted-foreground">
                                {{ category.products_count }} products
                            </span>
                        </div>
                        <p
                            v-if="top_categories.length === 0"
                            class="py-6 text-center text-sm text-muted-foreground"
                        >
                            No categories yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
