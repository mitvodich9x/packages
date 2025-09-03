<template>
    <n-breadcrumb v-if="breadcrumbs.length" class="p-2 pb-0 md:p-4 md:pb-0 bg-white text-sm">
        <n-breadcrumb-item v-for="(item, index) in breadcrumbs" :key="index">
            <!-- <n-icon :component="MdCash" class="mr-1" /> -->
            <a
                :href="item.url"
                :class="[
                    index === breadcrumbs.length - 1
                        ? 'text-[var(--vgp-color-primary)]! font-semibold  hover:underline!'
                        : 'text-gray-500 hover:underline',
                ]"
            >
                {{ item.label }}
            </a>
        </n-breadcrumb-item>
    </n-breadcrumb>
</template>

<script setup>
const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});
</script>
