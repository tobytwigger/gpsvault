<template>
    <li class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg">

            <div class="flex flex-row flex-wrap">
                <div class="row-span-2 mr-3">
                    <input
                        :name="task.id"
                        v-model="checkboxValue"
                        class="text-gray-400 w-8 h-8 mr-2 focus:ring-gray-300 focus:ring-opacity-25 border border-gray-300 rounded-full"
                        type="checkbox"
                        :true-value="true"
                        :false-value="false"/>
                </div>
                <div class="flex flex-col flex-grow">
                    <div><label :for="task.id">{{task.name}}</label></div>
                    <div class="text-gray-500">
                        {{ task.description }}
    <!--                <span-->
    <!--                    :class="{'text-red-400': status === 'failed', 'text-gray-400': status === 'running' || status === 'succeeded'}"-->
    <!--                    v-if="message">{{message}}</span>-->
                    </div>
                </div>
                <div class="row-span-2 mr-1 content-center">
                    <a href="#" @click.prevent="showSetup = true" v-if="task.setup_component !== null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                </div>
            </div>

        <modal :show="showSetup" :closeable="true" @close="showSetup = false" v-if="task.setup_component">
            <div class="px-6 py-4">
                <div class="text-lg">
                    Setup for {{task.name}}
                </div>

                <component :is="task.setup_component" :value="config" @input="config = $event">

                </component>

            </div>

            <div class="px-12 py-4 bg-gray-100 text-right">
                <div>
                    <jet-button type="button" @click="showSetup = false">
                        Finished
                    </jet-button>
                </div>
            </div>

        </modal>

    </li>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'
import Modal from '@/Jetstream/Modal';

export default {
    name: "Task",
    inheritAttrs: false,
    components: {
        JetButton,
        Modal
    },
    props: {
        value: {
            type: Object,
            required: true
        },
        task: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            showSetup: false
        }
    },
    computed: {
        dataValue: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        },
        checkboxValue: {
            get() {
                return this.dataValue.enabled;
            },
            set(val) {
                let dataValue = this.dataValue;
                dataValue.enabled = val;
                this.dataValue = dataValue;
            }
        },
        config: {
            get() {
                return this.dataValue.config;
            },
            set(val) {
                console.log(val);
                let dataValue = this.dataValue;
                dataValue.config = val;
                this.dataValue = dataValue;
            }
        }
    }
}
</script>

<style scoped>

</style>
