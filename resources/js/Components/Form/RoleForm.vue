<template>
    <div class="p-4 space-y-4">
        <n-form
            :model="form"
            @submit.prevent="submit"
            class="w-full lg:max-w-1/2 mx-auto"
            ref="formRef"
        >
            <n-form-item label="Chọn máy chủ" path="server">
                <n-select
                    v-model:value="form.server"
                    :options="serverOptions"
                    placeholder="Chọn máy chủ"
                    filterable
                    show-search
                >
                    <template #empty>
                        <span>Không có nhân vật</span>
                    </template>
                </n-select>
            </n-form-item>

            <n-form-item
                v-if="form.server && hasCharacters"
                label="Chọn nhân vật"
                path="role"
            >
                <n-select
                    v-model:value="form.role"
                    :options="filteredRoleOptions"
                    placeholder="Chọn nhân vật"
                    :render-label="renderRoleLabel"
                />
            </n-form-item>

            <n-button
                type="primary"
                :loading="processing"
                block
                @click="confirmSelection"
                :disabled="
                    !form.server ||
                    (hasCharacters && !form.role) ||
                    (form.server && !hasCharacters)
                "
            >
                Xác nhận
            </n-button>
        </n-form>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useAppStore } from "@/stores/useAppStore";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { useMessage } from "naive-ui";
import axios from "axios";

const props = defineProps({
    game: {
        type: Object,
        required: true,
    },
    type: {
        type: String,
    },
});

// console.log("role form: ", props.game);

// const emit = defineEmits(["role-selected", "drawer-close"]);
const page = usePage();
const store = useAppStore();
const message = useMessage();

// const selectedRole = ref(null);

const form = useForm({
    server: "",
    role: "",
});

const processing = ref(false);
const gameType = ref("");
const allServers = ref([]);
const allRoles = ref([]);

const hasCharacters = computed(() => gameType.value === "roles");

// const serverOptions = computed(() => {
//     const grouped = new Map();

//     allServers.value.forEach((s) => {
//         const key = s.server_id;
//         if (!grouped.has(key)) {
//             grouped.set(key, {
//                 label: `${s.server_name}`,
//                 value: key,
//                 count:
//                     hasCharacters.value && Array.isArray(s.characters)
//                         ? s.characters.length
//                         : 0,
//             });
//         }
//     });

//     //  (${s.count} nhân vật)
//     return Array.from(grouped.values()).map((s) => ({
//         label: hasCharacters.value
//             ? `${s.label}`
//             : s.label,
//         value: s.value,
//     }));
// });

// const filteredRoleOptions = computed(() => {
//     if (!form.server || !hasCharacters.value) return [];
//     const server = allServers.value.find((s) => s.server_id === form.server);
//     return (server?.characters ?? []).map((c) => ({
//         label: `${c.name} - Lv ${c.lv}`,
//         value: c.id,
//     }));
// });
const toNum = (v) => (v == null ? 0 : Number(v) || 0);

const serverOptions = computed(() => {
    const grouped = new Map();

    const sorted = (allServers.value ?? [])
        .slice()
        .sort((a, b) => toNum(b.server_id) - toNum(a.server_id));

    sorted.forEach((s) => {
        const key = s.server_id;
        if (!grouped.has(key)) {
            grouped.set(key, {
                label: `${s.server_name}`,
                value: key,
                count:
                    hasCharacters.value && Array.isArray(s.characters)
                        ? s.characters.length
                        : 0,
            });
        }
    });

    return Array.from(grouped.values()).map((s) => ({
        label: hasCharacters.value ? `${s.label}` : s.label,
        value: s.value,
    }));
});

const filteredRoleOptions = computed(() => {
    if (!form.server || !hasCharacters.value) return [];
    const server = (allServers.value ?? []).find(
        (s) => String(s.server_id) === String(form.server)
    );

    const chars = server?.characters ?? [];
    const sortedChars = chars.slice().sort((a, b) => toNum(b.id) - toNum(a.id));

    return sortedChars.map((c) => ({
        label: `${c.name} - Lv ${c.lv}`,
        value: c.id,
    }));
});


const renderRoleLabel = (option) => {
    const server = allServers.value.find((s) => s.server_id === form.server);
    const role = server?.characters?.find((r) => r.id === option.value);
    if (!role) return option.label;
    return `${role.name} (Lv ${role.lv}) - Id: ${role.id}`;
};

const fetchServersAndRoles = async () => {
    // console.log(props.type);

    if (!props.game.value && !props.game.game_id) return;

    processing.value = true;

    try {
        const response = await axios.post("/roles", {
            game: props.game.game_id ?? props.game.value,
        });

        const data = response.data?.data ?? {};
        gameType.value = data.type;

        if (data.type === "roles") {
            allServers.value = data.servers ?? [];
        } else if (data.type === "servers_characters") {
            allServers.value = data.servers ?? [];
        } else {
            message.error("Loại dữ liệu không xác định.");
        }
        message.success("Lấy thông tin máy chủ và nhân vật thành công");
    } catch (error) {
        message.error("Không thể lấy thông tin máy chủ và nhân vật.");
    } finally {
        processing.value = false;
    }
};

const fetchCharactersByServer = async () => {
    if (!props.game.value || !form.server || hasCharacters.value) return;

    processing.value = true;

    try {
        const response = await axios.post("/roles-by-server", {
            game: props.game.value,
            server: form.server,
        });

        const characters = response.data?.data ?? [];

        const serverIndex = allServers.value.findIndex(
            (s) => s.server_id === form.server
        );
        if (serverIndex !== -1) {
            allServers.value[serverIndex].characters = characters;
        }
        message.success("Lấy danh sách nhân vật thành công.");
    } catch (error) {
        message.error("Không thể lấy danh sách nhân vật theo server.");
    } finally {
        processing.value = false;
    }
};

const confirmSelection = () => {
    if (!form.server || (hasCharacters.value && !form.role)) {
        message.error("Vui lòng chọn máy chủ và nhân vật.");
        return;
    }

    const alias = props.game.alias;
    const server = allServers.value.find((s) => s.server_id === form.server);
    const role = server?.characters?.find((r) => r.id === form.role);

    const serverName = server?.server_name || "";
    const roleName = role?.name || "";

    if (alias) {
        router.post("/select-role", {
            alias,
            server_id: form.server,
            server_name: serverName,
            role_id: form.role,
            role_name: roleName,
        });

        store.closeDrawer();
        // router.push(`/${alias}`);
    } else {
        message.warning("Không tìm thấy alias của game.");
    }
};

const submit = () => {
    confirmSelection();
};

watch(
    () => form.server,
    () => {
        if (form.role) form.role = null;
        fetchCharactersByServer();
    }
);

// watch(
//     () => props.game,
//     () => {
//         fetchServersAndRoles();
//     },
//     { immediate: true }
// );

onMounted(() => {
    fetchServersAndRoles();
});
</script>
