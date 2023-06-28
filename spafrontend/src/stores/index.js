import { defineStore } from "pinia";

export const useHelperStore = defineStore('helper', {
    state: () => ({
        home_index_view: true,
        profile_data: null,
    }),
    getters: {
        homeIndexView: (state) => state.home_index_view,
    },
    actions: {
        async getLoggedUser() {
            this.user_data = {
                "name": "Super Admin",
                "email": "admin@vue.com",
                "user_type": "Admin"
            }
        },
        async testHide() {
            this.home_index_view = !this.homeIndexView;
        },
        
    }
})