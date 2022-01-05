<template>

    <v-app-layout title="Strava Logs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Strava Logs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">


                    <section class="container mx-auto p-6">
                        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
                            <div class="w-full overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                    <tr class="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Message</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                    <tr class="text-gray-700" v-for="log in logs">
                                        <td class="px-4 py-3 border">
                                            <div class="flex items-center text-sm">
                                                <div>
                                                    <p class="font-semibold text-black">{{ log.type }}</p>
                                                    <!--                                                    <p class="text-xs text-gray-600">Developer</p>-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-ms font-semibold border">
                                            {{ toDateTime(log.created_at) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm border">{{
                                                log.log
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>


                </div>
            </div>
        </div>


    </v-app-layout>

</template>

<script>
import { defineComponent } from 'vue'
import moment from 'moment';

export default defineComponent({
    name: "Log",
    props: {
        logs: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    methods: {
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY HH:mm:ss');
        }
    }
});

</script>

<style scoped>

</style>
