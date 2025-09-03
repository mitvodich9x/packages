<template>
    <n-form
        :model="form"
        :rules="rules"
        @submit.prevent="submit"
        class="space-y-4 w-full lg:max-w-[60%] mx-auto p-4 md:px-8"
        ref="formRef"
    >
        <n-form-item label="Tên Đăng Nhập" path="username">
            <n-input
                v-model:value="form.username"
                placeholder="Tên Đăng Nhập"
            />
        </n-form-item>

        <n-form-item label="Mật khẩu" path="password">
            <n-input
                type="password"
                v-model:value="form.password"
                placeholder="Mật khẩu"
                show-password-on="click"
                :maxlength="50"
            >
                <template #password-visible-icon>
                    <n-icon :size="16" :component="EyeOutline" />
                </template>
                <template #password-invisible-icon>
                    <n-icon :size="16" :component="EyeOffOutline" />
                </template>
            </n-input>
        </n-form-item>

        <n-button
            type="primary"
            size="large"
            strong
            attr-type="submit"
            :loading="processing"
            block
        >
            <span class="text-lg md:text-xl">Đăng nhập bằng tài khoản VGP</span>
        </n-button>

        <div v-if="props.game.value !== 10">
            <div class="text-center text-gray-400 my-1">Hoặc</div>

            <n-space vertical :size="12" class="w-full">
                <n-button
                    block
                    type="info"
                    size="large"
                    strong
                    @click="loginWithFacebook"
                >
                    <template #icon>
                        <!-- icon facebook -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                        >
                            <path
                                d="M480 257.35c0-123.7-100.3-224-224-224s-224 100.3-224 224c0 111.8 81.9 204.47 189 221.29V322.12h-56.89v-64.77H221V208c0-56.13 33.45-87.16 84.61-87.16c24.51 0 50.15 4.38 50.15 4.38v55.13H327.5c-27.81 0-36.51 17.26-36.51 35v42h62.12l-9.92 64.77H291v156.54c107.1-16.81 189-109.48 189-221.31z"
                                fill="currentColor"
                            />
                        </svg>
                    </template>
                    <span class="text-lg md:text-xl"
                        >Đăng nhập bằng Facebook</span
                    >
                </n-button>
            </n-space>
        </div>

        <div class="py-5 font-medium text-md">
            <div
                class="flex justify-between gap-1 text-[var(--vgp-color-primary)]"
            >
                <a href="https://vgplay.vn/register">Bạn chưa có tài khoản?</a>
                <a href="https://vgplay.vn/password/reset">Quên mật khẩu</a>
            </div>
        </div>
    </n-form>
</template>

<script setup>
import { ref } from "vue";
import { EyeOutline, EyeOffOutline } from "@vicons/ionicons5";
import { useForm, router } from "@inertiajs/vue3";
import { useMessage } from "naive-ui";
import { useAuthStore } from "@/stores/auth";
import RoleForm from "@/Components/Form/RoleForm.vue";

const props = defineProps({
    game: { type: Object, default: null },
    option: { type: String, default: null },
});

const emit = defineEmits(["logged-in", "drawer-close"]);
const message = useMessage();
const processing = ref(false);

const auth = useAuthStore();

const form = useForm({
    username: "",
    password: "",
    ...(props.game ? { game_id: props.game.value } : {}),
});

const rules = {
    username: [
        {
            required: true,
            message: "Vui lòng nhập tên đăng nhập",
            trigger: "blur",
        },
    ],
    password: [
        { required: true, message: "Vui lòng nhập mật khẩu", trigger: "blur" },
    ],
};

const redirectAfterLogin = () => {
    if (props.option === "vxu") {
        router.visit("/vxu/payment");
    } else {
        const intended = props.game?.alias;
        if (intended) {
            router.reload({ only: ["userData", "roleData", "balance"] });
            if (props.game?.flags?.check_roles === 1) {
                // mở form chọn nhân vật
                useAppStore().openDrawer(
                    "form",
                    RoleForm,
                    { game: props.game, type: "click" },
                    "Chọn nhân vật"
                );
            } else {
                router.visit(`/${intended}/payment`);
            }
        } else {
            router.visit("/");
        }
    }
};

const submit = async () => {
    processing.value = true;
    try {
        await auth.login(form); // gọi API Sanctum qua store
        emit("logged-in");
        emit("drawer-close");
        redirectAfterLogin();
        message.success("Đăng nhập thành công!");
    } catch (err) {
        message.error(
            err.response?.data?.message || "Có lỗi xảy ra khi đăng nhập."
        );
    } finally {
        processing.value = false;
    }
};

function loginWithFacebook() {
    const currentOrigin = window.location.origin;
    const redirectUri = encodeURIComponent(
        `${currentOrigin}/${props.game.alias}`
    );
    window.location.href = `/oauth/facebook/login?game_id=${encodeURIComponent(
        props.game.value
    )}&redirect_uri=${redirectUri}`;
}
</script>
