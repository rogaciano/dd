<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    denuncias: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            total: 0,
            from: 0,
            to: 0,
        }),
    },
    metricas: {
        type: Object,
        default: () => ({
            total: 0,
            urgentes: 0,
            novas: 0,
        }),
    },
});

const itens = computed(() => props.denuncias?.data ?? []);

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleString('pt-BR');
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Painel de Controle</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-blue-500">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ metricas.total }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total de Denuncias</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-red-500">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ metricas.urgentes }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Urgentes</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-green-500">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ metricas.novas }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Novas</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-lg font-medium">Denuncias Recentes</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Exibindo {{ denuncias.from ?? 0 }}-{{ denuncias.to ?? 0 }} de {{ denuncias.total ?? 0 }}
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Protocolo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioridade</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Localidade</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recebida Em</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acoes</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="denuncia in itens" :key="denuncia.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ denuncia.protocolo }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                {{ denuncia.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ denuncia.prioridade }}
                                            <span v-if="denuncia.urgente" class="ml-2 text-red-500 text-xs font-bold">URGENTE</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ denuncia.local?.municipio || 'N/A' }} {{ denuncia.local?.uf ? '- ' + denuncia.local?.uf : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(denuncia.recebida_em) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Visualizar</a>
                                        </td>
                                    </tr>

                                    <tr v-if="itens.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Nenhuma denuncia registrada ainda.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="denuncias.links?.length > 3" class="mt-6 flex flex-wrap gap-2">
                            <component
                                :is="link.url ? Link : 'span'"
                                v-for="link in denuncias.links"
                                :key="link.label"
                                :href="link.url || undefined"
                                class="rounded-md border px-3 py-2 text-sm transition"
                                :class="[
                                    link.active
                                        ? 'border-blue-600 bg-blue-600 text-white'
                                        : 'border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/40',
                                    !link.url ? 'cursor-not-allowed opacity-50' : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
