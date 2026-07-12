<script setup lang="ts">
import { computed } from 'vue';
import { formatTaka } from '@/lib/shop/currency';
import type { AdminDashboardRevenuePoint } from '@/types/admin';

const props = defineProps<{
    data: AdminDashboardRevenuePoint[];
}>();

const maxRevenue = computed(() =>
    Math.max(...props.data.map((point) => point.revenue), 1),
);

const totalRevenue = computed(() =>
    props.data.reduce((sum, point) => sum + point.revenue, 0),
);

const totalOrders = computed(() =>
    props.data.reduce((sum, point) => sum + point.orders, 0),
);
</script>

<template>
    <div
        class="h-full overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm"
    >
        <div class="border-b border-border/40 px-6 py-5">
            <div class="flex items-center gap-2">
                <div class="size-2 rounded-full bg-indigo-500" />
                <h3 class="text-sm font-semibold text-foreground">
                    Revenue Trend
                </h3>
            </div>
            <p class="mt-1 text-xs text-muted-foreground">
                Paid orders over the last 30 days ·
                {{ formatTaka(totalRevenue) }} from {{ totalOrders }} orders
            </p>
        </div>
        <div class="p-6">
            <div class="flex h-48 items-end gap-1 sm:gap-1.5">
                <div
                    v-for="point in data"
                    :key="point.date"
                    class="group flex min-w-0 flex-1 flex-col items-center gap-2"
                >
                    <div
                        class="relative flex w-full flex-1 items-end justify-center"
                    >
                        <div
                            class="w-full max-w-4 rounded-t-md bg-indigo-500/70 transition-all duration-200 group-hover:bg-indigo-500"
                            :style="{
                                height: `${Math.max((point.revenue / maxRevenue) * 100, point.revenue > 0 ? 4 : 0)}%`,
                            }"
                            :title="`${point.label}: ${formatTaka(point.revenue)}`"
                        />
                    </div>
                    <span
                        class="hidden text-[10px] text-muted-foreground sm:block"
                    >
                        {{ point.label }}
                    </span>
                </div>
            </div>
            <p class="mt-3 text-xs text-muted-foreground sm:hidden">
                Swipe horizontally to view daily bars on smaller screens.
            </p>
        </div>
    </div>
</template>
