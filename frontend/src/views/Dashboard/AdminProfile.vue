<template>
  <v-container fluid class="pa-6">
    <v-row>
      <!-- Profile Sidebar Card -->
      <v-col cols="12" lg="4">
        <v-card class="rounded-xl overflow-hidden mb-6" elevation="1">
          <div class="profile-header-bg"></div>
          <v-card-text class="text-center pt-0 profile-card-content">
            <div class="avatar-wrapper mb-4">
              <v-avatar size="120" class="profile-avatar border-4 border-white">
                <v-img :src="profileData.avatar || defaultAvatar" cover></v-img>
              </v-avatar>
              <v-btn
                icon="mdi-camera"
                size="small"
                color="primary"
                class="avatar-edit-btn"
                @click="$refs.avatarInput.click()"
              ></v-btn>
              <input
                type="file"
                ref="avatarInput"
                class="d-none"
                accept="image/*"
                @change="onAvatarChange"
              />
            </div>

            <h2 class="text-h5 font-weight-black mb-1">
              {{ profileData.name }}
            </h2>
            <v-chip color="primary" size="small" class="mb-4 font-weight-bold">
              {{ getRoleName(profileData.role) }}
            </v-chip>

            <v-divider class="mb-4"></v-divider>

            <v-list
              density="compact"
              :class="isRtl ? 'text-right' : 'text-left'"
            >
              <v-list-item class="px-0">
                <template v-slot:prepend>
                  <v-icon color="grey-darken-1" :class="isRtl ? 'ml-3' : 'mr-3'"
                    >mdi-email-outline</v-icon
                  >
                </template>
                <v-list-item-title class="text-subtitle-2 text-grey-darken-1">{{
                  $t("profile_page.email")
                }}</v-list-item-title>
                <v-list-item-subtitle class="font-weight-bold text-body-2">{{
                  profileData.email
                }}</v-list-item-subtitle>
              </v-list-item>

              <v-list-item class="px-0">
                <template v-slot:prepend>
                  <v-icon color="grey-darken-1" :class="isRtl ? 'ml-3' : 'mr-3'"
                    >mdi-phone-outline</v-icon
                  >
                </template>
                <v-list-item-title class="text-subtitle-2 text-grey-darken-1">{{
                  $t("profile_page.phone")
                }}</v-list-item-title>
                <v-list-item-subtitle class="font-weight-bold text-body-2">{{
                  profileData.phone || $t("profile_page.unregistered")
                }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>

        <v-card class="rounded-xl pa-4" elevation="1">
          <h3
            class="text-subtitle-1 font-weight-bold mb-4 d-flex align-center gap-2"
          >
            <v-icon color="primary">mdi-shield-check-outline</v-icon>
            {{ $t("profile_page.permissions_title") }}
          </h3>
          <div class="d-flex flex-wrap gap-2">
            <v-chip
              v-for="perm in profileData.permissions"
              :key="perm"
              size="x-small"
              variant="tonal"
              color="info"
              class="rounded-lg"
            >
              {{ perm }}
            </v-chip>
            <div
              v-if="
                !profileData.permissions || profileData.permissions.length === 0
              "
              class="text-caption text-grey"
            >
              {{ $t("profile_page.super_admin") }} (Full Access)
            </div>
          </div>
        </v-card>
      </v-col>

      <!-- Profile Edit Form -->
      <v-col cols="12" lg="8">
        <v-card class="rounded-xl pa-6" elevation="1">
          <v-tabs v-model="activeTab" color="primary" class="mb-6 border-b">
            <v-tab value="general" class="font-weight-bold">
              <v-icon left :class="isRtl ? 'ml-2' : 'mr-2'"
                >mdi-account-outline</v-icon
              >
              {{ $t("profile_page.general_tab") }}
            </v-tab>
            <v-tab value="security" class="font-weight-bold">
              <v-icon left :class="isRtl ? 'ml-2' : 'mr-2'"
                >mdi-lock-outline</v-icon
              >
              {{ $t("profile_page.security_tab") }}
            </v-tab>
          </v-tabs>

          <v-window v-model="activeTab">
            <!-- General Info Tab -->
            <v-window-item value="general">
              <v-form @submit.prevent="updateProfile">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="profileData.name"
                      :label="$t('profile_page.full_name')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-account"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="profileData.email"
                      :label="$t('profile_page.email')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-email"
                      type="email"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="profileData.phone"
                      :label="$t('profile_page.phone')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-phone"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      v-model="profileData.bio"
                      :label="$t('profile_page.bio')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-text-account"
                      rows="3"
                    ></v-textarea>
                  </v-col>
                </v-row>
                <div class="d-flex justify-end mt-4">
                  <v-btn
                    color="primary"
                    size="large"
                    class="px-8 font-weight-black rounded-lg shadow-btn"
                    type="submit"
                    :loading="loading"
                  >
                    {{ $t("profile_page.save_changes") }}
                  </v-btn>
                </div>
              </v-form>
            </v-window-item>

            <!-- Security Tab -->
            <v-window-item value="security">
              <v-form @submit.prevent="updatePassword">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="passwordForm.current"
                      :label="$t('profile_page.current_password')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-lock-outline"
                      type="password"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="passwordForm.new"
                      :label="$t('profile_page.new_password')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-lock-plus-outline"
                      type="password"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="passwordForm.confirm"
                      :label="$t('profile_page.confirm_password')"
                      variant="outlined"
                      density="comfortable"
                      prepend-inner-icon="mdi-lock-check-outline"
                      type="password"
                      required
                    ></v-text-field>
                  </v-col>
                </v-row>
                <div class="d-flex justify-end mt-4">
                  <v-btn
                    color="primary"
                    size="large"
                    class="px-8 font-weight-black rounded-lg shadow-btn"
                    type="submit"
                    :loading="loading"
                  >
                    {{ $t("profile_page.update_password") }}
                  </v-btn>
                </div>
              </v-form>
            </v-window-item>
          </v-window>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, inject, computed } from "vue";
import { useI18n } from "vue-i18n";
import api from "../../axios";

const { t, locale } = useI18n();
const isRtl = computed(() => locale.value === "ar");

const emitter = inject("emitter");
const activeTab = ref("general");
const loading = ref(false);
const defaultAvatar = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";

const profileData = ref({
  name: "",
  email: "",
  phone: "",
  bio: "",
  avatar: "",
  permissions: [],
  role: "admin",
});

const getRoleName = (role) => {
  if (role === "super_admin") return t("profile_page.super_admin");
  if (role === "staff") return t("profile_page.staff");
  return t("profile_page.admin");
};

const passwordForm = ref({
  current: "",
  new: "",
  confirm: "",
});

const fetchProfile = async () => {
  try {
    const res = await api.get("/user");
    profileData.value = {
      ...res.data,
      permissions: res.data.permissions || [],
    };
  } catch (err) {
    console.error(err);
  }
};

const updateProfile = async () => {
  loading.value = true;
  try {
    await api.put("/user/profile", {
      name: profileData.value.name,
      email: profileData.value.email,
      phone: profileData.value.phone,
      bio: profileData.value.bio,
    });
    emitter.emit("showMessage", {
      text: t("profile_page.update_success"),
      color: "success",
    });
    emitter.emit("updateHeaderData");
  } catch (err) {
    emitter.emit("showMessage", {
      text: err.response?.data?.message || t("dashboard.error_occurred"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

const updatePassword = async () => {
  if (passwordForm.value.new !== passwordForm.value.confirm) {
    emitter.emit("showMessage", {
      text: t("profile_page.password_mismatch"),
      color: "error",
    });
    return;
  }
  loading.value = true;
  try {
    await api.put("/user/profile", {
      current_password: passwordForm.value.current,
      password: passwordForm.value.new,
      password_confirmation: passwordForm.value.confirm,
    });
    emitter.emit("showMessage", {
      text: t("profile_page.password_success"),
      color: "success",
    });
    passwordForm.value = { current: "", new: "", confirm: "" };
  } catch (err) {
    emitter.emit("showMessage", {
      text: err.response?.data?.message || t("dashboard.error_occurred"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

const onAvatarChange = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("avatar", file);

  loading.value = true;
  try {
    const res = await api.post("/user/profile", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    profileData.value.avatar = res.data.user.avatar;
    emitter.emit("showMessage", {
      text: t("profile_page.avatar_success"),
      color: "success",
    });
    emitter.emit("updateHeaderData");
  } catch (err) {
    emitter.emit("showMessage", {
      text: t("profile_page.avatar_error"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

onMounted(fetchProfile);
</script>

<style scoped>
.profile-header-bg {
  height: 120px;
  background: linear-gradient(45deg, rgb(var(--v-theme-primary)), #fb923c);
  width: 100%;
}

.profile-card-content {
  margin-top: -60px;
}

.avatar-wrapper {
  position: relative;
  display: inline-block;
}

.profile-avatar {
  background-color: white;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.avatar-edit-btn {
  position: absolute;
  bottom: 0;
  left: 0;
  border: 2px solid white !important;
}

.gap-2 {
  gap: 8px;
}

.shadow-btn {
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3) !important;
}
</style>
