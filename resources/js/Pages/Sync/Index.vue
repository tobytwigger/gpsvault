<template>
    <app-layout title="Sync">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sync</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <sync :sync="sync"></sync>
                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                            <integration v-for="integration in integrations" :key="integration.id" :integration="integration"></integration>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'

    import Integration from './Partials/Integration';
    import Sync from './Partials/Sync';

    export default defineComponent({
        components: {
            Sync,
            Integration,
            AppLayout,
        },
        props: {
            integrations: {
                type: Array,
                required: false,
                default: () => []
            },
            sync: {
                required: false,
                type: Object,
                default: () => {
                    return {
                        tasks: [],
                        sync: null,
                        lastComplete: null,
                        openSync: false
                    }
                }
            }
        }
    })
</script>
