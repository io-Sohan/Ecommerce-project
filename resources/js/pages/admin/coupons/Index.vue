<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import CouponController from '@/actions/App/Http/Controllers/Admin/CouponController';
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
import { create, edit, index } from '@/routes/admin/coupons';
import type { AdminCoupon } from '@/types/admin';

defineProps<{
    coupons: AdminCoupon[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Coupons', href: index() },
        ],
    },
});

function formatDiscount(coupon: AdminCoupon): string {
    return coupon.discount_type === 'percentage'
        ? `${coupon.discount_value}%`
        : `৳${coupon.discount_value}`;
}
</script>

<template>
    <Head title="Coupons" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading
                title="Coupons"
                description="Manage discount coupons for your store"
            />

            <Button as-child class="bg-indigo-600 hover:bg-indigo-700">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add coupon
                </Link>
            </Button>
        </div>

        <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[960px] text-sm">
                    <thead class="border-b bg-muted/30 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Code</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Discount</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Min. Order</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Usage</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Expires</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="coupon in coupons"
                            :key="coupon.id"
                            class="border-b border-border/30 last:border-b-0 transition-colors hover:bg-muted/20"
                        >
                            <td class="px-4 py-3 font-mono font-semibold">
                                {{ coupon.code }}
                            </td>
                            <td class="px-4 py-3">
                                {{ formatDiscount(coupon) }}
                                <span class="ml-1 text-xs text-muted-foreground">
                                    ({{ coupon.discount_type }})
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                ৳{{ coupon.min_order_amount }}
                            </td>
                            <td class="px-4 py-3">
                                {{ coupon.times_used }}
                                <span v-if="coupon.usage_limit !== null">
                                    / {{ coupon.usage_limit }}
                                </span>
                                <span v-else class="text-muted-foreground">
                                    / ∞
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <template v-if="coupon.expires_at">
                                    {{ coupon.expires_at }}
                                </template>
                                <span v-else class="text-muted-foreground">Never</span>
                            </td>
                            <td class="px-4 py-3">
                                <Badge :variant="coupon.is_active ? 'default' : 'secondary'">
                                    {{ coupon.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <Button as-child variant="outline" size="sm">
                                        <Link :href="edit(coupon.id)">
                                            <Pencil class="size-4" />
                                            Edit
                                        </Link>
                                    </Button>
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button variant="destructive" size="sm">
                                                <Trash2 class="size-4" />
                                                Delete
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <Form
                                                v-bind="CouponController.destroy.form(coupon.id)"
                                                :options="{ preserveScroll: true }"
                                                v-slot="{ processing }"
                                            >
                                                <DialogHeader class="space-y-3">
                                                    <DialogTitle>Delete coupon?</DialogTitle>
                                                    <DialogDescription>
                                                        This will remove coupon
                                                        <strong>{{ coupon.code }}</strong>.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <DialogFooter class="gap-2">
                                                    <DialogClose as-child>
                                                        <Button type="button" variant="secondary">Cancel</Button>
                                                    </DialogClose>
                                                    <Button type="submit" variant="destructive" :disabled="processing">
                                                        Delete
                                                    </Button>
                                                </DialogFooter>
                                            </Form>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="coupons.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-muted-foreground">
                                No coupons yet. Create your first coupon.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
