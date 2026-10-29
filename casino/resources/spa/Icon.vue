<script setup>
defineProps({ name: { type: String, required: true }, size: { type: [String, Number], default: 20 } });
</script>

<template>
  <svg class="spa-icon" :width="size" :height="size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <template v-if="name === 'home'"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/><path d="M9 21v-6h6v6"/></template>
    <template v-else-if="name === 'games'"><rect x="3" y="7" width="18" height="12" rx="3"/><path d="M8 13h4M10 11v4M16 12h.01M18 14h.01"/></template>
    <template v-else-if="name === 'sports'"><circle cx="12" cy="12" r="8.5"/><path d="m7 6 4 3 4-2 2 4-3 3 1 4-4 1-3-3 1-4-2-3Z"/></template>
    <template v-else-if="name === 'vip'"><path d="m4 7 3 10h10l3-10-5 4-3-6-3 6Z"/><path d="M7 21h10"/></template>
    <template v-else-if="name === 'user'"><circle cx="12" cy="8" r="3.5"/><path d="M5 21a7 7 0 0 1 14 0"/></template>
    <template v-else-if="name === 'slots'"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8M8 12h2m4 0h2M8 17h8"/></template>
    <template v-else-if="name === 'sparkles'"><path d="m12 3 1.6 5.4L19 10l-5.4 1.6L12 17l-1.6-5.4L5 10l5.4-1.6Z"/><path d="m19 16 .7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7Z"/></template>
    <template v-else-if="name === 'catalog'"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></template>
    <template v-else-if="name === 'share'"><circle cx="18" cy="5" r="2"/><circle cx="6" cy="12" r="2"/><circle cx="18" cy="19" r="2"/><path d="m8 11 8-5M8 13l8 5"/></template>
    <template v-else-if="name === 'search'"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/></template>
    <template v-else-if="name === 'close'"><path d="m7 7 10 10M17 7 7 17"/></template>
    <template v-else-if="name === 'arrow-right'"><path d="M5 12h13M14 7l5 5-5 5"/></template>
    <template v-else-if="name === 'gift'"><rect x="4" y="9" width="16" height="11" rx="1"/><path d="M12 9v11M3 9h18M12 9H8.5A2.5 2.5 0 1 1 11 6.5L12 9Zm0 0h3.5A2.5 2.5 0 1 0 13 6.5L12 9Z"/></template>
    <template v-else-if="name === 'wallet'"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M16 12h5v4h-5a2 2 0 0 1 0-4ZM3 9h18"/></template>
    <template v-else-if="name === 'clock'"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></template>
    <template v-else-if="name === 'help'"><circle cx="12" cy="12" r="8.5"/><path d="M9.7 9a2.45 2.45 0 1 1 4.35 1.56c-.94 1.2-2.05 1.48-2.05 2.94"/><path d="M12 17.4h.01"/></template>
    <template v-else-if="name === 'message'"><path d="M20 11.5a7.5 7.5 0 0 1-8 7.48 8.4 8.4 0 0 1-3.3-.7L4 20l1.48-4.18A7.36 7.36 0 0 1 4 11.5a7.5 7.5 0 0 1 8-7.48 7.5 7.5 0 0 1 8 7.48Z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"/></template>
    <template v-else-if="name === 'logout'"><path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"/><path d="m14 8 4 4-4 4M18 12H9"/></template>
    <template v-else-if="name === 'lock'"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></template>
    <template v-else-if="name === 'settings'"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-2.22 2.22-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51v.08h-3.14v-.08a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06-2.22-2.22.06-.06A1.65 1.65 0 0 0 6.81 15a1.65 1.65 0 0 0-1.51-1H5.2v-3.14h.08a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06L8.62 5.76l.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1-1.51v-.08h3.14v.08a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06 2.22 2.22-.06.06a1.65 1.65 0 0 0-.33 1.82 1.65 1.65 0 0 0 1.51 1h.08V14h-.08a1.65 1.65 0 0 0-1.51 1Z"/></template>
  </svg>
</template>
