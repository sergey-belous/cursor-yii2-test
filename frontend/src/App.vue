<script setup>
import { computed, onMounted } from "vue";
import { RouterLink, RouterView } from "vue-router";
import { useAuth } from "./stores/auth";

const { authState, refreshUser, logout } = useAuth();

const isAuthenticated = computed(() => authState.user !== null);
const username = computed(() => authState.user?.username ?? "");

onMounted(() => {
  refreshUser();
});

async function onLogout() {
  await logout();
}
</script>

<template>
  <div class="d-flex flex-column min-vh-100">
    <header>
      <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
          <RouterLink class="navbar-brand" to="/">URL Shortener</RouterLink>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#app-navbar"
            aria-controls="app-navbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

          <div id="app-navbar" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
              <li class="nav-item">
                <RouterLink class="nav-link" to="/" exact-active-class="active">Home</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/about" exact-active-class="active">About</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/contact" exact-active-class="active">Contact</RouterLink>
              </li>
            </ul>

            <ul class="navbar-nav">
              <li v-if="!isAuthenticated" class="nav-item">
                <RouterLink class="nav-link" to="/login" exact-active-class="active">Login</RouterLink>
              </li>
              <li v-else class="nav-item">
                <button type="button" class="btn btn-link nav-link" @click="onLogout">
                  Logout ({{ username }})
                </button>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main class="flex-grow-1 py-4">
      <div class="container">
        <RouterView v-slot="{ Component, route }">
          <Transition name="page-transition" mode="out-in">
            <component :is="Component" :key="route.fullPath" />
          </Transition>
        </RouterView>
      </div>
    </main>

    <footer class="py-3 bg-light border-top">
      <div class="container text-muted d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; My Company {{ new Date().getFullYear() }}</span>
        <span>Powered by Yii2 + Vue 3</span>
      </div>
    </footer>
  </div>
</template>
