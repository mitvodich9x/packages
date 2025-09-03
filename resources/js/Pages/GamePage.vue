<template>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />
    <!-- {{ game?.flags?.flags?.check_roles }} -->
    <!-- {{ role }} -->
    <div class="header flex mt-2 px-2 md:px-4 items-center">
        <img
            class="mr-2 w-[80px] rounded-3xl shadow-xl lg:mr-4 lg:w-[100px]"
            :src="game?.icon"
            alt=""
            srcset=""
        />
        <div
            v-if="game?.flags?.flags?.check_roles === true"
            class="content w-full"
        >
            <h1 class="font-medium text-2xl md:text-3xl">
                {{ game?.name }}
            </h1>
            <div
                class="char mt-2 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-md lg:w-full"
            >
                <div class="font-medium text-lg">
                    <p>
                        Máy chủ:
                        <span class="text-primary">{{
                            role?.server_name
                        }}</span>
                    </p>
                    <p>
                        Nhân vật:
                        <span class="text-primary">{{ role?.role_name }}</span>
                    </p>
                </div>
                <n-button
                    type="primary"
                    strong
                    size="medium"
                    @click="switchRole(game)"
                >
                    Đổi nhân vật
                </n-button>
                <!-- <a
                    href="javascript:void(0);"
                    class="border border-[var(--vgp-color-primary)] bg-white p-2 font-light text-[var(--vgp-color-primary)] hover:underline"
                    @click="switchRole(selectedGame)"
                    >Đổi nhân vật</a
                > -->
            </div>
        </div>
        <div v-else class="content w-5/6">
            <h1 class="font-medium text-lg">Nạp Vxu</h1>
            <div
                class="char flex items-center justify-between gap-4 text-md lg:w-full"
            >
                <div class="font-medium">
                    <p>
                        Tài khoản: <span>{{ user?.username }}</span>
                    </p>
                    <p>
                        ID: <span>{{ user?.vgp_id }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/useAppStore";
import { useMessage } from "naive-ui";
import Breadcrumbs from "@/Components/Base/Breadcrumbs.vue";
import RoleForm from "@/Components/Form/RoleForm.vue";
// import PaymentMethod from "@/Components/Payment/PaymentMethod.vue";
// import Guide from "@/Components/Guide.vue";
// import axios from "@/lib/api";
import vxuImage from "@/assets/images/vxu.webp";
import { useLoadingStore } from "@/stores/useLoadingStore";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    game: {
        type: Object,
        default: null,
    },
    role: {
        type: Object,
        default: null,
    },
});

const store = useAppStore();
const auth = useAuthStore();
const user = auth?.user;

const switchRole = (game) => {
    store.openDrawer(
        "form",
        RoleForm,
        {
            game: game,
        },
        `Chọn nhân vật`
    );
};
</script>
