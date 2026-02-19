<script setup>
import { reactive, ref } from "vue";
import api from "../services/http";
import { extractApiMessage } from "../utils/errors";

const form = reactive({
  url: "",
});

const isSubmitting = ref(false);
const alertState = reactive({
  visible: false,
  isError: false,
  message: "",
});

const result = reactive({
  visible: false,
  shortUrl: "",
  qrCode: "",
});

function showAlert(message, isError) {
  alertState.visible = true;
  alertState.isError = isError;
  alertState.message = message;
}

function hideResult() {
  result.visible = false;
  result.shortUrl = "";
  result.qrCode = "";
}

async function submit() {
  isSubmitting.value = true;
  alertState.visible = false;
  hideResult();

  try {
    const response = await api.post("/link/create", { url: form.url });
    if (!response.data?.success) {
      showAlert(response.data?.message ?? "Не удалось сократить ссылку.", true);
      return;
    }

    showAlert("Ссылка успешно создана.", false);
    result.visible = true;
    result.shortUrl = response.data.shortUrl;
    result.qrCode = response.data.qrCode;
  } catch (error) {
    showAlert(extractApiMessage(error, "Ошибка сервера. Попробуйте позже."), true);
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <section class="row justify-content-center">
    <div class="col-lg-8">
      <h1 class="h2 mb-3">Сервис коротких ссылок + QR</h1>
      <p class="text-muted mb-4">
        Вставьте URL сайта, нажмите <strong>OK</strong> и получите короткую ссылку с QR-кодом.
      </p>

      <form class="mb-3" @submit.prevent="submit">
        <div class="input-group input-group-lg">
          <input
            v-model="form.url"
            class="form-control"
            type="text"
            placeholder="https://example.com"
            autocomplete="off"
          />
          <button class="btn btn-primary" type="submit" :disabled="isSubmitting">
            {{ isSubmitting ? "..." : "OK" }}
          </button>
        </div>
      </form>

      <Transition name="content-transition">
        <div
          v-if="alertState.visible"
          class="alert"
          :class="alertState.isError ? 'alert-danger' : 'alert-success'"
          role="alert"
        >
          {{ alertState.message }}
        </div>
      </Transition>

      <Transition name="content-transition">
        <div v-if="result.visible" class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-4 text-center mb-3 mb-md-0">
                <img :src="result.qrCode" alt="QR Code" class="img-fluid border rounded p-2" />
              </div>
              <div class="col-md-8">
                <h2 class="h5">Короткая ссылка</h2>
                <a :href="result.shortUrl" target="_blank" rel="noopener noreferrer">
                  {{ result.shortUrl }}
                </a>
                <p class="text-muted mt-2 mb-0">
                  Откройте QR в камере телефона, чтобы перейти по ссылке.
                </p>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </section>
</template>
