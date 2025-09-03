<template>
    <div v-if="isLoggedIn">
        <div>VGP ID: {{ user?.vgp_id }}</div>
        <div>Username: {{ user?.username }}</div>
        <div>Game ID: {{ user?.game_id }}</div>
        <div v-if="user?.user_token">Token: {{ user.user_token }}</div>
    </div>
    <div class="mt-4">
        <div class="grid md:grid-cols-8 gap-4">
            <n-carousel
                :space-between="20"
                class="md:col-span-6 cursor-pointer"
                autoplay
                :loop="false"
            >
                <div
                    v-for="slide in payments?.payment_site_sliders"
                    :key="slide"
                >
                    <img class="carousel-img" :src="slide" />
                </div>
            </n-carousel>
            <div class="hidden md:block md:col-span-2">
                <div class="grid grid-cols-2 gap-2 mr-4">
                    <div
                        class="mcard text-center bg-white shadow-md rounded-xl p-4 border border-primary"
                    >
                        <n-icon :size="40" class="text-primary transition">
                            <GiftSharp />
                        </n-icon>
                        <div class="text-[11px]">
                            Ưu đãi <br />
                            hấp dẫn
                        </div>
                    </div>
                    <div
                        class="mcard text-center bg-white shadow-md rounded-xl py-4 border border-primary"
                    >
                        <n-icon :size="40" class="text-primary transition">
                            <TrophyOutline />
                        </n-icon>
                        <div class="text-[11px]">
                            Vật phẩm <br />
                            độc quyền
                        </div>
                    </div>
                    <div
                        class="mcard text-center bg-white shadow-md rounded-xl p-4 border border-primary"
                    >
                        <n-icon :size="40" class="text-primary transition">
                            <CardOutline />
                        </n-icon>
                        <div class="text-[11px]">
                            Thanh toán <br />
                            đơn giản
                        </div>
                    </div>
                    <div
                        class="mcard text-center bg-white shadow-md rounded-xl p-4 border border-primary"
                    >
                        <n-icon :size="40" class="text-primary transition">
                            <CashOutline />
                        </n-icon>
                        <div class="text-[11px]">Giá tốt nhất</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-4 pb-10">
        <!-- <h1 class="text-xl font-medium uppercase">Game của bạn</h1>

        <n-carousel
            :slides-per-view="slidesPerView"
            :space-between="spaceBetween"
            :loop="true"
            draggable
            show-arrow
            autoplay
            class="mt-4"
        >
            <template #arrow="{ prev, next }">
                <div class="custom-arrow">
                    <button
                        type="button"
                        class="custom-arrow--left"
                        @click="prev"
                    >
                        <n-icon
                            :size="24"
                            class="text-gray-400 hover:text-orange-500 transition"
                        >
                            <ArrowBack />
                        </n-icon>
                    </button>
                    <button
                        type="button"
                        class="custom-arrow--right"
                        @click="next"
                    >
                        <n-icon
                            :size="24"
                            class="text-gray-400 hover:text-orange-500 transition"
                        >
                            <ArrowForward />
                        </n-icon>
                    </button>
                </div>
            </template>

            <n-card
                v-for="(game, index) in hotGames"
                :key="index"
                header-class="p-2!"
                :title="
                    () =>
                        h(
                            'div',
                            {
                                class: [
                                    'line-clamp-2 text-center  text-xs md:text-md lg:text-lg transition-colors duration-200',
                                    hoveredCardIndex === index
                                        ? 'text-orange-500'
                                        : 'text-gray-800',
                                ],
                            },
                            game.label
                        )
                "
                :bordered="false"
                @click="() => handleGameClick(game)"
                class="cursor-pointer"
                @mouseenter="hoveredCardIndex = index"
                @mouseleave="hoveredCardIndex = null"
            >
                <template #cover>
                    <div>
                        <img :src="game.data.icon" />
                    </div>
                    <div class="mtag absolute top-0 right-0">
                        <n-tag
                            :bordered="false"
                            :color="{
                                color: 'red',
                                textColor: '#fff',
                                borderColor: 'red',
                            }"
                        >
                            Hot
                        </n-tag>
                    </div>
                </template>
            </n-card>
        </n-carousel> -->
        <h2 class="text-xl font-medium uppercase">Nạp Game</h2>
        <div class="grid grid-cols-3 lg:grid-cols-4 gap-6 mt-4">
            <n-card
                v-for="(game, index) in gameStore.games"
                :key="index"
                header-class="p-2!"
                :title="
                    () =>
                        h(
                            'div',
                            {
                                class: [
                                    'line-clamp-2 text-center font-medium text-[14px] lg:text-lg transition-colors duration-200',
                                    hoveredCardIndex === index
                                        ? 'text-orange-500'
                                        : 'text-gray-800',
                                ],
                            },
                            game.name
                        )
                "
                :bordered="false"
                @click="() => handleGameClick(game)"
                class="cursor-pointer"
                @mouseenter="hoveredCardIndex = index"
                @mouseleave="hoveredCardIndex = null"
            >
                {{}}
                <template #cover>
                    <div>
                        <img :src="game.icon" />
                    </div>
                    <div
                        v-if="
                            game.flags?.flags['enable_hot_game'] === true &&
                            game.flags?.flags['enable_new_game'] === false
                        "
                        class="mtag absolute top-0 right-[-12px] shadow-tag"
                    >
                        <div class="flex relative">
                            <div class="left-tag">
                                <div class="triangle-topright hot"></div>
                                <div class="triangle-bottomright hot"></div>
                            </div>
                            <div
                                class="content py-0.5 px-5 text-white bg-[red] rounded-tr-lg"
                            >
                                HOT
                            </div>
                            <div
                                class="triangle-topleft absolute bottom-[-15px] z-2 right-0 hot"
                            ></div>
                        </div>
                    </div>
                    <div
                        v-if="game?.flags?.flags['enable_new_game'] === true"
                        class="mtag absolute top-0 right-[-12px] shadow-tag"
                    >
                        <div class="flex relative">
                            <div class="left-tag">
                                <div class="triangle-topright new"></div>
                                <div class="triangle-bottomright new"></div>
                            </div>
                            <div
                                class="content py-0.5 px-5 text-white bg-[green] rounded-tr-lg"
                            >
                                New
                            </div>
                            <div
                                class="triangle-topleft absolute bottom-[-15px] z-2 right-0 new"
                            ></div>
                        </div>
                    </div>
                </template>
            </n-card>
            <!-- <n-card
                    header-class="p-2!"
                    :title="
                        () =>
                            h(
                                'div',
                                {
                                    class: [
                                        'line-clamp-2 text-center text-xs md:text-md lg:text-lg transition-colors duration-200',
                                        hoveredCardIndex === index
                                            ? 'text-orange-500'
                                            : 'text-gray-800',
                                    ],
                                },
                                'Nạp Vxu'
                            )
                    "
                    :bordered="false"
                    @click="() => goToTopup()"
                    class="cursor-pointer col-start-1 row-start-1"
                    @mouseenter="hoveredCardIndex = index"
                    @mouseleave="hoveredCardIndex = null"
                >
                    <template #cover>
                        <div class="rounded-4xl overflow-hidden">
                            <img :src="vxuImage" />
                        </div>
                    </template>
                </n-card> -->
        </div>
    </div>
</template>

<script setup>
import { ref, h, computed, onMounted, onUnmounted, watch } from "vue";
import { useSettingStore } from "@/stores/settings";
import { useGameStore } from "@/stores/games";
import { useAppStore } from "@/stores/useAppStore";
import { storeToRefs } from 'pinia';
import { useAuthStore } from "@/stores/auth";
import { usePage } from "@inertiajs/vue3";
import LoginForm from "@/Components/Form/LoginForm.vue";

const { user, isLoggedIn } = storeToRefs(useAuthStore());

// games được inject từ backend qua Inertia
import {
    ArrowBack,
    ArrowForward,
    GiftSharp,
    TrophyOutline,
    CardOutline,
    CashOutline,
} from "@vicons/ionicons5";

const page = usePage();
const settingStore = useSettingStore();
const gameStore = useGameStore();
const store = useAppStore();
const auth = useAuthStore();
const payments = settingStore.settings?.payment;
const hoveredCardIndex = ref(null);

gameStore.setGames(page.props.games);

function handleGameClick(game) {
    // console.log(`auth.loginType: `, auth.loginTyp);
    console.log(`=game: `, game);
    console.log(`auth: `, auth?.user);

    // 1) Chưa đăng nhập → mở form login
    if (!auth.isLoggedIn) {
        store.openDrawer("form", LoginForm, { game }, "Đăng nhập");
        return;
    }

    // 2) Nếu login bằng Facebook và khác game đang ràng buộc → cảnh báo đăng xuất
    const isFacebook = auth?.isFacebook ?? auth.loginType === "facebook";
    console.log(auth.fbGameAlias, auth.fbGameId);
    console.log(`isFacebook:`, isFacebook);

    const isDifferentGame =
        isFacebook &&
        auth.fbGameId != null &&
        String(auth.fbGameId) !== String(game.value);

    if (isDifferentGame) {
        store.openDrawer(
            "form",
            LogoutWarning,
            {
                current: { id: auth.fbGameId, alias: auth.fbGameAlias },
                target: game,
                onConfirm: async () => {
                    // dùng tiện ích đã có
                    await performLogout();
                    // sau khi logout → mở form login cho game mới
                    store.openDrawer("form", LoginForm, { game }, "Đăng nhập");
                },
            },
            "Thông báo",
            "mx-auto! max-h-[20%]"
        );
        return;
    }

    // 3) Hợp lệ → điều hướng / chọn nhân vật như cũ
    if (game?.flags?.flags?.check_roles === 0) {
        router.visit(`/${game.alias}/payment`, {
            preserveState: true,
            onStart: () => store.loading?.show?.(), // nếu bạn có loader trong useAppStore
            onFinish: () => store.loading?.hide?.(),
        });
    } else {
        store.openDrawer(
            "form",
            RoleForm,
            { game, type: "click" },
            `Chọn nhân vật`
        );
    }
}
</script>

<style></style>
