<template>
    <table class="w-full">
        <thead>
        <tr class="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Owner</th>
            <th class="px-4 py-3">Limits</th>
            <th class="px-4 py-3"></th>
        </tr>
        </thead>
        <tbody class="bg-white">
        <tr class="text-gray-700" v-for="client in clients">
            <td class="px-4 py-3 border">
                <div>
                    {{ client.id }}
                    <span v-if="client.enabled === false">Disabled</span>
                </div>
            </td>
            <td class="px-4 py-3 border">
                <div>
                    {{ client.user }}
                </div>
            </td>
            <td class="px-4 py-3 border">
                <div class="flex items-center text-sm">
                    <div>
                        <p class="text-black">{{ client.used_15_min_calls }}/100 used until {{ next15Mins }}</p>
                        <p class="text-black">{{ client.used_daily_calls }}/1000 used until {{ nextDay }}</p>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 text-sm border">
                <Link :href="ziggyRoute('strava.client.leave', client.id)" method="delete" as="button" type="button">
                    <div class="flex items-center text-sm font-semibold text-red-700">
                        <div>Leave</div>

                        <div class="ml-1 text-indigo-500">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                </Link>
                <span v-if="client.is_connected === false">
                <a :href="ziggyRoute('strava.login', client.id)">Click to login</a></span>
                <span v-else>Logged In</span>
            </td>
        </tr>
        </tbody>
    </table>

</template>

<script>
import {Link} from '@inertiajs/inertia-vue'
import moment from 'moment';

export default {
    name: "SharedClientRow",
    components: {Link},
    props: {
        clients: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
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
}
</script>

<style scoped>

</style>
