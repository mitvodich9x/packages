import { defineStore } from "pinia";
import LoginForm from "@/Components/Form/LoginForm.vue";

export const useAppStore = defineStore("app", {
    state: () => ({
        user: null,
        drawer: {
            active: false,
            type: "",
            title: "",
            component: null,
            cssStyle: "",
            props: {},
            cssHeaderStyle: {
                textAlign: "center",
                color: "#FF9138",
                fontSize: "25px",
                textTransform: "uppercase",
            },
        },
    }),

    actions: {
        openDrawer(
            type,
            component = null,
            props = {},
            title = "",
            cssStyle = "",
            cssHeaderStyle = {}
        ) {
            const defaultCssHeaderStyle = {
                textAlign: "center",
                color: "#FF9138",
                fontSize: "25px",
                textTransform: "uppercase",
            };

            this.drawer = {
                active: true,
                type,
                title,
                cssStyle: cssStyle || "mx-auto! min-h-[70%]!",
                component: component || LoginForm,
                component: component,
                props,
                cssHeaderStyle: {
                    ...defaultCssHeaderStyle,
                    ...cssHeaderStyle,
                },
            };
        },

        closeDrawer() {
            this.drawer.active = false;
            this.drawer.component = null;
            this.drawer.props = {};
            this.drawer.title = "";
            this.drawer.type = "";
        },

        setUser(userData) {
            this.user = userData;
            this.closeDrawer();
        },
    },
});
