import { ref, onMounted, watch } from 'vue'

export function useDarkMode() {
  const isDarkMode = ref(false)

  function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value
    updateTheme()
  }

  function updateTheme() {
    // Update DOM
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark')
      localStorage.theme = 'dark'
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.theme = 'light'
    }
  }

  // Initialize theme based on system preference or stored value
  onMounted(() => {
    isDarkMode.value =
      localStorage.theme === 'dark' ||
      (!('theme' in localStorage) &&
        window.matchMedia('(prefers-color-scheme: dark)').matches)
    updateTheme()
  })

  // Watch for system theme changes
  onMounted(() => {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    mediaQuery.addEventListener('change', (e) => {
      if (!('theme' in localStorage)) {
        isDarkMode.value = e.matches
        updateTheme()
      }
    })
  })

  return {
    isDarkMode,
    toggleDarkMode
  }
}