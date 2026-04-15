import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { createTestingPinia } from "@pinia/testing";
import CartPage from "@/views/User/CartPage.vue";
import { useSettingsStore } from "@/store/Settings";
import { AddInCart } from "@/store/Cart";

// Mock vue-i18n
vi.mock("vue-i18n", () => ({
  useI18n: () => ({
    locale: { value: "ar" },
    t: (key) => key,
  }),
}));

// Mock vue-router
vi.mock("vue-router", () => ({
  useRouter: () => ({
    push: vi.fn(),
  }),
}));

describe("CartPage.vue", () => {
  let pinia;

  beforeEach(() => {
    pinia = createTestingPinia({
      createSpy: vi.fn,
      stubActions: false,
    });
  });

  it("calculates free shipping progress correctly", async () => {
    const settingsStore = useSettingsStore(pinia);
    const cartStore = AddInCart(pinia);

    settingsStore.freeShippingEnabled = true;
    settingsStore.freeShippingThreshold = 1000;

    // Mock cart items
    cartStore.CartItem = [
      {
        id: 1,
        title: "Item 1",
        price: 250,
        quantity: 2,
        discountPercentage: 0,
      },
    ]; // Total = 500

    const wrapper = mount(CartPage, {
      global: {
        plugins: [pinia],
        stubs: {
          // Stubbing Vuetify components for speed
          "v-container": true,
          "v-row": true,
          "v-col": true,
          "v-card": true,
          "v-btn": true,
          "v-progress-linear": true,
          "v-icon": true,
          "v-chip": true,
          "v-divider": true,
        },
      },
    });

    // Subtotal should be 500
    expect(wrapper.vm.cartTotal).toBe(500);
    // Progress should be 50%
    expect(wrapper.vm.freeShippingProgress).toBe(50);
    // shippingReached should be false
    expect(wrapper.vm.freeShippingReached).toBe(false);
  });

  it("detects when free shipping is reached", async () => {
    const settingsStore = useSettingsStore(pinia);
    const cartStore = AddInCart(pinia);

    settingsStore.freeShippingEnabled = true;
    settingsStore.freeShippingThreshold = 1000;

    cartStore.CartItem = [
      {
        id: 1,
        title: "Item 1",
        price: 1000,
        quantity: 1,
        discountPercentage: 0,
      },
    ];

    const wrapper = mount(CartPage, {
      global: {
        plugins: [pinia],
        stubs: {
          "v-container": true,
          "v-row": true,
          "v-col": true,
          "v-card": true,
          "v-btn": true,
          "v-progress-linear": true,
        },
      },
    });

    expect(wrapper.vm.freeShippingReached).toBe(true);
  });
});
