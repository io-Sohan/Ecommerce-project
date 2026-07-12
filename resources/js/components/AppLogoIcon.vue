<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Shopping bag body -->
        <rect
            x="6"
            y="14"
            width="28"
            height="24"
            rx="4"
            fill="currentColor"
            opacity="0.15"
        />
        <rect
            x="6"
            y="14"
            width="28"
            height="24"
            rx="4"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
        />
        <!-- Bag handle -->
        <path
            d="M14 14V10a6 6 0 0 1 12 0v4"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
        />
        <!-- Checkmark inside bag -->
        <path
            d="M15 26l4 4 6-8"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
