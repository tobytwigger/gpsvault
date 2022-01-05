<template>
    <v-app-layout title="Sync">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>

                        <section class="container mx-auto p-6" v-if="ownedClients.length > 0">
                            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
                                <div class="w-full overflow-x-auto">
                                    Your Clients
                                    <client-row :clients="ownedClients"></client-row>
                                </div>
                            </div>
                        </section>

                        <section class="container mx-auto p-6" v-if="sharedClients.length > 0">
                            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
                                <div class="w-full overflow-x-auto">
                                    Shared Clients
                                    <shared-client-row :clients="sharedClients"></shared-client-row>
                                </div>
                            </div>
                        </section>

                        <section class="container mx-auto p-6" v-if="publicClients.length > 0">
                            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
                                <div class="w-full overflow-x-auto">
                                    Public Clients
                                    <public-client-row :clients="publicClients"></public-client-row>
                                </div>
                            </div>
                        </section>

                        <jet-button :disabled="isAddingClient === true || newClientForm.processing"
                                    @click="isAddingClient = true">
                            Add new client
                        </jet-button>
                    </div>
                </div>
            </div>
        </div>


        <modal :closeable="true" :show="isAddingClient" @close="isAddingClient = false">
            <form @submit.prevent="addClient">

                <div class="px-6 py-4">
                    <div class="text-lg">
                        Add Client
                    </div>

                    <div class="mt-4">
                        <p>You can find the client details in strava.</p>

                        <div class="mt-4">
                            <jet-label for="client_id" value="Client ID"/>
                            <jet-input id="client_id" v-model="newClientForm.client_id" class="mt-1 block w-full"
                                       type="password"/>
                        </div>

                        <div class="mt-4">
                            <jet-label for="client_secret" value="Client Secret"/>
                            <jet-input id="client_secret" v-model="newClientForm.client_secret" class="mt-1 block w-full"
                                       type="password"/>
                        </div>

                    </div>
                </div>

                <div class="px-12 py-4 bg-gray-100 text-right">
                    <div>
                        <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                type="submit">
                            Add Client
                        </button>
                    </div>
                </div>
            </form>
        </modal>

    </v-app-layout>
</template>

<script>
import Modal from '@/Jetstream/Modal';
import JetButton from '@/Jetstream/Button.vue'
import JetDangerButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import ClientRow from './Partials/ClientRow';
import SharedClientRow from './Partials/SharedClientRow';
import PublicClientRow from './Partials/PublicClientRow';

export default {
    name: "Index",
    components: {
        PublicClientRow,
        SharedClientRow,
        ClientRow,
        Modal,
        JetButton,
        JetInput,
        JetLabel,
        JetDangerButton,
    },
    props: {
        ownedClients: {
            type: Array,
            default: () => []
        },
        publicClients: {
            type: Array,
            default: () => []
        },
        sharedClients: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            isAddingClient: false,
            newClientForm: this.$inertia.form({
                client_id: null,
                client_secret: null
            })
        }
    },
    methods: {
        addClient() {
            this.newClientForm.post(route('strava.client.store'), {
                onSuccess: () => {
                    this.newClientForm.reset();
                    this.isAddingClient = false;
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
