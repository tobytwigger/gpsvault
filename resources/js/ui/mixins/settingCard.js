export default {
    props: {
        modelValue: {
            required: false,
            default: null
        },
        isBasicSetting: {
            required: false,
            type: Boolean,
            default: true
        },
        errors: {
            required: false,
            type: Array,
            default: []
        }
    },
    computed: {
        value: {
            get() {
                return this.modelValue;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            }
        }
    }

}
