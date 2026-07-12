<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Bell } from '@lucide/vue';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-border/60 bg-card px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6"
    >
        <div class="flex items-center gap-3">
            <SidebarTrigger class="-ml-1 text-muted-foreground hover:text-foreground" />
            <div
                v-if="breadcrumbs && breadcrumbs.length > 0"
                class="hidden md:block"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                class="relative flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
            >
                <Bell class="size-4" />
                <span
                    class="absolute top-1.5 right-1.5 flex size-2 rounded-full bg-indigo-500"
                />
            </button>

            <div class="hidden items-center gap-2.5 sm:flex">
                <Avatar class="size-8 rounded-lg shadow-sm ring-2 ring-indigo-500/20">
                    <AvatarImage
                        v-if="auth.user.avatar"
                        :src="auth.user.avatar"
                        :alt="auth.user.name"
                    />
                    <AvatarFallback
                        class="rounded-lg bg-indigo-500/10 text-xs font-semibold text-indigo-600"
                    >
                        {{ getInitials(auth.user?.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="grid text-left text-xs leading-tight">
                    <span class="font-semibold text-foreground">{{
                        auth.user.name
                    }}</span>
                    <span class="text-muted-foreground">{{
                        auth.user.role === 'admin' ? 'Administrator' : 'User'
                    }}</span>
                </div>
            </div>
        </div>
    </header>
</template>
