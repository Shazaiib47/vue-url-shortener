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