<template>
    <v-app-layout title="Dashboard">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <welcome />
                </div>
            </div>
        </div>
    </v-app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import VAppLayout from '../../ui/layouts/VAppLayout';

    export default defineComponent({
        components: {
            VAppLayout,
            Welcome,
        },
    })
</script>
