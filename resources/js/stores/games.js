import { defineStore } from "pinia";

export const useGameStore = defineStore("games", {
    state: () => ({
        games: [], // danh sách tất cả game
    }),

    getters: {
        // lấy 1 game theo id
        findById: (state) => {
            return (id) => state.games.find((g) => g.game_id === id) || null;
        },
        // lấy 1 game theo alias
        findByAlias: (state) => {
            return (alias) =>
                state.games.find((g) => g.alias === alias) || null;
        },
    },

    actions: {
        // đồng bộ dữ liệu từ backend (Inertia share)
        setGames(games) {
            this.games = games || [];
        },

        // helper để lấy field cụ thể
        get(key, defaultValue = null) {
            return this.games[key] ?? defaultValue;
        },
    },
});
