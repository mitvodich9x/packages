// resources/js/stores/auth.js
import { defineStore } from "pinia";
import { router } from "@inertiajs/vue3";
import axios from "@/lib/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null, // object từ backend share('auth.user')
        loginType: null, // "vgp" | "facebook" | null
        fbGameId: null,
        fbGameAlias: null,
    }),

    getters: {
        isLoggedIn: (state) => !!state.user,
        isFacebook: (state) => state.loginType === "facebook",
        userInfo: (state) => state.user?.info ?? {},
        userToken: (state) => state.user?.user_token ?? null,
    },

    actions: {
        async login(credentials) {
            try {
                const res = await axios.post("/login", credentials);
                this.user = res.data?.user ?? null;
                await router.reload({ only: ["auth"] });

                return res;
            } catch (err) {
                console.error("Login failed:", err);
                throw err;
            }
        },

        async logout() {
            try {
                await axios.post("/logout");
            } catch (e) {
                console.warn("Logout request failed:", e);
            } finally {
                this.clearAuth();
                await router.reload({ only: ["auth"] });
            }
        },

        hydrateFromPage(pageProps) {
            if (pageProps?.auth?.user) {
                this.user = pageProps.auth.user;
                this.loginType = pageProps.auth.login_type ?? "vgp";
                this.fbGameId = pageProps.auth.fb_game_id ?? null;
                this.fbGameAlias = pageProps.auth.fb_game_alias ?? null;
            } else {
                this.clearAuth();
            }
        },

        /**
         * Tiện ích: tự gọi router.reload để cập nhật auth và hydrate lại
         */
        async refreshAuth() {
            await router.reload({ only: ["auth"] });
        },

        /**
         * Clear local state
         */
        clearAuth() {
            this.user = null;
            this.loginType = null;
            this.fbGameId = null;
            this.fbGameAlias = null;
        },
    },
});
