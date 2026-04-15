import { describe, it, expect, vi, beforeEach } from "vitest";
import { setActivePinia, createPinia } from "pinia";
import { useSettingsStore } from "@/store/Settings";
import api from "@/axios";

// Mock the axios instance
vi.mock("@/axios", () => ({
  default: {
    get: vi.fn(),
  },
}));

describe("SettingsStore", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  it("updates state correctly after fetchSettings", async () => {
    const store = useSettingsStore();
    const mockData = {
      storeName: "New ZadStore",
      currency: "USD",
      free_shipping_enabled: "true",
      free_shipping_threshold: "500",
    };

    vi.mocked(api.get).mockResolvedValue({ data: mockData });

    await store.fetchSettings();

    expect(store.storeName).toBe("New ZadStore");
    expect(store.currency).toBe("USD");
    expect(store.freeShippingEnabled).toBe(true);
    expect(store.freeShippingThreshold).toBe(500);
    expect(store.loading).toBe(false);
  });

  it("handles API errors gracefully", async () => {
    const store = useSettingsStore();
    vi.mocked(api.get).mockRejectedValue(new Error("API Error"));

    await store.fetchSettings();

    // Loading should still be false even if it fails
    expect(store.loading).toBe(false);
  });
});
