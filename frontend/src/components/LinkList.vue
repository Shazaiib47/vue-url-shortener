// this component is responsible for displaying the list of recently shortened links
<script setup>
import { ref } from 'vue'

// the list of links comes from App.vue
defineProps({
  links: { type: Array, required: true },
})

// remembers which link was just copied so its button can say "Copied"
const copied = ref(null)

// this function is called when the user clicks the "Copy" button
async function copy(link) {
  await navigator.clipboard.writeText(link.short_url)
  copied.value = link.code
  // switch the button text back after 1.5 seconds
  setTimeout(() => (copied.value = null), 1500)
}
</script>

// the template is the html that is rendered on the page
<template>
  <!-- show a message when there's nothing to list yet -->
  <p v-if="!links.length" class="empty">No links yet.</p>

  <!-- list of links -->
  <ul v-else class="link-list">
    <!-- one row per link, keyed by its code so vue can track each one -->
    <li v-for="link in links" :key="link.code">
      <div class="link-info">
        <a :href="link.short_url" target="_blank">{{ link.short_url }}</a>
        <small>{{ link.url }}</small>
      </div>
      <!-- "1 click" vs "2 clicks" -->
      <span class="clicks">
        {{ link.clicks }} {{ link.clicks === 1 ? 'click' : 'clicks' }}
      </span>
      <button @click="copy(link)">
        {{ copied === link.code ? 'Copied' : 'Copy' }}
      </button>
    </li>
  </ul>
</template>