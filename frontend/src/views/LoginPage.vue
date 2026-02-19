<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "../stores/auth";
import { extractApiMessage } from "../utils/errors";

const router = useRouter();
const { authState, login } = useAuth();

const form = reactive({
  username: "",
  password: "",
  rememberMe: true,
});

const isSubmitting = ref(false);
const errorMessage = ref("");

async function submit() {
  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    const response = await login({ ...form });
    if (!response?.success) {
      errorMessage.value = response?.message ?? "Не удалось выполнить вход.";
      return;
    }

    await router.push("/");
  } catch (error) {
    errorMessage.value = extractApiMessage(error, "Неверный логин или пароль.");
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <section class="row">
    <div class="col-lg-5">
      <h1>Login</h1>

      <Transition name="content-transition" mode="out-in">
        <div v-if="authState.user" key="login-authenticated" class="alert alert-info">
          Вы уже вошли как <strong>{{ authState.user.username }}</strong>.
        </div>

        <form v-else key="login-form" @submit.prevent="submit">
          <Transition name="content-transition">
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>
          </Transition>

          <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input id="username" v-model="form.username" type="text" class="form-control" autofocus />
          </div>

          <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input id="password" v-model="form.password" type="password" class="form-control" />
          </div>

          <div class="form-check mb-3">
            <input id="rememberMe" v-model="form.rememberMe" type="checkbox" class="form-check-input" />
            <label class="form-check-label" for="rememberMe">Remember me</label>
          </div>

          <button class="btn btn-primary" type="submit" :disabled="isSubmitting">
            {{ isSubmitting ? "Logging in..." : "Login" }}
          </button>
        </form>
      </Transition>

      <p class="text-muted mt-4 mb-0">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.
      </p>
    </div>
  </section>
</template>
