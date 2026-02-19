import axios from "axios";

const api = axios.create({
  baseURL: "/api",
  withCredentials: true,
  headers: {
    "X-Requested-With": "XMLHttpRequest",
  },
});

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.getAttribute("content") ?? "";
}

function upsertMeta(name, content) {
  if (!name || !content) {
    return;
  }

  let meta = document.querySelector(`meta[name="${name}"]`);
  if (!meta) {
    meta = document.createElement("meta");
    meta.setAttribute("name", name);
    document.head.appendChild(meta);
  }

  meta.setAttribute("content", content);
}

function syncCsrfMeta(csrf) {
  if (!csrf || typeof csrf !== "object") {
    return;
  }

  if (typeof csrf.token === "string" && csrf.token.length > 0) {
    upsertMeta("csrf-token", csrf.token);
  }

  if (typeof csrf.param === "string" && csrf.param.length > 0) {
    upsertMeta("csrf-param", csrf.param);
  }
}

api.interceptors.request.use((config) => {
  const method = (config.method ?? "get").toLowerCase();
  if (["post", "put", "patch", "delete"].includes(method)) {
    const token = getCsrfToken();
    if (token) {
      config.headers["X-CSRF-Token"] = token;
    }
  }

  return config;
});

api.interceptors.response.use((response) => {
  syncCsrfMeta(response.data?._csrf);
  return response;
});

export default api;
