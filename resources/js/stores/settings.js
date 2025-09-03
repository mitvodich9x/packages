import { defineStore } from "pinia";

export const useSettingStore = defineStore("settings", {
    state: () => ({
        settings: {
            footer: { content: [] }, // mặc định content là mảng rỗng
            site: {},
            payment: {},
        },
    }),
    actions: {
        setSettings(settings) {
            this.settings = {
                footer: settings?.footer ?? { content: [] },
                site: settings?.site ?? {},
                payment: settings?.payment ?? {},
            };
        },
        get(key, defaultValue = null) {
            return this.settings[key] ?? defaultValue;
        },
    },
});
