<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { CouponFormData } from '@/types/admin';

defineProps<{
    form: InertiaForm<CouponFormData>;
}>();
</script>

<template>
    <div class="space-y-5">
        <div class="space-y-2">
            <Label for="code">Coupon Code</Label>
            <Input
                id="code"
                v-model="form.code"
                placeholder="e.g. SAVE20"
                class="uppercase"
            />
            <InputError :message="form.errors.code" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="discount_type">Discount Type</Label>
                <Select v-model="form.discount_type">
                    <SelectTrigger id="discount_type">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="flat">Flat (৳)</SelectItem>
                        <SelectItem value="percentage"
                            >Percentage (%)</SelectItem
                        >
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.discount_type" />
            </div>

            <div class="space-y-2">
                <Label for="discount_value">
                    Discount Value
                    <span class="text-muted-foreground">
                        ({{ form.discount_type === 'percentage' ? '%' : '৳' }})
                    </span>
                </Label>
                <Input
                    id="discount_value"
                    v-model="form.discount_value"
                    type="number"
                    step="0.01"
                    min="0"
                />
                <InputError :message="form.errors.discount_value" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="min_order_amount">Min. Order Amount (৳)</Label>
                <Input
                    id="min_order_amount"
                    v-model="form.min_order_amount"
                    type="number"
                    step="1"
                    min="0"
                    placeholder="0"
                />
                <InputError :message="form.errors.min_order_amount" />
            </div>

            <div class="space-y-2">
                <Label for="usage_limit">Usage Limit</Label>
                <Input
                    id="usage_limit"
                    v-model="form.usage_limit"
                    type="number"
                    min="1"
                    placeholder="Unlimited"
                />
                <InputError :message="form.errors.usage_limit" />
            </div>
        </div>

        <div class="space-y-2">
            <Label for="expires_at">Expiry Date</Label>
            <Input id="expires_at" v-model="form.expires_at" type="date" />
            <InputError :message="form.errors.expires_at" />
        </div>

        <div class="flex items-center gap-2">
            <Checkbox
                id="is_active"
                :model-value="form.is_active"
                @update:model-value="form.is_active = !!$event"
            />
            <Label for="is_active" class="cursor-pointer">Active</Label>
        </div>
    </div>
</template>
