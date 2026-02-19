import { reactive } from "vue";
import api from "../services/http";

const authState = reactive({
  user: null,
  isLoading: false,
  initialized: false,
});

async function refreshUser() {
  authState.isLoading = true;

  try {
    const response = await api.get("/auth/me");
    authState.user = response.data?.authenticated ? response.data.user : null;
  } catch (error) {
    authState.user = null;
  } finally {
    authState.isLoading = false;
    authState.initialized = true;
  }
}

async function login(payload) {
  const response = await api.post("/auth/login", payload);
  if (response.data?.success) {
    await refreshUser();
  }

  return response.data;
}

async function logout() {
  try {
    await api.post("/auth/logout");
  } finally {
    authState.user = null;
    authState.initialized = true;
  }
}

export function useAuth() {
  return {
    authState,
    refreshUser,
    login,
    logout,
  };
}
