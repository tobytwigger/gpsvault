<template>
    <li class="px-4 py-2 border-b border-gray-200 w-full" :class="{'bg-gray-200': task.disabled}">

            <div class="flex flex-row flex-wrap">
                <div class="row-span-2 mr-3">
                    <input
                        :name="task.id"
                        v-model="checkboxValue"
                        :class="{'bg-gray-200': task.disabled}"
                        class="text-gray-400 w-8 h-8 mr-2 focus:ring-gray-300 focus:ring-opacity-25 border border-gray-300 rounded-full"
                        type="checkbox"
                        :disabled="task.disabled"
                        :true-value="true"
                        :false-value="false"/>
                </div>
                <div class="flex flex-col flex-grow">
                    <div><label :for="task.id">
                        {{task.name}}
                    </label></div>
                    <div class="text-gray-500">
                        {{ task.description }}
                        <span v-if="task.disabled" class="text-red-400">
                            {{task.disable_because}}
                        </span>
                    </div>
                </div>
                <div class="row-span-2 mr-1 content-center">
                    <div class="flex flex-row" v-if="task.disabled === false && checkboxValue === true">
                        <a href="#" @click.prevent="showSetup = true" v-if="task.setup_component !== null" class="mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>

                        <svg xmlns="http://www.w3.org/2000/svg" v-if="taskErrors === null && isReady" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" v-else class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

        <modal :show="showSetup" :closeable="true" @close="showSetup = false" v-if="task.setup_component">
            <div class="px-6 py-4">
                <div class="text-lg">
                    Setup for {{task.name}}
                </div>

                <component :is="task.setup_component" :value="config" @input="config = $event" :errors="taskErrors">

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

export default {
    name: "Task",
    inheritAttrs: false,
    components: {
    },
    props: {
        index: {
            required: true,
            type: Number
        },
        value: {
            type: Object,
            required: true
        },
        task: {
            type: Object,
            required: true
        }
    },
    watch: {
        task: {
            handler(val) {
                if(val.disabled && this.checkboxValue === true) {
                    this.checkboxValue = false;
                }
            },
            deep: true
        },
        isReady(val) {
            this.validValue = val;
        }
    },
    mounted() {
        if((this.task.disabled || this.task.checked === false) && this.checkboxValue === true) {
            this.checkboxValue = false;
        }
        this.validValue = this.isReady;
    },
    data() {
        return {
            showSetup: false
        }
    },
    computed: {
        taskErrors() {
            let errors = {};
            let errorKey = 'tasks.' + this.index + '.config.'
            if(this.$page.props.errors.hasOwnProperty('startSync')) {
                Object.keys(this.$page.props.errors.startSync ?? {})
                    .filter(key => key.startsWith(errorKey) && key.length > errorKey.length)
                    .map(key => key.slice(errorKey.length))
                    .forEach(key => errors[key] = this.$page.props.errors.startSync[errorKey + key]);
                return errors;
            }
            return null;
        },
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
        validValue: {
            get() {
                return this.dataValue.valid;
            },
            set(val) {
                let dataValue = this.dataValue;
                dataValue.valid = val;
                this.dataValue = dataValue;
            }
        },
        config: {
            get() {
                return this.dataValue.config;
            },
            set(val) {
                let dataValue = this.dataValue;
                dataValue.config = val;
                this.dataValue = dataValue;
            }
        },
        isReady() {
            return this.task.required_config.filter(key => !this.config.hasOwnProperty(key)).length === 0;
        }
    }
}
</script>

<style scoped>

</style>
