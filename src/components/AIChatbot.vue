<template>
  <div class="ai-chatbot-wrapper" dir="rtl">
    <!-- Chat Window -->
    <v-fade-transition>
      <v-card
        v-if="isOpen"
        class="chat-window elevation-12 rounded-xl"
        width="350"
      >
        <!-- Header -->
        <v-toolbar color="primary" density="compact" class="px-2">
          <v-avatar size="32" class="bg-white elevation-2">
            <v-icon color="primary" size="20">mdi-robot-outline</v-icon>
          </v-avatar>
          <div class="me-3">
            <div
              class="text-subtitle-2 font-weight-bold line-height-1 text-white"
            >
              {{ $t("chatbot.title") }}
            </div>
            <div
              class="text-caption text-white-50"
              style="font-size: 10px; opacity: 0.8"
            >
              <v-icon size="8" color="success" class="me-1">mdi-circle</v-icon>
              {{ $t("chatbot.online") }}
            </div>
          </div>
          <v-spacer></v-spacer>
          <v-btn
            icon
            size="small"
            variant="text"
            color="white"
            @click="isOpen = false"
          >
            <v-icon size="20">mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <!-- Message List -->
        <v-card-text
          ref="chatContainer"
          class="chat-messages pa-4 bg-grey-lighten-5"
        >
          <div
            v-for="(msg, index) in messages"
            :key="index"
            :class="[
              'message-bubble mb-3 d-flex',
              msg.isBot ? 'justify-start' : 'justify-end',
            ]"
          >
            <div
              :class="[
                'pa-3 rounded-lg elevation-1',
                msg.isBot
                  ? 'bot-bubble bg-white'
                  : 'user-bubble bg-primary text-white',
              ]"
            >
              <div class="text-body-2 font-weight-medium mb-1" v-if="msg.isBot">
                <v-icon size="14" class="me-1">mdi-robot</v-icon>
                {{ $t("chatbot.bot_name") }}
              </div>
              <div class="text-body-2">{{ msg.text }}</div>
              <div class="text-right mt-1" style="font-size: 9px; opacity: 0.7">
                {{ msg.time }}
              </div>
            </div>
          </div>
          <div v-if="isTyping" class="message-bubble mb-3 d-flex justify-start">
            <div class="pa-3 rounded-lg bg-white elevation-1 bot-bubble">
              <v-progress-circular
                indeterminate
                size="16"
                width="2"
                color="primary"
                class="me-2"
              ></v-progress-circular>
              <span class="text-caption text-grey">جاري الكتابة...</span>
            </div>
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <!-- Input Area -->
        <v-card-actions class="pa-3 bg-white">
          <v-text-field
            v-model="userInput"
            :placeholder="$t('chatbot.placeholder')"
            variant="solo-filled"
            density="compact"
            hide-details
            rounded="pill"
            flat
            @keyup.enter="sendMessage"
            class="chat-input"
          >
            <template v-slot:append-inner>
              <v-btn
                icon
                size="small"
                color="primary"
                variant="text"
                @click="sendMessage"
                :disabled="!userInput.trim()"
              >
                <v-icon>mdi-send</v-icon>
              </v-btn>
            </template>
          </v-text-field>
        </v-card-actions>

        <!-- Support Shortcut -->
        <div class="pa-2 text-center bg-grey-lighten-4 border-t">
          <v-btn
            variant="text"
            size="x-small"
            color="secondary"
            @click="goToSupport"
            class="text-caption"
          >
            <v-icon size="14" class="me-1">mdi-headphones</v-icon>
            {{ $t("chatbot.support_btn") }}
          </v-btn>
        </div>
      </v-card>
    </v-fade-transition>

    <!-- Chat Toggle Button -->
    <v-badge dot color="success" offset-x="10" offset-y="10" class="bot-badge">
      <v-btn
        icon
        size="large"
        :color="isOpen ? 'grey-darken-3' : 'primary'"
        class="chatbot-toggle elevation-8 mb-2"
        @click="isOpen = !isOpen"
      >
        <v-icon size="30" v-if="!isOpen">mdi-robot-happy</v-icon>
        <v-icon size="30" v-else>mdi-chevron-down</v-icon>
        <v-tooltip activator="parent" location="top" v-if="!isOpen">
          {{ $t("chatbot.title") }}
        </v-tooltip>
      </v-btn>
    </v-badge>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { t } = useI18n();
const router = useRouter();

const isOpen = ref(false);
const userInput = ref("");
const isTyping = ref(false);
const chatContainer = ref(null);

const messages = ref([
  {
    text: t("chatbot.welcome"),
    isBot: true,
    time: new Date().toLocaleTimeString("ar-EG", {
      hour: "2-digit",
      minute: "2-digit",
    }),
  },
]);

const scrollToBottom = async () => {
  await nextTick();
  if (chatContainer.value?.$el) {
    const el = chatContainer.value.$el;
    el.scrollTop = el.scrollHeight;
  }
};

watch(
  messages,
  () => {
    scrollToBottom();
  },
  { deep: true }
);

const sendMessage = async () => {
  if (!userInput.value.trim()) return;

  const userText = userInput.value;
  messages.value.push({
    text: userText,
    isBot: false,
    time: new Date().toLocaleTimeString("ar-EG", {
      hour: "2-digit",
      minute: "2-digit",
    }),
  });

  userInput.value = "";
  isTyping.value = true;

  // Mock AI Logic
  setTimeout(() => {
    let botResponse = "";
    const text = userText.toLowerCase();

    if (
      text.includes("سعر") ||
      text.includes("بكام") ||
      text.includes("فلوس")
    ) {
      botResponse =
        'أسعارنا تنافسية للغاية وهدفنا توفير أفضل ربح للمسوقين! يمكنك تصفح قسم المنتجات لمعرفة سعر كل منتج وعمولتك المقدرة، ولا تنسَ متابعة عروض "الفلاش ديل" الحصرية.';
    } else if (
      text.includes("توصيل") ||
      text.includes("شحن") ||
      text.includes("وقت") ||
      text.includes("متى")
    ) {
      botResponse =
        "نحن نشحن لجميع محافظات مصر! مدة التوصيل تتراوح بين 24 إلى 48 ساعة من تأكيد طلبك، ويمكنك تتبع حالة الشحنة مباشرة من صفحة طلباتك.";
    } else if (
      text.includes("مشكلة") ||
      text.includes("شكوى") ||
      text.includes("عطل") ||
      text.includes("خطأ")
    ) {
      botResponse =
        'نأسف جداً لأي إزعاج! يرجى الضغط على زر "تحدث مع الدعم الفني" بالأسفل لفتح تذكرة دعم، وسيتواصل معك أحد ممثلينا خلال دقائق لحل المشكلة.';
    } else if (
      text.includes("ربح") ||
      text.includes("عمولة") ||
      text.includes("رصيد") ||
      text.includes("سحب")
    ) {
      botResponse =
        "أرباحك في أمان مع ZadStore! يتم إضافة العمولة لمحفظتك فور استلام العميل للطلب، ويمكنك سحب أرباحك عند وصولها للحد الأدنى عبر الوسيلة التي تفضلها.";
    } else if (
      text.includes("سلام") ||
      text.includes("شكرا") ||
      text.includes("مرحبا") ||
      text.includes("اهلا")
    ) {
      botResponse =
        "أهلاً بك في ZadStore! أنا هنا لمساعدتك في أي استفسار يخص المنتجات أو الربح أو النظام. كيف يمكنني خدمتك اليوم؟";
    } else if (
      text.includes("تاجر") ||
      text.includes("مسوق") ||
      text.includes("شغل") ||
      text.includes("عمل")
    ) {
      botResponse =
        "ZadStore هي المنصة الأفضل للمسوقين في مصر! نحن نوفر لك المنتجات والشحن والتحصيل، ومهمتك هي التسويق فقط وجني الأرباح. ابدأ الآن بإضافة منتجات لمتجرك!";
    } else {
      botResponse =
        "عذراً، لم أفهم استفسارك بدقة. هل تود الاستفسار عن (الأسعار، الشحن، الأرباح، أو تبليغ عن مشكلة)؟ يمكنني أيضاً تحويلك للدعم الفني البشري إذا أردت.";
    }

    messages.value.push({
      text: botResponse,
      isBot: true,
      time: new Date().toLocaleTimeString("ar-EG", {
        hour: "2-digit",
        minute: "2-digit",
      }),
    });
    isTyping.value = false;
  }, 1500);
};

const goToSupport = () => {
  isOpen.value = false;
  router.push("/User/UserProfile/MessagePage");
};

onMounted(() => {
  // Initial scroll
  scrollToBottom();
});
</script>

<style scoped>
.ai-chatbot-wrapper {
  position: relative;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.chat-window {
  position: absolute;
  bottom: 70px;
  right: 0;
  max-height: 500px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  transform-origin: bottom right;
}

.chat-messages {
  height: 350px;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(var(--v-theme-primary), 0.2) transparent;
}

.chat-messages::-webkit-scrollbar {
  width: 4px;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: rgba(var(--v-theme-primary), 0.2);
  border-radius: 10px;
}

.message-bubble {
  max-width: 85%;
}

.bot-bubble {
  border-bottom-right-radius: 2px !important;
  color: #2c3e50;
}

.user-bubble {
  border-bottom-left-radius: 2px !important;
}

.chatbot-toggle {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
}

.chatbot-toggle:hover {
  transform: scale(1.1) rotate(5deg);
}

.chat-input :deep(.v-field__input) {
  padding-top: 8px !important;
  padding-bottom: 8px !important;
}

.line-height-1 {
  line-height: 1.2;
}

@media (max-width: 600px) {
  .chat-window {
    width: 280px !important;
    bottom: 60px;
  }
}
</style>
