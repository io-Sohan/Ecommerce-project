<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2 } from '@lucide/vue';
import CategoryController from '@/actions/App/Http/Controllers/Admin/CategoryController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { dashboard } from '@/routes';
import { create, edit, index, show } from '@/routes/admin/categories';
import type { AdminCategory } from '@/types/admin';

defineProps<{
    categories: AdminCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Categories',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Categories" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Categories"
                description="Manage product categories for your store"
            />

            <Button as-child class="bg-indigo-600 hover:bg-indigo-700">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add category
                </Link>
            </Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[960px] text-sm">
                    <thead class="border-b bg-muted/30 text-left">
                        <tr>
                            <th
                                class="px-5 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Image
                            </th>
                            <th
                                class="px-4 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Slug
                            </th>
                            <th
                                class="px-4 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Products
                            </th>
                            <th
                                class="px-4 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Sort
                            </th>
                            <th
                                class="px-4 py-3 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in categories"
                            :key="category.id"
                            class="border-b border-border/30 transition-colors last:border-b-0 hover:bg-muted/20"
                        >
                            <td class="px-5 py-3.5">
                                <div
                                    class="size-12 overflow-hidden rounded-md border bg-muted"
                                >
                                    <img
                                        v-if="category.image"
                                        :src="category.image"
                                        :alt="category.name"
                                        class="size-full object-cover"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ category.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ category.slug }}
                            </td>
                            <td class="px-4 py-3">
                                {{ category.products_count }}
                            </td>
                            <td class="px-4 py-3">
                                {{ category.sort_order }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        category.is_active
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        category.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        as-child
                                        variant="outline"
                                        size="sm"
                                    >
                                        <Link :href="show(category.id)">
                                            <Eye class="size-4" />
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        as-child
                                        variant="outline"
                                        size="sm"
                                    >
                                        <Link :href="edit(category.id)">
                                            <Pencil class="size-4" />
                                            Edit
                                        </Link>
                                    </Button>
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                :data-test="`delete-category-${category.id}`"
                                            >
                                                <Trash2 class="size-4" />
                                                Delete
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <Form
                                                v-bind="
                                                    CategoryController.destroy.form(
                                                        category.id,
                                                    )
                                                "
                                                :options="{
                                                    preserveScroll: true,
                                                }"
                                                v-slot="{ processing }"
                                            >
                                                <DialogHeader class="space-y-3">
                                                    <DialogTitle>
                                                        Delete category?
                                                    </DialogTitle>
                                                    <DialogDescription>
                                                        This will remove
                                                        <strong>{{
                                                            category.name
                                                        }}</strong>
                                                        from your store. This
                                                        action can be undone
                                                        from the database if
                                                        needed.
                                                    </DialogDescription>
                                                </DialogHeader>

                                                <DialogFooter class="gap-2">
                                                    <DialogClose as-child>
                                                        <Button
                                                            type="button"
                                                            variant="secondary"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </DialogClose>
                                                    <Button
                                                        type="submit"
                                                        variant="destructive"
                                                        :disabled="processing"
                                                        :data-test="`confirm-delete-category-${category.id}`"
                                                    >
                                                        Delete
                                                    </Button>
                                                </DialogFooter>
                                            </Form>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                No categories yet. Create your first category.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
