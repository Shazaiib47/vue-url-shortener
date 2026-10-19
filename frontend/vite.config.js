import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    // anything starting with /api gets sent on to the php server,
    // so the browser thinks it's the same site and we avoid cors issues
    proxy: {
      '/api': 'http://localhost:8000',
    },
  },
})