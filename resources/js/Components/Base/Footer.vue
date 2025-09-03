<template>
    <footer
        class="footer footer-horizontal footer-center bg-gray-200 px-4 py-10 text-black"
    >
        <div class="grid gap-2 md:grid-cols-3 md:gap-6 font-[400]">
            <div class="image flex items-center w-1/2 md:w-auto mx-auto">
                <a href="/"
                    ><img :src="vgpImage" alt="" srcset="" class=""
                /></a>
            </div>
            <div
                class="contact px-6 md:px-0 text-md font-[400] flex justify-center"
            >
                <div class="text-center md:text-left">
                    <div class="text-lg uppercase font-medium">
                        {{ footer?.company ?? "Công ty cổ phần VGP" }}
                    </div>
                    <p class="uppercase">Thông tin liên hệ</p>
                    <p>
                        Hotline:
                        <a
                            :href="`tel:${footer?.hotline ?? '1900 636 521'}`"
                            class="text-primary underline"
                            >{{ footer?.hotline ?? '1900 636 521' }}</a
                        >
                    </p>
                    <p>
                        Email:
                        <a
                            :href="`mailto:${footer?.email ?? 'hotro@vgpjsc.vn'}`"
                            class="text-primary underline"
                            >{{ footer?.email }}</a
                        >
                    </p>
                </div>
            </div>
            <div class="address hidden md:block text-md font-[400]">
                <p class="uppercase">CHỊU TRÁCH NHIỆM QUẢN LÝ NỘI DUNG:</p>
                <p>{{ footer?.content_auth ?? 'Hoàng Thái Sơn' }}</p>
                <p>{{ footer?.lincense ?? 'Giấy phép G1 số: 318/GP-PTTH&TTDT cấp ngày 15/7/2025' }}</p>
                <p class="uppercase">Địa chỉ:</p>
                <p>{{ footer?.address ?? 'Tầng 4 tòa nhà Greenpark Tower, 33 Dương Đình Nghệ, Yên Hòa, Hà Nội.' }}</p>
            </div>
        </div>
    </footer>
    <div
        class="mfooter sticky bottom-0 z-10 border-t border-t-gray-300 bg-white text-center shadow-xl"
    >
        <nav class="grid grid-cols-4 md:gap-2">
            <a
                href="javascript:void(0);"
                class="dark:active:bg-[var(--vgp-color-secondary)]! capitalize dark:active:text-black! flex flex-col md:flex-row items-center justify-center py-2 md:py-4 dark:text-black dark:hover:bg-[var(--vgp-color-secondary)] text-[.85em] md:text-lg"
                alt=""
                @click="loginToken"
                ><svg
                    class="inline w-10 h-10 md:w-5 md:h-5 md:mr-2 text-[var(--vgp-color-primary)]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                    data-slot="icon"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                    ></path>
                </svg>
                Tài khoản</a
            >
            <a
                href="javascript:void(0);"
                class="dark:active:bg-[var(--vgp-color-secondary)]! capitalize dark:active:text-black! flex flex-col md:flex-row items-center justify-center py-2 md:py-4 dark:text-black dark:hover:bg-[var(--vgp-color-secondary)] text-[.85em] md:text-lg"
                alt=""
                @click="loginToken('support')"
                ><svg
                    class="inline w-10 h-10 md:w-5 md:h-5 md:mr-2 text-[var(--vgp-color-primary)]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                    data-slot="icon"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                    ></path>
                </svg>
                Hỗ trợ</a
            >
            <a
                href="javascript:void(0);"
                class="dark:active:bg-[var(--vgp-color-secondary)]! capitalize dark:active:text-black! flex flex-col md:flex-row items-center justify-center py-2 md:py-4 dark:text-black dark:hover:bg-[var(--vgp-color-secondary)] text-[.85em] md:text-lg"
                alt=""
                @click="showGuide"
                ><svg
                    class="inline w-10 h-10 md:w-5 md:h-5 md:mr-2 text-[var(--vgp-color-primary)]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
                    />
                </svg>
                Hướng dẫn nạp</a
            >
            <a
                href="https://vgplay.vn/dieu-khoan"
                target="_blank"
                class="dark:active:bg-[var(--vgp-color-secondary)]! capitalize dark:active:text-black! flex flex-col md:flex-row items-center justify-center py-2 md:py-4 dark:text-black dark:hover:bg-[var(--vgp-color-secondary)] text-[.85em] md:text-lg"
                alt=""
                ><svg
                    class="inline w-10 h-10 md:w-5 md:h-5 md:mr-2 text-[var(--vgp-color-primary)]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                    data-slot="icon"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
                    ></path>
                </svg>
                Điều khoản</a
            >
        </nav>
    </div>
</template>

<script setup>
import { useAppStore } from "@/stores/useAppStore";
import Guide from "@/Components/Support/Guide.vue";
// import LoginForm from "@/Components/LoginForm.vue";
import vgpImage from "@/assets/images/vgp-logo.webp";
// import { isAuthenticated, user } from "@/stores/auth";

defineProps({
    footer: { type: Object, default: () => ({}) },
});

const store = useAppStore();

const showGuide = () => {
    store.openDrawer(
        "guide",
        Guide,
        {
            option: "all",
        },
        `Hướng dẫn nạp thẻ`,
        "mx-auto! min-h-[70%]!",
        {
            textTransform: "capitalize",
        }
    );
};

const loginToken = (option = null) => {
    if (option && option === "support") {
        if (isAuthenticated.value === true) {
            window.open(
                `https://vgplay.vn/account/token?password=${user.value.user_token}&option=${option}`,
                "_blank"
            );
        } else {
            store.openDrawer(
                "form",
                LoginForm,
                {
                    game: 10,
                },
                `Đăng nhập`
            );
            // window.open(`https://vgplay.vn/support/ticket`, "_blank");
        }
    } else {
        if (isAuthenticated.value === true) {
            window.open(
                `https://vgplay.vn/account/token?password=${user.value.user_token}`,
                "_blank"
            );
        } else {
            store.openDrawer(
                "form",
                LoginForm,
                {
                    game: 10,
                },
                `Đăng nhập`
            );
            // window.open(`https://vgplay.vn/support/account`, "_blank");
        }
    }
};
</script>
