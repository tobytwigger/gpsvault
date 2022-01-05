<template>
    <v-app-layout title="Sync">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <sync :task-details="taskDetails" :current="current" :previous="previous" :user-id="userId"></sync>
                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                            <integration v-for="integration in integrations" :key="integration.id" :integration="integration"></integration>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </v-app-layout>
</template>

<script>
    import { defineComponent } from 'vue'

    import Integration from './Partials/Integration';
    import Sync from './Partials/Sync';
    import VAppLayout from '../../ui/layouts/VAppLayout';

    export default defineComponent({
        components: {
            VAppLayout,
            Sync,
            Integration,
        },
        props: {
            integrations: {
                type: Array,
                required: false,
                default: () => []
            },
            taskDetails: {
                required: false,
                type: Array,
                default: () => []
            },
            current: {
                required: false,
                type: Object,
                default: null
            },
            previous: {
                required: false,
                type: Array,
                default: () => []
            },
            userId: {
                required: true,
                type: Number
            }
        }
    })
</script>
