<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex size-8 items-center justify-center rounded-lg bg-shop-primary-600 text-white shadow-sm"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div class="ml-1.5 grid flex-1 text-left text-sm">
        <span class="truncate leading-tight font-bold tracking-tight">
            Shop<span class="text-shop-primary-500">Ease</span>
        </span>
        <span
            class="truncate text-[10px] font-medium leading-tight text-sidebar-foreground/60"
        >
            Admin Panel
        </span>
    </div>
</template>
