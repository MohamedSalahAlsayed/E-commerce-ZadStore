<template>
  <v-container class="py-10" style="min-height: 60vh">
    <h1 class="text-h3 font-weight-black text-center mb-8 text-primary">
      {{ $t("blog.title") }}
    </h1>
    <p class="text-center text-grey-darken-1 mb-10">
      {{ $t("blog.subtitle") }}
    </p>

    <v-row>
      <v-col cols="12" md="4" v-for="i in 3" :key="i">
        <v-card class="rounded-xl h-100" elevation="2">
          <v-img
            :src="`https://picsum.photos/400/200?random=${i}`"
            height="200"
            cover
          ></v-img>
          <v-card-item>
            <v-card-title class="font-weight-bold px-0">{{
              $t("blog.post_title", { number: i })
            }}</v-card-title>
            <v-card-subtitle class="px-0">{{
              $t("blog.post_date")
            }}</v-card-subtitle>
          </v-card-item>
          <v-card-text class="px-4 pb-0 text-grey-darken-1">
            {{ $t("blog.post_desc") }}
          </v-card-text>
          <v-card-actions class="px-4 pb-4">
            <v-btn
              color="primary"
              variant="text"
              class="font-weight-bold px-0"
              >{{ $t("blog.read_more") }}</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
// Placeholder for Blog logic
</script>
