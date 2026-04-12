<template>
  <v-card class="product-skeleton-card mb-4" elevation="0">
    <v-skeleton-loader
      type="image"
      height="220"
      class="rounded-t-xl"
    ></v-skeleton-loader>
    <v-card-text class="pa-4">
      <v-skeleton-loader
        type="text"
        width="40%"
        class="mb-2"
      ></v-skeleton-loader>
      <v-skeleton-loader
        type="heading"
        width="90%"
        class="mb-4"
      ></v-skeleton-loader>
      <div class="d-flex align-center gap-2 mb-4">
        <v-skeleton-loader
          type="avatar"
          size="16"
          v-for="n in 5"
          :key="n"
        ></v-skeleton-loader>
      </div>
      <div class="d-flex justify-space-between align-center">
        <v-skeleton-loader type="text" width="60%"></v-skeleton-loader>
      </div>
    </v-card-text>
    <v-card-actions class="pa-4 pt-0">
      <v-skeleton-loader
        type="button"
        width="100%"
        class="rounded-lg"
      ></v-skeleton-loader>
    </v-card-actions>
  </v-card>
</template>

<script setup></script>

<style scoped>
.product-skeleton-card {
  border-radius: 16px !important;
  border: 1px solid #eee !important;
}
</style>
