<template>
    <div class="max-w-4xl mx-auto">
        <n-config-provider :theme-overrides="themeOverrides">
            <Loading />
            <n-message-provider>
                <Header
                    @open-login="
                        store.openDrawer('login', 'LoginForm', {}, 'Đăng nhập')
                    "
                />

                <n-layout has-sider class="min-h-[calc(100vh-128px)]">
                    <n-layout-content :native-scrollbar="false" class="pb-20">
                        <slot :site="site" :payment="payment" />
                    </n-layout-content>
                </n-layout>

                <Footer :footer="footer" />

                <n-drawer
                    v-model:show="store.drawer.active"
                    :width="502"
                    placement="bottom"
                    :class="store.drawer.cssStyle"
                    style="background: #fff; max-width: 896px"
                    :content-style="{ paddingLeft: '0px', paddingRight: '0px' }"
                    :body-content-style="{ padding: '0px' }"
                >
                    <n-drawer-content
                        :title="store.drawer.title"
                        class="max-w-4xl; mx-auto; flex-none!"
                        :header-style="store.drawer.cssHeaderStyle"
                        closable
                        @close="store.closeDrawer"
                    >
                        <component
                            :is="store.drawer.component"
                            v-bind="store.drawer.props"
                            @logged-in="store.setUser"
                            @role-selected="handleRoleSelected"
                            @drawer-close="store.closeDrawer"
                        />
                    </n-drawer-content>
                </n-drawer>
            </n-message-provider>
        </n-config-provider>
    </div>
</template>

<script setup>
import { computed, watch, onMounted } from "vue";
import { useAppStore } from "@/stores/useAppStore";
import { usePage, router } from "@inertiajs/vue3";
import Header from "@/Components/Base/Header.vue";
import Footer from "@/Components/Base/Footer.vue";
import { useAuthStore } from "@/stores/auth";
import Loading from "@/Components/Base/Loading.vue";
import { useLoadingStore } from "@/stores/useLoadingStore";
import { useSettings } from "@/composables/useSettings";

const { footer, site, payment } = useSettings();
const store = useAppStore();
const page = usePage();

const authStore = useAuthStore(); // Pinia (nếu bạn cần)
const loading = useLoadingStore();

// ✅ Tạo computed reactive từ Inertia props
const authProps = computed(() => page.props.auth || {});
const inertiaUser = computed(() => authProps.value.user || null);

console.log("games: ", page.props.games);

// (nếu muốn log thay đổi)
watch(
    [authProps, () => page.url],
    ([newAuth, newUrl]) => {
        // console.log('auth changed', newAuth, 'url', newUrl)
    },
    { immediate: true }
);

const themeOverrides = {
    common: {
        primaryColor: "#FF9138",
        primaryColorHover: "#e67300",
        primaryColorPressed: "#cc6600",
        primaryColorSuppl: "#ffa64d",
    },
};

onMounted(() => {
    loading.show();
    window.addEventListener("load", () => loading.hide());

    // ❌ Sai: only: ["userData", "balance"] (không phải top-level)
    // ✅ Đúng: reload lại 'auth' (top-level prop bạn share từ backend)
    router.reload({ only: ["auth"] });

    router.on("start", () => loading.show());
    router.on("finish", () => loading.hide());
});

function handleRoleSelected({ game, role }) {
    // ...
}
</script>
