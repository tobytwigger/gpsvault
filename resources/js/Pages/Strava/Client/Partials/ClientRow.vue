<template>
    <table class="w-full">
        <thead>
        <tr class="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Limits Used</th>
            <th class="px-4 py-3">Invitations</th>
            <th class="px-4 py-3">Details</th>
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
                    <div class="flex items-center text-sm">
                        <div>
                            <p class="text-black">{{ client.used_15_min_calls }}/100 used until {{ next15Mins }}</p>
                            <p class="text-black">{{ client.used_daily_calls }}/1000 used until {{ nextDay }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 border">
                    <span v-if="client.invitation_link_uuid">
                        {{ client.invitation_link }}.
                        <span v-if="client.invitation_link_expired">
                            Link Expired
                        </span>
                        <span v-else>
                        Valid until {{ toDateTime(client.invitation_link_expires_at) }}
                        </span>
                    </span>
                    <span v-else>
                    N/A
                    </span>
                    <Link :href="route('strava.client.invite', client.id)" method="post" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Refresh</div>

                            <div class="ml-1 text-indigo-500">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </Link>
                </td>
                <td class="px-4 py-3 border">
                    <p>Client ID: {{client.client_id}}</p>
                    <p>Client Secret: {{client.client_secret}}</p>
                </td>
                <td class="px-4 py-3 text-sm border">
                    <Link :href="route('strava.client.destroy', client.id)" method="delete" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Delete</div>

                            <div class="ml-1 text-indigo-500">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </Link>
                    <span v-if="client.is_connected === false">
                        <a :href="route('strava.login', client.id)">Click to login</a>
                    </span>
                    <span v-else>
                        <Link :href="route('integration.destroy', 'strava')" method="delete" as="button" type="button">
                            <div class="flex items-center text-sm font-semibold text-red-700">
                                <div>Click to log out</div>
                            </div>
                        </Link>
                    </span>

                    <Link v-if="client.enabled" :href="route('strava.client.disable', client.id)" method="post" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Disable</div>
                        </div>
                    </Link>
                    <Link v-else :href="route('strava.client.enable', client.id)" method="post" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Enable</div>
                        </div>
                    </Link>

                    <Link v-if="client.public" :href="route('strava.client.private', client.id)" method="post" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Make Private</div>
                        </div>
                    </Link>
                    <Link v-else :href="route('strava.client.public', client.id)" method="post" as="button" type="button">
                        <div class="flex items-center text-sm font-semibold text-red-700">
                            <div>Make Public</div>
                        </div>
                    </Link>
                </td>
            </tr>
        </tbody>
    </table>

</template>

<script>
import {Link} from '@inertiajs/inertia-vue'
import moment from 'moment';

export default {
    name: "ClientRow",
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
