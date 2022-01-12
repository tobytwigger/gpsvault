<template>
    <div class="p-6 border-t border-gray-200 md:border-l">
        <div class="flex items-center">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-400"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a :href="integration.service_url">{{integration.name}}</a></div>
        </div>
        <div class="ml-12">
            <div class="mt-2 text-sm text-gray-500">
                {{integration.description}}
                <ul class="list-disc" v-if="integration.functionality.length > 0">
                    <li v-for="functionality in integration.functionality">{{functionality}}</li>
                </ul>
            </div>
            <div v-if="integration.connected">
                <div class="mt-3 flex items-center text-sm font-semibold">
                    <div>Connected to {{ integration.name }}</div>
                    <div class="ml-1 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <a href="#" @click.prevent="deleteIntegration" class="mt-3 flex items-center text-sm text-gray-500">
                    <div>Disconnect</div>
                    <div class="ml-1 text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </a>
                <a :href="route('integration.logs', integration.id)" class="mt-3 flex items-center text-sm text-gray-500">
                    <div>View connection logs</div>

                    <div class="ml-1 text-indigo-500">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>
            </div>
            <a :href="route('integration.login', integration.id)" v-else>
                <div v-if="integration.login_image_url !== null">
                    <button type="button">
                        <img :src="integration.login_image_url" :alt="'Connect to ' + integration.name" />
                    </button>
                </div>
                <div v-else>
                    <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                        <div>Connect to {{ integration.name }}</div>
                        <div class="ml-1 text-indigo-500">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                </div>
            </a>
            <div v-if="integration.vue_addon">
                <component
                    :is="integration.vue_addon"
                    :key="'integration-add-on-' + integration.id"
                    v-bind="integration.vue_addon_props"
                >

                </component>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Integration",
    props: {
        integration: {
            required: true,
            type: Object
        }
    },
    methods: {
        deleteIntegration() {
            this.$inertia.delete(route('integration.destroy', this.integration.id));
        }
    }
}
</script>

<style scoped>

</style>
