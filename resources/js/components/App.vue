<script setup>
import { ref } from 'vue';

const messaggio = ref("Benvenuti nel nuovo sito di Arsnet!");
const contatore = ref(0);
</script>

<template>
    <div class="p-5 bg-blue-100 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-blue-900">{{ messaggio }}</h1>
        <p class="mt-2">Hai cliccato il pulsante {{ contatore }} volte.</p>
        <button 
            @click="contatore++" 
            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded hover:bg-blue-700"
        >
            Cliccami!
        </button>
    </div>
</template>