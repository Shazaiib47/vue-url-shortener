// this component is responsible for the form that lets the user enter a URL to shorten
<script setup>
import { ref } from 'vue'
import { shortenUrl } from '../api'

// lets the parent (App.vue) know when a new link has been made
const emit = defineEmits(['created'])

const url = ref('')
const error = ref('')
const loading = ref(false)

// this function is called when the user submits the form
async function submit() {
  error.value = ''
  loading.value = true

  // call the api to shorten the url, and emit the new link to the parent
  try {
    const link = await shortenUrl(url.value)
    emit('created', link)
    // clear the box ready for the next one
    url.value = ''
  } catch (e) {
    // show the message from the api, e.g. invalid url
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

// the template is the html that is rendered on the page
<template>
  <!-- .prevent stops the page reloading on submit -->
  <form class="shorten-form" @submit.prevent="submit">
    <input
      v-model="url"
      type="url"
      placeholder="https://example.com/a/really/long/link"
      required
    />
    <!-- disabled while waiting so it can't be double submitted -->
    <button :disabled="loading">
      {{ loading ? 'Shortening…' : 'Shorten' }}
    </button>
  </form>
  <p v-if="error" class="error">{{ error }}</p>
</template>