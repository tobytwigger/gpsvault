<template>
    <v-app-layout title="API Tokens">

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <api-token-manager :tokens="tokens"
                                   :available-permissions="availablePermissions"
                                   :default-permissions="defaultPermissions" />
            </div>
        </div>
    </v-app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import ApiTokenManager from '@/Pages/API/Partials/ApiTokenManager.vue'
    import VAppLayout from '../../ui/layouts/VAppLayout';

    export default defineComponent({
        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        components: {
            VAppLayout,
            ApiTokenManager,
        },
    })
</script>
