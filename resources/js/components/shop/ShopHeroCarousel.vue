<script setup lang="ts">
import { ref } from 'vue';
import { useShopCarousel } from '@/composables/shop/useShopCarousel';
import type { ShopCarouselSlide } from '@/types/shop';

const { slides } = defineProps<{
    slides: ShopCarouselSlide[];
}>();

const {
    current,
    goTo,
    next,
    prev,
    startAuto,
    stopAuto,
    handleTouchStart,
    handleTouchEnd,
} = useShopCarousel(slides.length);

const touchStartX = ref(0);

function onTouchStart(event: TouchEvent): void {
    touchStartX.value = handleTouchStart(event);
}

function onTouchEnd(event: TouchEvent): void {
    handleTouchEnd(event, touchStartX.value);
}
</script>

<template>
    <section
        v-if="slides.length > 0"
        class="relative"
        aria-roledescription="carousel"
        aria-label="Featured promotions"
    >
        <div
            class="relative h-[44vh] overflow-hidden sm:h-[58vh] lg:h-[75vh]"
            @mouseenter="stopAuto"
            @mouseleave="startAuto"
            @touchstart.passive="onTouchStart"
            @touchend.passive="onTouchEnd"
        >
            <div
                v-for="(slide, index) in slides"
                :key="index"
                class="shop-slide absolute inset-0"
                :class="{ active: current === index }"
                role="group"
                aria-roledescription="slide"
                :aria-label="`${index + 1} of ${slides.length}`"
            >
                <img
                    :src="slide.src"
                    :alt="slide.alt"
                    class="h-full w-full object-cover"
                />
                <!-- Dark overlay for better contrast -->
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent" />
            </div>

            <button
                type="button"
                aria-label="Previous slide"
                class="absolute top-1/2 left-5 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-2xl bg-white/90 text-gray-800 shadow-xl ring-1 ring-black/5 transition duration-200 hover:bg-white hover:shadow-2xl hover:scale-105 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none md:flex"
                @click="
                    prev();
                    startAuto();
                "
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>
            <button
                type="button"
                aria-label="Next slide"
                class="absolute top-1/2 right-5 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-2xl bg-white/90 text-gray-800 shadow-xl ring-1 ring-black/5 transition duration-200 hover:bg-white hover:shadow-2xl hover:scale-105 focus:ring-2 focus:ring-shop-primary-600 focus:outline-none md:flex"
                @click="
                    next();
                    startAuto();
                "
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>

            <div
                class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-2.5"
            >
                <button
                    v-for="(_, index) in slides"
                    :key="index"
                    type="button"
                    :aria-label="`Go to slide ${index + 1}`"
                    class="transition-all duration-300"
                    :class="
                        current === index
                            ? 'h-2.5 w-8 rounded-full bg-white shadow-lg'
                            : 'h-2.5 w-2.5 rounded-full bg-white/50 hover:bg-white/75'
                    "
                    :aria-current="current === index ? 'true' : 'false'"
                    @click="
                        goTo(index);
                        startAuto();
                    "
                />
            </div>
        </div>
    </section>
</template>
