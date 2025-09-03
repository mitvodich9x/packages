import "./bootstrap";
import "./lib/api";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createPinia } from "pinia";
import router from "./router";
import naive from "naive-ui";
import { useSettingStore } from "@/stores/settings";
import { useAuthStore } from "@/stores/auth";
import AppLayout from "@/Layouts/AppLayout.vue";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });

        const page = Object.entries(pages).find(([key]) =>
            key.endsWith(`/${name}.vue`)
        )?.[1];

        if (!page) {
            throw new Error(`Page not found: ${name}`);
        }

        const pageComponent = page.default;

        if (!pageComponent.layout) {
            pageComponent.layout = AppLayout;
        }

        return pageComponent;
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(router)
            .use(naive);

        // inject settings từ Inertia props
        const authStore = useAuthStore();
        authStore.hydrateFromPage(props.initialPage.props);

        const settingStore = useSettingStore(pinia);
        settingStore.setSettings(props.initialPage.props.settings);

        app.mount(el);
    },
});
