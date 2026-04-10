<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
});

const form = useForm({
    relato: '',
    resumo: '',
    local: {
        uf: '',
        municipio: '',
        endereco_manual: '',
    }
});

const isSuccess = ref(false);
const protocolo = ref('');

const submit = () => {
    form.post(route('denuncia.store'), {
        preserveScroll: true,
        onSuccess: (page) => {
            isSuccess.value = true;
            protocolo.value = page.props.flash?.protocolo || page.props.session?.protocolo || 'N/A';
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Denúncia Anônima" />

    <div class="min-h-screen bg-gray-900 text-gray-100 flex items-center justify-center p-4 selection:bg-[#FF2D20] selection:text-white relative overflow-hidden">
        
        <!-- Blobs / Glassmorphism Background -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/30 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/30 blur-[100px] rounded-full pointer-events-none"></div>

        <nav v-if="canLogin" class="absolute top-0 right-0 p-6 z-10">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-md px-4 py-2 text-white ring-1 ring-white/20 hover:bg-white/10 transition"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="rounded-md px-4 py-2 text-white ring-1 ring-white/20 hover:bg-white/10 transition"
                >
                    Log in Interno
                </Link>
            </template>
        </nav>

        <div class="relative z-10 w-full max-w-2xl bg-white/5 backdrop-blur-xl ring-1 ring-white/10 rounded-2xl p-8 sm:p-12 shadow-2xl">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">Portal de Denúncias</h1>
                <p class="text-gray-400">Canal seguro, anônimo e protegido.</p>
            </div>

            <div v-if="isSuccess" class="bg-green-500/10 border border-green-500/30 rounded-xl p-6 mb-6 text-center animate-pulse">
                <svg class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-bold text-green-400 mb-2">Denúncia Registrada com Sucesso!</h2>
                <p class="text-gray-300 mb-4">Anote seu protocolo para acompanhamento futuro:</p>
                <div class="bg-gray-900/50 rounded-lg p-4 font-mono text-2xl tracking-widest text-white border border-gray-700">
                    {{ $page.props.flash?.protocolo || protocolo }}
                </div>
                <button @click="isSuccess = false" class="mt-6 text-sm text-gray-400 hover:text-white transition">Nova Denúncia</button>
            </div>

            <form v-else @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="relato" class="block text-sm font-medium text-gray-300 mb-2">Relato da Denúncia *</label>
                    <textarea 
                        id="relato"
                        v-model="form.relato"
                        rows="5"
                        class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Descreva com o máximo de detalhes o que aconteceu..."
                        required
                    ></textarea>
                    <div v-if="form.errors.relato" class="mt-2 text-sm text-red-500">{{ form.errors.relato }}</div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="uf" class="block text-sm font-medium text-gray-300 mb-2">Estado (UF)</label>
                        <select 
                            id="uf" 
                            v-model="form.local.uf"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                            <option value="">Selecione...</option>
                            <option value="SP">São Paulo</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="MG">Minas Gerais</option>
                        </select>
                    </div>

                    <div>
                        <label for="municipio" class="block text-sm font-medium text-gray-300 mb-2">Município</label>
                        <input 
                            type="text" 
                            id="municipio" 
                            v-model="form.local.municipio"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Ex: Campinas"
                        >
                    </div>
                </div>

                <div>
                    <label for="endereco" class="block text-sm font-medium text-gray-300 mb-2">Endereço Completo</label>
                    <input 
                        type="text" 
                        id="endereco" 
                        v-model="form.local.endereco_manual"
                        class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Rua, número, bairro, referências..."
                    >
                </div>

                <div class="pt-4">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg transform transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Enviando Seguro...</span>
                        <span v-else>Enviar Denúncia Anonimamente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
