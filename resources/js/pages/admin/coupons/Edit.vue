<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CouponController from '@/actions/App/Http/Controllers/Admin/CouponController';
import CouponForm from '@/components/admin/CouponForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { edit, index } from '@/routes/admin/coupons';
import type { AdminCoupon, CouponFormData } from '@/types/admin';

const props = defineProps<{
    coupon: AdminCoupon;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Coupons', href: index() },
            { title: 'Edit', href: edit(0) },
        ],
    },
});

const form = useForm<CouponFormData>({
    code: props.coupon.code,
    discount_type: props.coupon.discount_type,
    discount_value: props.coupon.discount_value,
    min_order_amount: props.coupon.min_order_amount,
    usage_limit: props.coupon.usage_limit ?? '',
    expires_at: props.coupon.expires_at ?? '',
    is_active: props.coupon.is_active,
});

function submit(): void {
    form.put(CouponController.update.url(props.coupon.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Edit coupon: ${coupon.code}`" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <Heading
            :title="`Edit coupon: ${coupon.code}`"
            description="Update this coupon's details"
        />

        <div
            class="max-w-2xl rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border"
        >
            <form class="space-y-6" @submit.prevent="submit">
                <CouponForm :form="form" />

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="form.processing">
                        Update coupon
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
