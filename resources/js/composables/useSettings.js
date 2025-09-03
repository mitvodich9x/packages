import { storeToRefs } from "pinia";
import { useSettingStore } from "@/stores/settings";

export function useSettings() {
    const settingStore = useSettingStore();
    const { settings } = storeToRefs(settingStore);

    return {
        settings,

        // shortcuts
        footer: settings.value.footer,
        footerContent: settings.value.footer?.content?.[0] ?? {},

        site: settings.value.site,
        payment: settings.value.payment,
    };
}
