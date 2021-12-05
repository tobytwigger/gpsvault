<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Activities
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
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Distance</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                    <tr class="text-gray-700" v-for="activity in activities">
                                        <td class="px-4 py-3 border">
                                            <div class="flex items-center text-sm">
                                                <div>
                                                    <p class="font-semibold text-black">{{ activity.name }}</p>
                                                    <!--                                                    <p class="text-xs text-gray-600">Developer</p>-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-ms font-semibold border">
                                            {{ toDateTime(activity.started_at) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm border">{{
                                                toKilometers(activity.distance)
                                            }}km
                                        </td>
                                        <td class="px-4 py-3 text-sm border">
                                            <Link :href="route('activity.show', activity.id)">
                                                <div class="flex items-center text-sm font-semibold text-indigo-700">
                                                    <div>View</div>

                                                    <div class="ml-1 text-indigo-500">
                                                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                    </div>
                                                </div>
                                            </Link>
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


    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataDisplayTile from './Partials/DataDisplayTile';
import moment from 'moment';
import {Link} from '@inertiajs/inertia-vue3'

export default defineComponent({
    components: {
        DataDisplayTile,
        AppLayout,
        Link
    },
    props: {
        activities: {
            required: false,
            type: Array,
            default: []
        },
    },
    methods: {
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY');
        },
        toKilometers(value) {
            return Math.round(value / 10) / 100;
        }
    }
})
</script>
