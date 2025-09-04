<template>
    <n-layout-header
        :class="[
            'sticky top-0 z-50 shadow p-4 mheader grid w-full gap-2 bg-white lg:grid-cols-[[100px],2fr,auto,auto] lg:grid-rows-1 dark:bg-gray-600 items-center',
            router.path === '/' ? 'grid-cols-[1fr,2fr] grid-rows-2 ' : '',
        ]"
        bordered
    >
        <a
            href="/"
            class="col-start-1 row-start-1 w-[100px]"
            style="text-decoration: none; color: inherit"
        >
            <n-image
                width="100"
                src="https://vgplay.vn/images/vgplay-logo.png"
                preview-disabled
            />
        </a>
        <div
            class="col-start-2 row-start-1 flex justify-end lg:col-start-4 lg:row-start-1"
        >
            <div v-if="auth.isLoggedIn">
                <div class="flex items-center cursor-pointer group">
                    <div class="name text-right text-lg font-medium mr-2">
                        <div class="text-xl">{{ user?.username }}</div>
                        <div class="flex items-center">
                            <div class="text-xl text-primary mr-2">
                                {{
                                    new Intl.NumberFormat("vi-VN").format(
                                        wallet?.vxu ?? 0
                                    )
                                }}
                                Vxu
                            </div>
                            <n-icon
                                size="20"
                                class="text-primary rounded-full"
                                @click="refreshBalance"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512"
                                >
                                    <path
                                        d="M65.9 228.5c13.3-93 93.4-164.5 190.1-164.5 53 0 101 21.5 135.8 56.2 .2 .2 .4 .4 .6 .6l7.6 7.2-47.9 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 53.4-11.3-10.7C390.5 28.6 326.5 0 256 0 127 0 20.3 95.4 2.6 219.5 .1 237 12.2 253.2 29.7 255.7s33.7-9.7 36.2-27.1zm443.5 64c2.5-17.5-9.7-33.7-27.1-36.2s-33.7 9.7-36.2 27.1c-13.3 93-93.4 164.5-190.1 164.5-53 0-101-21.5-135.8-56.2-.2-.2-.4-.4-.6-.6l-7.6-7.2 47.9 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 320c-8.5 0-16.7 3.4-22.7 9.5S-.1 343.7 0 352.3l1 127c.1 17.7 14.6 31.9 32.3 31.7S65.2 496.4 65 478.7l-.4-51.5 10.7 10.1c46.3 46.1 110.2 74.7 180.7 74.7 129 0 235.7-95.4 253.4-219.5z"
                                    />
                                </svg>
                            </n-icon>
                        </div>
                    </div>
                    <n-dropdown
                        trigger="click"
                        size="large"
                        placement="bottom-end"
                        :options="options"
                        @select="handleDropdownSelect"
                        :style="{ 'min-width': '250px' }"
                    >
                        <n-icon size="40" color="#FF9138" class="ml-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2 35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"
                                />
                            </svg>
                        </n-icon>
                    </n-dropdown>
                </div>
            </div>
            <n-button
                v-else
                type="primary"
                size="large"
                strong
                @click="showLogin"
            >
                Đăng nhập
            </n-button>
        </div>

        <div
            v-if="page.url === '/'"
            class="col-span-2 row-start-2 lg:col-span-2 lg:col-start-2 lg:row-start-1"
        >
            <n-auto-complete
                v-model:value="searchKeyword"
                :options="autoCompleteOptions"
                placeholder="Tìm kiếm game..."
                :render-label="renderLabel"
                :clearable="true"
                @update:value="handleSearchInput"
                @select="handleSelect"
            />
        </div>
    </n-layout-header>
</template>

<script setup>
import { useAppStore } from "@/stores/useAppStore";
import axios from "@/lib/api";
import LoginForm from "@/Components/Form/LoginForm.vue";
import RoleForm from "@/Components/Form/RoleForm.vue";
// import History from "@/Components/History.vue";
import { h, ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { NTag, NText, useMessage } from "naive-ui";
import { useAuthStore } from "@/stores/auth";
import logoImage from "@/assets/images/vgplaylogo.webp";

const store = useAppStore();
const page = usePage();
const message = useMessage();
const auth = useAuthStore();

const user = auth?.user?.info;
const wallet = auth?.user?.wallet;
const refreshing = ref(false);
const games = page.props?.games;

const searchKeyword = ref("");

const autoCompleteOptions = games.map((g) => ({
    name: g.name,
    value: g.game_id,
    alias: g.alias,
    flags: g.flags,
    icon: g.icon
}));

console.log("games: ", games);

function showLogin() {
    store.openDrawer(
        "form",
        LoginForm,
        {
            game: {
                value: 10,
            },
        },
        "Đăng nhập"
    );
}

// Tìm kiếm
const handleSearchInput = (val) => {
    searchKeyword.value = val;
    if (!val) {
        autoCompleteOptions.value = [];
        return;
    }
    const keyword = val.toLowerCase();

    autoCompleteOptions.value = games?.filter((game) =>
        game.name.toLowerCase().includes(keyword)
    );
};

const renderLabel = (option) => {
    const tags = [];

    const tagMapping = {
        enable_hot_game: { text: "Hot", type: "error" },
        enable_new_game: { text: "Mới", type: "success" },
    };

    if (option.flags?.flags) {
        Object.entries(tagMapping).forEach(([key, { text, type }]) => {
            if (option.flags?.flags[key] === true) {
                tags.push(
                    h(
                        NTag,
                        {
                            size: "small",
                            type,
                            class: "ml-2 align-middle",
                        },
                        { default: () => text }
                    )
                );
            }
        });
    }

    return h("div", { class: "flex justify-between items-center w-full" }, [
        h("div", { class: "flex items-center" }, [
            h("img", {
                src: option.icon || "https://via.placeholder.com/20",
                alt: "Game Icon",
                class: "w-15 h-15 rounded-lg p-2 mr-2 inline-block object-cover",
            }),
            h("span", { class: "align-middle text-lg" }, option.name),
        ]),
        h("div", { class: "flex items-center" }, tags),
    ]);
};

// const handleSelect = (val) => {
//     const selectedGame = games.find((game) => game.game_id === val);
//     if (auth.isLoggedIn === true) {
//         if (selectedGame.flags?.flags?.check_roles === false) {
//             console.log("header: ", selectedGame.alias);

//             router.visit(`/${selectedGame.alias}`, {
//                 preserveState: true,
//                 onSuccess: () => {
//                     console.log(route.path);
//                 },
//             });
//         } else {
//             store.openDrawer(
//                 "form",
//                 RoleForm,
//                 { game: selectedGame, type: "click" },
//                 `Chọn nhân vật`
//             );
//         }
//     } else {
//         store.openDrawer(
//             "form",
//             LoginForm,
//             { game: selectedGame },
//             `Đăng nhập`
//         );
//     }
// };

// Tài khoản

const handleSelect = (val, option) => {
    const selectedGame = games.find((g) => g.game_id === val);

    if (!selectedGame) return;

    if (auth.isLoggedIn) {
        if (selectedGame.flags?.flags.check_roles === false) {
            router.visit(`/${selectedGame.alias}`, {
                preserveState: true,
                onSuccess: () => {
                    console.log("Đã chuyển đến:", selectedGame.alias);
                },
            });
        } else {
            store.openDrawer(
                "form",
                RoleForm,
                { game: selectedGame, type: "click" },
                `Chọn nhân vật`
            );
        }
    } else {
        store.openDrawer(
            "form",
            LoginForm,
            { game: selectedGame },
            `Đăng nhập`
        );
    }
};

const renderAccountHeader = () => {
    return h(
        "div",
        {
            style: "display: flex; align-items: center; padding: 8px 12px;",
        },
        [
            h(
                "svg",
                {
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 512 512",
                    style: "width: 35px; height: 35px; margin-right: .5em",
                    fill: "#FF9138",
                },
                [
                    h("path", {
                        d: "M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2 35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z",
                    }),
                ]
            ),
            h("div", null, [
                h("div", null, [
                    h(
                        NText,
                        {
                            depth: 2,
                            class: "font-medium text-md",
                        },
                        {
                            default: () =>
                                user?.username + " - ID: " + user?.id,
                        }
                    ),
                ]),
                h("div", null, [
                    h(
                        NText,
                        {
                            depth: 2,
                            style: "color: #FF9138",
                            class: "font-medium text-lg text-primary",
                        },
                        {
                            default: () =>
                                new Intl.NumberFormat("vi-VN").format(
                                    wallet?.vxu
                                ) + " Vxu",
                        }
                    ),
                ]),
            ]),
        ]
    );
};

const createHoverItem = (
    label,
    icon,
    onClick,
    showCondition = () => true,
    centerOnly = false,
    noHover = false
) => {
    return {
        key: label,
        type: "render",
        render: () => {
            if (!showCondition()) return null;

            const isImageIcon =
                typeof icon === "string" && icon.endsWith(".webp");

            let iconElement;

            if (isImageIcon) {
                iconElement = h("img", {
                    src: icon,
                    class: "w-1/2 object-contain mr-2",
                    alt: label,
                });
            } else if (Array.isArray(icon)) {
                iconElement = h(
                    "svg",
                    {
                        xmlns: "http://www.w3.org/2000/svg",
                        viewBox: "0 0 32 32",
                        fill: "currentColor",
                        class:
                            "w-6 h-6 text-gray-600" +
                            (noHover ? "" : " group-hover:text-white") +
                            (label ? " mr-2" : ""),
                    },
                    icon.map((d) => h("path", { d }))
                );
            }

            const containerClasses = [
                "flex items-center px-3 py-2 cursor-pointer transition-colors duration-200",
                centerOnly ? "justify-center" : "justify-start",
                noHover ? "" : "group hover:bg-orange-500",
            ]
                .filter(Boolean)
                .join(" ");

            const labelClass = noHover
                ? "text-gray-800"
                : "text-gray-800 group-hover:text-white";

            return h(
                "div",
                {
                    class: containerClasses,
                    onClick: onClick,
                },
                [
                    iconElement,
                    label ? h("span", { class: labelClass }, label) : null,
                ]
            );
        },
    };
};

const createDividerIf = (conditionFn, key) => ({
    key,
    type: "render",
    render: () =>
        conditionFn()
            ? h("div", { class: "h-px bg-gray-200 my-2 mx-2" })
            : null,
});

const options = [
    {
        key: "header",
        type: "render",
        render: renderAccountHeader,
    },
    {
        key: "header-divider",
        type: "divider",
    },
    // createHoverItem(
    //     "Nạp Vxu",
    //     [
    //         "M28 8H4V5h22V3H4a2 2 0 0 0-2 2v21a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2zM4 26V10h24v3h-8a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h8v3zm24-11v6h-8v-6z",
    //     ],
    //     () => goToVxu()
    // ),
    // {
    //     type: "divider",
    //     key: "d1",
    // },
    createHoverItem(
        "Lịch sử giao dịch",
        [
            "M25.7 9.3l-7-7c-.2-.2-.4-.3-.7-.3H8c-1.1 0-2 .9-2 2v24c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V10c0-.3-.1-.5-.3-.7zM18 4.4l5.6 5.6H18V4.4zM24 28H8V4h8v6c0 1.1.9 2 2 2h6v16z",
        ],
        () => showHistory(),
        () => window.location.pathname !== "/"
    ),
    createDividerIf(
        () => window.location.pathname === "/game",
        "divider-after-history"
    ),
    createHoverItem(
        "Quản lý tài khoản",
        [
            "M26.749 24.93A13.99 13.99 0 1 0 2 16a13.899 13.899 0 0 0 3.251 8.93l-.02.017c.07.084.15.156.222.239c.09.103.187.2.28.3c.28.304.568.596.87.87c.092.084.187.162.28.242c.32.276.649.538.99.782c.044.03.084.069.128.1v-.012a13.901 13.901 0 0 0 16 0v.012c.044-.031.083-.07.128-.1c.34-.245.67-.506.99-.782c.093-.08.188-.159.28-.242c.302-.275.59-.566.87-.87c.093-.1.189-.197.28-.3c.071-.083.152-.155.222-.24zM16 8a4.5 4.5 0 1 1-4.5 4.5A4.5 4.5 0 0 1 16 8zM8.007 24.93A4.996 4.996 0 0 1 13 20h6a4.996 4.996 0 0 1 4.993 4.93a11.94 11.94 0 0 1-15.986 0z",
        ],
        () => showProfile()
    ),
    {
        type: "divider",
        key: "d1",
    },
    createHoverItem(
        "Đăng xuất",
        [
            "M6 30h12a2.002 2.002 0 0 0 2-2v-3h-2v3H6V4h12v3h2V4a2.002 2.002 0 0 0-2-2H6a2.002 2.002 0 0 0-2 2v24a2.002 2.002 0 0 0 2 2z",
            "M20.586 20.586L24.172 17H10v-2h14.172l-3.586-3.586L22 10l6 6l-6 6l-1.414-1.414z",
        ],
        () => performLogout()
    ),
    {
        type: "divider",
        key: "d1",
    },
    createHoverItem("Account", logoImage, null, () => true, true, true),
];

const handleDropdownSelect = (key) => {
    message.info(String(key));
};

const goToVxu = () => {
    router.visit("/vxu/payment");
};

const showHistory = () => {
    store.openDrawer(
        "history",
        History,
        { game: page.props?.game },
        `Lịch sử giao dịch`,
        "mx-auto! min-h-[70%]!"
    );
};

// const refreshBalance = async () => {
//     try {
//         await axios.get(`/balance`);
//         router.reload({ only: ["userData", "balance"] });
//         message.success("Làm mới ví thành công");
//     } catch (err) {
//         console.error("Lỗi lấy lịch sử:", err);
//     } finally {
//         // loading.value = false;
//     }
// };

const refreshBalance = async () => {
    if (refreshing.value) return;
    refreshing.value = true;
    try {
        await axios.get("/balance");

        await router.reload({ only: ["auth"], preserveScroll: true });

        message.success("Làm mới ví thành công");
    } catch (err) {
        if (err?.response?.status === 401) {
            message.error("Bạn chưa đăng nhập");
        } else {
            message.error("Không thể làm mới ví");
            console.error("Refresh balance error:", err);
        }
    } finally {
        refreshing.value = false;
    }
};

const showProfile = () => {
    window.open(
        `https://vgplay.vn/account/token?password=${user.value.user_token}`,
        "_blank"
    );
};

const performLogout = async () => {
    try {
        await auth.logout();
        router.visit("/");
        message.success("Đã đăng xuất");
    } catch (error) {
        message.error("Đăng xuất thất bại");
    }
};
</script>
