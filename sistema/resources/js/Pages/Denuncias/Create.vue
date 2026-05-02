<template>
    <Head title="Nova Denúncia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nova Denúncia</h2>
        </template>

        <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-2xl font-bold mb-4">Inclusão de Denúncia</h2>
        </div>
        
        <!-- Tabs Nav -->
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button @click="currentTab = 'relato'" :class="currentTab === 'relato' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
              Relato e Local
            </button>
            <button @click="currentTab = 'envolvidos'" :class="currentTab === 'envolvidos' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
              Envolvidos
            </button>
            <button @click="currentTab = 'dados'" :class="currentTab === 'dados' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
              Dados
            </button>
          </nav>
        </div>

        <div class="p-6 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900/50">
          <!-- Relato Tab -->
          <div v-show="currentTab === 'relato'">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                 <h3 class="font-bold mb-2 text-gray-800 dark:text-gray-200">Relato</h3>
                 <textarea class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 h-64" v-model="form.relato" placeholder="Insira o relato completo da ocorrência..." required></textarea>
               </div>
               <div>
                 <h3 class="font-bold mb-2 flex items-center justify-between text-gray-800 dark:text-gray-200">
                     Local de Ocorrência
                     <span v-if="loadingCep" class="text-sm text-gray-500 dark:text-gray-400">Buscando CEP...</span>
                 </h3>
                 <div class="grid grid-cols-6 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                        <input type="text" v-model="form.cep" @blur="searchCEP" maxlength="9" placeholder="00000-000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">UF</label>
                        <select v-model="form.uf" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Município</label>
                        <select v-model="form.municipio" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option v-for="m in municipios" :key="m" :value="m">{{ m }}</option>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro</label>
                        <input type="text" v-model="form.logradouro" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                     <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                        <input type="text" v-model="form.numero" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label>
                        <input type="text" v-model="form.bairro" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complemento</label>
                        <input type="text" v-model="form.complemento" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ponto de Referência</label>
                        <input type="text" v-model="form.ponto_referencia" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                 </div>
               </div>
             </div>
          </div>

          <div v-show="currentTab === 'envolvidos'">
             <div class="flex justify-between items-center mb-4">
                 <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Pessoas Envolvidas</h3>
                 <button type="button" @click="abrirModalEnvolvido" class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded shadow transition-colors">
                     + Incluir Envolvido
                 </button>
             </div>

             <div v-if="form.envolvidos.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 shadow-sm rounded border border-dashed border-gray-300 dark:border-gray-600">
                 Nenhuma pessoa envolvida cadastrada. Clique no botão acima para adicionar.
             </div>

             <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                 <div v-for="(env, index) in form.envolvidos" :key="index" class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm relative">
                     <button type="button" @click="removerEnvolvido(index)" class="absolute top-2 right-2 text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="Remover">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                         </svg>
                     </button>
                     <h4 class="font-bold text-md mb-1 text-gray-900 dark:text-gray-100">{{ env.apelido || env.nome || 'Pessoa sem identificação' }}</h4>
                     <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs px-2 py-1 rounded-full uppercase tracking-wide font-semibold mb-2">
                         {{ env.papel_no_caso || 'NÃO INFORMADO' }}
                     </span>
                     <p class="text-sm text-gray-600 dark:text-gray-400 truncate" v-if="env.nome"><strong class="text-gray-700 dark:text-gray-300">Nome:</strong> {{ env.nome }}</p>
                     <p class="text-sm text-gray-600 dark:text-gray-400 truncate" v-if="env.sinais_particulares"><strong class="text-gray-700 dark:text-gray-300">Detalhes:</strong> {{ env.sinais_particulares }}</p>
                 </div>
             </div>
          </div>

          <!-- Dados Tab -->
          <div v-show="currentTab === 'dados'">
             <div class="grid grid-cols-1 gap-6">
                 
                 <!-- Informaçoes de Classificacao / Controle -->
                 <div class="bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2">Controles e Atributos</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Canal de Recebimento</label>
                            <select v-model="form.canal" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="interno">Atendimento interno</option>
                                <option value="telefone">Telefone / WhatsApp</option>
                                <option value="web">Portal web</option>
                            </select>
                            <p v-if="form.errors.canal" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.canal }}</p>
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-6">
                                <input type="checkbox" v-model="form.difusaoImediata" class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                <span class="ml-2 font-bold text-red-600 dark:text-red-400">Difusão Imediata (Urgente)</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-6">
                                <input type="checkbox" v-model="form.bloqueada" class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50">
                                <span class="ml-2 font-bold text-gray-700 dark:text-gray-300">Bloquear Denúncia (Sigilo Máximo)</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assunto principal</label>
                        <select v-model="form.assunto_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Selecione o assunto principal da denúncia</option>
                            <optgroup v-for="grupo in gruposAssunto" :key="grupo.id" :label="grupo.nome">
                                <option v-for="assunto in grupo.assuntos" :key="assunto.id" :value="assunto.id">
                                    {{ assunto.nome }}
                                </option>
                            </optgroup>
                        </select>
                        <p v-if="form.errors.assunto_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.assunto_id }}</p>
                    </div>

                    <!-- Etiquetas Dinâmicas -->
                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Etiquetas de Classificação Especiais (Ex: DD Mulher, Roubo a Banco)</label>
                        <div class="flex flex-wrap gap-2">
                             <div v-for="etiqueta in etiquetas" :key="etiqueta.id" class="inline-flex items-center">
                                 <input type="checkbox" :id="'etq_'+etiqueta.id" :value="etiqueta.id" v-model="form.etiquetas" class="peer sr-only">
                                 <label :for="'etq_'+etiqueta.id" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-full cursor-pointer text-sm font-medium text-gray-600 dark:text-gray-300 peer-checked:bg-red-600 peer-checked:dark:bg-red-500 peer-checked:text-white peer-checked:border-red-600 peer-checked:dark:border-red-500 transition-colors">
                                     {{ etiqueta.nome }}
                                 </label>
                             </div>
                        </div>
                    </div>
                 </div>

                 <!-- Veiculos Envolvidos -->
                 <div class="bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Veículos da Ocorrência</h3>
                        <button type="button" @click="abrirModalVeiculo" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded shadow text-sm font-medium transition-colors">
                            + Incluir Veículo
                        </button>
                    </div>

                    <div v-if="form.veiculos.length === 0" class="text-center py-6 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 rounded border border-dashed border-gray-300 dark:border-gray-600">
                        Nenhum veículo adicionado. Importante para casos de roubo, rota de fuga ou ponto de droga.
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(vei, index) in form.veiculos" :key="index" class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm relative flex justify-between items-center">
                            <div>
                                <span class="font-bold text-lg text-gray-800 dark:text-gray-200 mr-2">{{ vei.placa || 'SEM PLACA' }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ vei.marca }} {{ vei.modelo }} - {{ vei.cor }}</span>
                            </div>
                            <button type="button" @click="removerVeiculo(index)" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-2" title="Remover">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                 </div>

                 <!-- Comunicação Interna -->
                 <div class="bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">Diretriz da Comunicação Interna</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Comunicação visível apenas para os diretores do DIsque-Denúncia, servindo como histórico de andamento.</p>
                    <textarea class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 h-24" v-model="form.comunicacao_interna" placeholder="Insira seu relato inicial de evolução do caso..."></textarea>
                 </div>
                 
             </div>
          </div>

        </div>
        
        <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
           <button class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white font-bold py-3 px-8 rounded shadow-lg text-lg transition-transform transform hover:-translate-y-1" @click="submit" :disabled="form.processing">
               <span v-if="!form.processing">Salvar Registro de Denúncia</span>
               <span v-else>Salvando...</span>
           </button>
        </div>
      </div>
    </div>
    
    <!-- Modal Fluida de Envolvido -->
    <div v-if="exibirModalEnvolvido" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="fecharModalEnvolvido"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-title">
                        Incluir Pessoa Envolvida
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Papel no Caso</label>
                            <select v-model="novoEnvolvido.papel_no_caso" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="Suspeito">Suspeito</option>
                                <option value="Vítima">Vítima</option>
                                <option value="Testemunha">Testemunha</option>
                                <option value="Informante">Informante</option>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexo</label>
                            <select v-model="novoEnvolvido.sexo" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Não informado</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                            <input type="text" v-model="novoEnvolvido.nome" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apelido/Vulgo</label>
                            <input type="text" v-model="novoEnvolvido.apelido" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Idade Estimada</label>
                            <input type="text" v-model="novoEnvolvido.idade_estimada" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cor da Pele</label>
                            <input type="text" v-model="novoEnvolvido.cor_pele" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Porte Físico</label>
                            <input type="text" v-model="novoEnvolvido.porte_fisico" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sinais Particulares (Tatuagens, cicatrizes, roupas...)</label>
                            <textarea v-model="novoEnvolvido.sinais_particulares" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 h-20"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="salvarEnvolvido" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 dark:bg-red-500 text-base font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Adicionar à Lista
                    </button>
                    <button type="button" @click="fecharModalEnvolvido" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fluida de Veiculo -->
    <div v-if="exibirModalVeiculo" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-veiculo-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="fecharModalVeiculo"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-veiculo-title">
                        Incluir Veículo
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Placa</label>
                            <input type="text" v-model="novoVeiculo.placa" placeholder="ABC-1234" maxlength="8" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 uppercase">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chassis</label>
                            <input type="text" v-model="novoVeiculo.chassis" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca (Nome genérico)</label>
                            <input type="text" v-model="novoVeiculo.marca" placeholder="Ex: FIAT" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modelo</label>
                            <input type="text" v-model="novoVeiculo.modelo" placeholder="Ex: UNO Mille" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cor</label>
                            <input type="text" v-model="novoVeiculo.cor" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proprietário ou Preposto</label>
                            <input type="text" v-model="novoVeiculo.proprietario" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações adicionais (Frota, Adesivos, Batidas...)</label>
                            <textarea v-model="novoVeiculo.detalhes" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 h-16"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="salvarVeiculo" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 dark:bg-blue-500 text-base font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Adicionar Veículo
                    </button>
                    <button type="button" @click="fecharModalVeiculo" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
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
    assunto_id: '',
    difusaoImediata: false,
    bloqueada: false,
    comunicacao_interna: '',
    envolvidos: [],
    veiculos: [],
    etiquetas: [],
    canal: 'interno',
});

const exibirModalEnvolvido = ref(false);
const novoEnvolvido = ref({});
const exibirModalVeiculo = ref(false);
const novoVeiculo = ref({});

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

const abrirModalVeiculo = () => {
    novoVeiculo.value = {
        placa: '',
        chassis: '',
        marca: '',
        modelo: '',
        cor: '',
        proprietario: '',
        detalhes: '',
    };
    exibirModalVeiculo.value = true;
};

const fecharModalVeiculo = () => {
    exibirModalVeiculo.value = false;
};

const salvarVeiculo = () => {
    form.veiculos.push({...novoVeiculo.value});
    fecharModalVeiculo();
};

const removerVeiculo = (index) => {
    form.veiculos.splice(index, 1);
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
