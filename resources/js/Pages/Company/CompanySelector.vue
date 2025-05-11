<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    companies: Array
  });

  const selectedCompanyId = ref('');

  function submit() {
    router.post('/select-company', {
      company_id: selectedCompanyId.value
    });
  }
  </script>

<template>
    <div class="company-selector">
      <h2>Válassz egy céget</h2>
      <form @submit.prevent="submit">
        <select v-model="selectedCompanyId">
          <option disabled value="">Válassz...</option>
          <option v-for="company in companies" :key="company.id" :value="company.id">
            {{ company.name }}
          </option>
        </select>
        <button type="submit">Belépés</button>
      </form>
    </div>
  </template>
