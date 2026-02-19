<script setup>
import { computed, reactive, ref } from "vue";
import api from "../services/http";
import { extractApiMessage } from "../utils/errors";

const submitted = ref(false);
const isSubmitting = ref(false);
const captchaVersion = ref(Date.now());
const errorMessage = ref("");

const form = reactive({
  name: "",
  email: "",
  subject: "",
  body: "",
  verifyCode: "",
});

const captchaUrl = computed(() => `/site/captcha?v=${captchaVersion.value}`);

function refreshCaptcha() {
  captchaVersion.value = Date.now();
}

async function submit() {
  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    const response = await api.post("/contact/submit", { ...form });
    if (!response.data?.success) {
      errorMessage.value = response.data?.message ?? "Не удалось отправить форму.";
      refreshCaptcha();
      return;
    }

    submitted.value = true;
  } catch (error) {
    errorMessage.value = extractApiMessage(error, "Ошибка отправки формы.");
    refreshCaptcha();
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <section class="row">
    <div class="col-lg-6">
      <h1>Contact</h1>

      <Transition name="content-transition" mode="out-in">
        <div v-if="submitted" key="contact-submitted" class="alert alert-success">
          Thank you for contacting us. We will respond to you as soon as possible.
        </div>

        <form v-else key="contact-form" @submit.prevent="submit">
          <p class="text-muted">
            If you have business inquiries or other questions, please fill out the following form.
          </p>

          <Transition name="content-transition">
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>
          </Transition>

          <div class="mb-3">
            <label class="form-label" for="contact-name">Name</label>
            <input id="contact-name" v-model="form.name" type="text" class="form-control" autocomplete="name" />
          </div>

          <div class="mb-3">
            <label class="form-label" for="contact-email">Email</label>
            <input id="contact-email" v-model="form.email" type="email" class="form-control" autocomplete="email" />
          </div>

          <div class="mb-3">
            <label class="form-label" for="contact-subject">Subject</label>
            <input id="contact-subject" v-model="form.subject" type="text" class="form-control" />
          </div>

          <div class="mb-3">
            <label class="form-label" for="contact-body">Body</label>
            <textarea id="contact-body" v-model="form.body" rows="6" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label" for="contact-verify-code">Verification Code</label>
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <img :src="captchaUrl" alt="Captcha" class="border rounded" />
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="refreshCaptcha">
                Refresh
              </button>
            </div>
            <input id="contact-verify-code" v-model="form.verifyCode" type="text" class="form-control mt-2" />
          </div>

          <button class="btn btn-primary" type="submit" :disabled="isSubmitting">
            {{ isSubmitting ? "Submitting..." : "Submit" }}
          </button>
        </form>
      </Transition>
    </div>
  </section>
</template>
