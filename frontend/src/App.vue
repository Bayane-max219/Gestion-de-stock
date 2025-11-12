<template>
  <div id="app">
    <router-view />
    <MigrationNotice />
  </div>
</template>

<script setup>
import MigrationNotice from './components/MigrationNotice.vue'
</script>

<style>
.temp-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.temp-page h1 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #1f2937;
}

.temp-page p {
  font-size: 1.2rem;
  color: #6b7280;
  margin-bottom: 2rem;
}

.temp-page button {
  background: #10b981;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
}

.temp-page button:hover {
  background: #059669;
}
</style>