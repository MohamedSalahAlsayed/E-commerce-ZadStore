<template>
  <form @submit.prevent="submitForm" class="form">
    <!-- Name -->
    <div class="form-group">
      <label>First Name</label>
      <input type="text" v-model="form.FirstName" />
    </div>
    <div class="form-group">
      <label>Last Name</label>
      <input type="text" v-model="form.LastName" />
    </div>
    <!-- Email -->
    <div class="form-group">
      <label>Email</label>
      <input type="email" v-model="form.email" />
    </div>

    <!-- Password -->
    <div class="form-group">
      <label>Password</label>
      <input type="password" v-model="form.password" />
    </div>

    <!-- Favorite Sports -->
    <div class="form-group">
      <label>Favorite Sports</label>
      <div>
        <label>
          <input
            type="checkbox"
            value="Football"
            v-model="form.sports.Football"
          />
          Football
        </label>
        <label>
          <input
            type="checkbox"
            value="Basketball"
            v-model="form.sports.Basketball"
          />
          Basketball
        </label>
        <label>
          <input type="checkbox" value="Tennis" v-model="form.sports.Tennis" />
          Tennis
        </label>
      </div>
    </div>

    <!-- Gender -->
    <div class="form-group">
      <label>Gender</label>
      <div>
        <label>
          <input type="radio" value="Male" v-model="form.gender" />
          Male
        </label>
        <label>
          <input type="radio" value="Female" v-model="form.gender" />
          Female
        </label>
      </div>
    </div>

    <!-- Academic Year -->
    <div class="form-group">
      <label>Academic Year</label>
      <select v-model="form.year">
        <option disabled value="">Select year</option>
        <option>First Year</option>
        <option>Second Year</option>
        <option>Third Year</option>
        <option>Fourth Year</option>
      </select>
    </div>

    <!-- Notes -->
    <div class="form-group">
      <label>Notes</label>
      <textarea v-model="form.notes"></textarea>
    </div>

    <!-- Submit -->
    <button type="submit">Submit</button>
  </form>
</template>

<script setup>
import { reactive } from "vue";

const form = reactive({
  FirstName: "",
  LastName: "",
  email: "",
  password: "",
  sports: {
    Football: "",
    Basketball: "",
    Tennis: "",
  },
  gender: "",
  year: "",
  notes: "",
});
const st = reactive([]);
const submitForm = () => {
  st.push({ ...form });
  console.log("Form Data:", st);
  resetForm();
};
const resetForm = () => {
  form.firstName = "";
  form.lastName = "";
  form.email = "";
  form.password = "";
  form.sports = [];
  form.gender = "";
  form.year = "";
  form.notes = "";
};
</script>

<style scoped>
.form {
  max-width: 400px;
  margin: auto;
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 5px;
  text-align: justify;
}
.form-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.form {
  background: rgb(var(--v-theme-surface));
  padding: 30px 35px;
  border-radius: 12px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
  color: rgb(var(--v-theme-on-surface));
}

input[type="text"],
input[type="email"],
input[type="password"],
select,
textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.2);
  color: rgb(var(--v-theme-on-surface));
  background: transparent;
  outline: none;
  transition: 0.3s;
}

input:focus,
select:focus,
textarea:focus {
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 0 0 2px rgba(var(--v-theme-primary), 0.2);
}

textarea {
  resize: none;
  min-height: 90px;
}

.form-group div {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

input[type="checkbox"],
input[type="radio"] {
  margin-right: 6px;
}

button {
  width: 100%;
  padding: 12px;
  background: rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-on-primary), #fff);
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}
</style>
