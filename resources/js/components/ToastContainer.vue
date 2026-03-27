<script setup>
import { ref, onMounted } from 'vue';

const notifiche = ref([]);

// Funzione per aggiungere una notifica
const aggiungiToast = (dipendente, tipo, ora) => {
    const id = Date.now();
    notifiche.value.push({ id, dipendente, tipo, ora });

    // Rimuovi automaticamente dopo 5 secondi
    setTimeout(() => {
        rimuoviToast(id);
    }, 5000);
};

const rimuoviToast = (id) => {
    notifiche.value = notifiche.value.filter(t => t.id !== id);
};

// Rendiamo la funzione disponibile globalmente per il tuo vecchio script
onMounted(() => {
    window.mostraToastVue = aggiungiToast;
});
</script>

<template>
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-3 w-80">
        
        <TransitionGroup name="list">
            <div v-for="t in notifiche" :key="t.id" 
                 :class="['flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border-l-4 bg-white shadow-2xl', 
                          t.tipo === 'entrata' ? 'border-green-500' : 'border-red-500']">
                
                <div :class="['w-9 h-9 rounded-full flex items-center justify-center text-white font-bold', 
                             t.tipo === 'entrata' ? 'bg-green-500' : 'bg-red-500']">
                    {{ t.dipendente.charAt(0).toUpperCase() }}
                </div>

                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ t.dipendente }}</p>
                    <p :class="['text-xs', t.tipo === 'entrata' ? 'text-green-600' : 'text-red-500']">
                        {{ t.tipo === 'entrata' ? '↗ Entrata' : '↙ Uscita' }} alle {{ t.ora }}
                    </p>
                </div>

                <button @click="rimuoviToast(t.id)" class="text-gray-300 hover:text-gray-500 text-xl">✕</button>
            </div>
        </TransitionGroup>

    </div>
</template>

<style scoped>
/* Animazioni di entrata e uscita */
.list-enter-active, .list-leave-active { transition: all 0.5s ease; }
.list-enter-from { opacity: 0; transform: translateX(30px); }
.list-leave-to { opacity: 0; transform: translateX(30px); }
</style>