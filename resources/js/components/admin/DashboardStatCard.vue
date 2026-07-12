<script setup lang="ts">
import type { Component } from 'vue';

type Props = {
    title: string;
    value: string;
    description?: string;
    changePercent?: number | null;
    icon?: Component;
    iconClass?: string;
};

defineProps<Props>();

function changeLabel(percent: number): string {
    const prefix = percent > 0 ? '+' : '';

    return `${prefix}${percent}% vs last month`;
}

function changeClass(percent: number | null | undefined): string {
    if (percent === null || percent === undefined) {
        return 'text-muted-foreground';
    }

    if (percent > 0) {
        return 'text-emerald-600 dark:text-emerald-400';
    }

    if (percent < 0) {
        return 'text-red-600 dark:text-red-400';
    }

    return 'text-muted-foreground';
}
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-xl border border-border/60 bg-card p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-800/50"
    >
        <!-- Subtle gradient accent on hover -->
        <div
            class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-indigo-500 via-indigo-400 to-indigo-600 opacity-0 transition-opacity group-hover:opacity-100"
        />

        <div class="flex items-start justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                    {{ title }}
                </p>
                <p class="text-2xl font-bold tracking-tight text-foreground">
                    {{ value }}
                </p>
            </div>
            <div
                v-if="icon"
                class="flex size-11 shrink-0 items-center justify-center rounded-xl transition-transform group-hover:scale-105"
                :class="iconClass ?? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'"
            >
                <component :is="icon" class="size-5" />
            </div>
        </div>

        <div class="mt-3">
            <p
                v-if="changePercent !== undefined"
                class="text-xs font-medium"
                :class="changeClass(changePercent)"
            >
                <template v-if="changePercent !== null">
                    {{ changeLabel(changePercent) }}
                </template>
                <template v-else>
                    No data for last month
                </template>
            </p>
            <p v-else-if="description" class="text-xs text-muted-foreground">
                {{ description }}
            </p>
        </div>
    </div>
</template>
