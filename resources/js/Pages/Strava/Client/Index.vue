<template>
    <app-layout title="Sync">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <ul class="list-disc">
                            <li v-for="client in clients">
                                #{{client.id}}
                                <ul class="list-disc">
                                    <li>rate limit 15 mins: {{client.used_15_min_calls}} / {{client['15_mins_resets_at']}}</li>
                                    <li>rate limit daily: {{client.used_daily_calls}} / {{client['daily_resets_at']}}</li>
                                    <li v-if="client.is_connected === false"><a :href="route('strava.login', client.id)">Click to login</a></li>
                                    <li v-else>Logged In</li>
                                    <li>
                                        <jet-danger-button @click="$inertia.delete(route('strava.client.destroy', client.id))">
                                        Delete
                                    </jet-danger-button></li>
                                </ul>
                            </li>
                        </ul>

                        <jet-button @click="isAddingClient = true" :disabled="isAddingClient === true || newClientForm.processing">
                            Add new client
                        </jet-button>
                    </div>
                </div>
            </div>
        </div>


        <modal :show="isAddingClient" :closeable="true" @close="isAddingClient = false">
            <form @submit.prevent="addClient">

                <div class="px-6 py-4">
                    <div class="text-lg">
                        Edit File
                    </div>

                    <div class="mt-4">
                        <p>Change the name and caption of a file</p>

                        <div class="mt-4">
                            <jet-label for="client_id" value="Client ID" />
                            <jet-input id="client_id" type="password" v-model="newClientForm.client_id" class="mt-1 block w-full" />
                        </div>

                        <div class="mt-4">
                            <jet-label for="client_secret" value="Client Secret" />
                            <jet-input id="client_secret" type="password" v-model="newClientForm.client_secret" class="mt-1 block w-full" />
                        </div>

                    </div>
                </div>

                <div class="px-12 py-4 bg-gray-100 text-right">
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Add Client
                        </button>
                    </div>
                </div>
            </form>
        </modal>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Jetstream/Modal';
import JetButton from '@/Jetstream/Button.vue'
import JetDangerButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    name: "Index",
    components: {
        AppLayout,
        Modal,
        JetButton,
        JetInput,
        JetLabel,
        JetDangerButton
    },
    props: {
        clients: {
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
