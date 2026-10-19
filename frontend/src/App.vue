// this component is responsible for the main page of the app, which includes the form to shorten a URL and the list of recently shortened links
<script setup>
import { ref, onMounted } from 'vue'
import ShortenForm from './components/ShortenForm.vue'
import LinkList from './components/LinkList.vue'
import { getLinks } from './api'

const links = ref([])
const loadError = ref('')

// fetch the existing links from the backend
async function loadLinks() {
  try {
    links.value = await getLinks()
  } catch {
    loadError.value = "Couldn't load links. Is the PHP server running?"
  }
}

// put the new link at the top of the list.
// the api returns the existing link for a repeated url, so remove any duplicate first
function onCreated(link) {
  links.value = [link, ...links.value.filter((l) => l.code !== link.code)]
}

// load the list as soon as the page opens
onMounted(loadLinks)
</script>

// the template is the html that is rendered on the page
<template>
  <main>
    <h1>Shorty</h1>
    <p class="subtitle">Paste a long link, get a short one. Simple.</p>

    <!-- when the form makes a link, add it to the list -->
    <ShortenForm @created="onCreated" />

    <p v-if="loadError" class="error">{{ loadError }}</p>
    <LinkList :links="links" />
  </main>
</template>