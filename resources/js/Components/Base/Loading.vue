<template>
    <div
        v-if="loading.visible"
        class="fixed inset-0 bg-[rgba(0,0,0,0.7)] flex items-center justify-center z-[9999]"
    >
        <div class="relative flex items-center justify-center">
            <img :src="vgpImage" alt="Logo" class="w-16 h-16 relative z-10" />
            <div
                class="absolute w-24 h-24 bg-white border-4 border-orange-500 border-t-transparent rounded-full animate-spin"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { useLoadingStore } from "@/stores/useLoadingStore";
import vgpImage from "@/assets/images/vgp.webp";

const loading = useLoadingStore();
</script>

<style>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
