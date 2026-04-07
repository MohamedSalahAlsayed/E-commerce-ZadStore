<template>
  <div class="mystudent">
    <h1 v-for="(student, index) in st" :key="index">
      <span>{{ student.name }}</span>
    </h1>
  </div>
</template>
<script>
export default {
  props: ["st"],
};
</script>
