import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
// vite.config.js
export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: {
      '/admin': {
        target: 'http://localhost/sdftrust/backend/admin',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/admin/, ''),
      },
      // ADD THIS so images show up in your React frontend
      '/uploads': {
        target: 'http://localhost/sdftrust/backend/admin/uploads',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/uploads/, ''),
      },
    },
  },
})