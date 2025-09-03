import { createRouter, createWebHistory } from "vue-router";
import GamePage from "@/Pages/GamePage.vue";
// import PaymentPage from "@/Pages/PaymentPage.vue";

const routes = [
    {
        path: "/:alias",
        name: "GamePage",
        component: GamePage,
        props: true,
    },
    // {
    //     path: "/:alias/payment",
    //     name: "PaymentPage",
    //     component: PaymentPage,
    //     props: true,
    // },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
export default router;
