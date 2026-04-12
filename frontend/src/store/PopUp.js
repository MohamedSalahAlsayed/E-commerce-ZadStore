import { defineStore } from "pinia";

export const usePopUpQuickView = defineStore("usePopUpQuickView", {
  state: () => ({
    QuickVeiw: false,
    productId: null,
  }),
  actions: {
    open(id) {
      if (!localStorage.getItem("auth_token")) {
        import("@/router").then((r) => r.default.push("/Auth/LogIn"));
        return;
      }
      this.productId = id;
      this.QuickVeiw = true;
    },
    close() {
      this.QuickVeiw = false;
      this.productId = null;
    },
  },
});
