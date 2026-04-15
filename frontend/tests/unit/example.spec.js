import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";

// Simple mock component for testing
const MessageComponent = {
  template: "<p>{{ msg }}</p>",
  props: ["msg"],
};

describe("Example Smoke Test", () => {
  it("renders props.msg when passed", () => {
    const msg = "Hello ZadStore";
    const wrapper = mount(MessageComponent, {
      props: { msg },
    });
    expect(wrapper.text()).toMatch(msg);
  });

  it("basic math works", () => {
    expect(1 + 1).toBe(2);
  });
});
