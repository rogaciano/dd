<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 border-b border-gray-200">
          <h2 class="text-2xl font-bold mb-4">Inclusão de Denúncia</h2>
          <!-- Form Header: Classification, Difusão Imediata, Bloquear, XPTO, DD Mulher -->
        </div>
        
        <!-- Tabs Nav -->
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button @click="currentTab = 'relato'" :class="currentTab === 'relato' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
              Relato e Local
            </button>
            <button @click="currentTab = 'envolvidos'" :class="currentTab === 'envolvidos' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
              Envolvidos
            </button>
            <button @click="currentTab = 'dados'" :class="currentTab === 'dados' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
              Dados
            </button>
          </nav>
        </div>

        <div class="p-6 text-gray-900 bg-gray-50">
          <!-- Relato Tab -->
          <div v-show="currentTab === 'relato'">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                 <h3 class="font-bold mb-2">Relato</h3>
                 <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-64" v-model="form.relato" placeholder="Insira o relato completo da ocorrência..." required></textarea>
               </div>
               <div>
                 <h3 class="font-bold mb-2 flex items-center justify-between">
                     Local de Ocorrência
                     <span v-if="loadingCep" class="text-sm text-gray-500">Buscando CEP...</span>
                 </h3>
                 <div class="grid grid-cols-6 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">CEP</label>
                        <input type="text" v-model="form.cep" @blur="searchCEP" maxlength="9" placeholder="00000-000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700">UF</label>
                        <select v-model="form.uf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Município</label>
                        <select v-model="form.municipio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option v-for="m in municipios" :key="m" :value="m">{{ m }}</option>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700">Logradouro</label>
                        <input type="text" v-model="form.logradouro" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                     <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" v-model="form.numero" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Bairro</label>
                        <input type="text" v-model="form.bairro" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Complemento</label>
                        <input type="text" v-model="form.complemento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Ponto de Referência</label>
                        <input type="text" v-model="form.ponto_referencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                 </div>
               </div>
             </div>
          </div>

          <div v-show="currentTab === 'envolvidos'">
             <div class="flex justify-between items-center mb-4">
                 <h3 class="font-bold text-lg">Pessoas Envolvidas</h3>
                 <button type="button" @click="abrirModalEnvolvido" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                     + Incluir Envolvido
                 </button>
             </div>

             <div v-if="form.envolvidos.length === 0" class="text-center py-8 text-gray-500 bg-white shadow-sm rounded border border-dashed border-gray-300">
                 Nenhuma pessoa envolvida cadastrada. Clique no botão acima para adicionar.
             </div>

             <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                 <div v-for="(env, index) in form.envolvidos" :key="index" class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm relative">
                     <button type="button" @click="removerEnvolvido(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700" title="Remover">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                         </svg>
                     </button>
                     <h4 class="font-bold text-md mb-1">{{ env.apelido || env.nome || 'Pessoa sem identificação' }}</h4>
                     <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full uppercase tracking-wide font-semibold mb-2">
                         {{ env.papel_no_caso || 'NÃO INFORMADO' }}
                     </span>
                     <p class="text-sm text-gray-600 truncate" v-if="env.nome"><strong>Nome:</strong> {{ env.nome }}</p>
                     <p class="text-sm text-gray-600 truncate" v-if="env.sinais_particulares"><strong>Detalhes:</strong> {{ env.sinais_particulares }}</p>
                 </div>
             </div>
          </div>

          <!-- Dados Tab -->
          <div v-show="currentTab === 'dados'">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                  <h3 class="font-bold mb-2">Comunicação Interna</h3>
                  <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-32" v-model="form.comunicacao_interna"></textarea>
               </div>
               <div>
                  <h3 class="font-bold mb-2">Veículos Envolvidos</h3>
                  <button class="bg-blue-600 text-white px-4 py-2 rounded mb-2">Incluir Veículo</button>
               </div>
             </div>
          </div>

        </div>
        
        <div class="p-6 bg-white border-t border-gray-200">
           <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow" @click="submit">
               Salvar Denúncia
           </button>
        </div>
      </div>
    </div>
    
    <!-- Modal Fluida de Envolvido -->
    <div v-if="exibirModalEnvolvido" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="fecharModalEnvolvido"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Incluir Pessoa Envolvida
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Papel no Caso</label>
                            <select v-model="novoEnvolvido.papel_no_caso" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="Suspeito">Suspeito</option>
                                <option value="Vítima">Vítima</option>
                                <option value="Testemunha">Testemunha</option>
                                <option value="Informante">Informante</option>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Sexo</label>
                            <select v-model="novoEnvolvido.sexo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Não informado</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" v-model="novoEnvolvido.nome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Apelido/Vulgo</label>
                            <input type="text" v-model="novoEnvolvido.apelido" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Idade Estimada</label>
                            <input type="text" v-model="novoEnvolvido.idade_estimada" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Cor da Pele</label>
                            <input type="text" v-model="novoEnvolvido.cor_pele" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Porte Físico</label>
                            <input type="text" v-model="novoEnvolvido.porte_fisico" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Sinais Particulares (Tatuagens, cicatrizes, roupas...)</label>
                            <textarea v-model="novoEnvolvido.sinais_particulares" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-20"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" @click="salvarEnvolvido" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Adicionar à Lista
                    </button>
                    <button type="button" @click="fecharModalEnvolvido" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    defaultCity: String,
    defaultUF: String,
    etiquetas: Array,
    gruposAssunto: Array,
});

const currentTab = ref('relato');

const estados = ref([]);
const municipios = ref([]);
const loadingCep = ref(false);

const form = useForm({
    relato: '',
    cep: '',
    uf: props.defaultUF || 'RJ',
    municipio: props.defaultCity || 'Rio de Janeiro',
    logradouro: '',
    numero: '',
    complemento: '',
    bairro: '',
    ponto_referencia: '',
    classificacao: 'NORMAL',
    difusaoImediata: false,
    bloqueada: false,
    comunicacao_interna: '',
    envolvidos: [],
});

const exibirModalEnvolvido = ref(false);
const novoEnvolvido = ref({});

const abrirModalEnvolvido = () => {
    novoEnvolvido.value = {
        papel_no_caso: 'Suspeito',
        nome: '',
        apelido: '',
        sexo: '',
        idade_estimada: '',
        cor_pele: '',
        porte_fisico: '',
        sinais_particulares: '',
    };
    exibirModalEnvolvido.value = true;
};

const fecharModalEnvolvido = () => {
    exibirModalEnvolvido.value = false;
};

const salvarEnvolvido = () => {
    form.envolvidos.push({...novoEnvolvido.value});
    fecharModalEnvolvido();
};

const removerEnvolvido = (index) => {
    form.envolvidos.splice(index, 1);
};

onMounted(async () => {
    // Carregar UFs do IBGE
    try {
        const { data } = await axios.get('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome');
        estados.value = data.map(uf => uf.sigla);
        if (form.uf) {
            carregarMunicipios(form.uf);
        }
    } catch (error) {
        console.error("Erro ao carregar UFs", error);
    }
});

const carregarMunicipios = async (uf) => {
    if (!uf) return;
    try {
        const { data } = await axios.get(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
        municipios.value = data.map(m => m.nome);
    } catch (error) {
        console.error("Erro ao carregar municípios", error);
    }
};

watch(() => form.uf, (novoUf) => {
    carregarMunicipios(novoUf);
});

const searchCEP = async () => {
    let cepLimpo = form.cep.replace(/\D/g, '');
    if (cepLimpo.length === 8) {
        loadingCep.value = true;
        try {
            const { data } = await axios.get(`https://viacep.com.br/ws/${cepLimpo}/json/`);
            if (!data.erro) {
                form.logradouro = data.logradouro;
                form.bairro = data.bairro;
                form.uf = data.uf;
                // Ao mudar o form.uf, o watch disparará a carga de municípios e precisaremos setar a cidade correta.
                // Como pode haver um atraso, vamos tentar já garantir a cidade se ela existir nas opções atuais.
                setTimeout(() => {
                    form.municipio = data.localidade;
                }, 500);
            }
        } catch (error) {
            console.error("Erro ao buscar CEP", error);
        } finally {
            loadingCep.value = false;
        }
    }
};

const submit = () => {
    form.post(route('denuncias.store'));
};
</script>
