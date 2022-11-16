export default {
    data() {
        return {
            tourSteps: []
        }
    },
    methods: {
        _createStep(selector, text, positioning = 'top') {
            return {
                id: selector,
                attachTo: { element: selector, on: positioning },
                text: text,
                arrow: true,
                classes: 'shadow-md bg-purple-dark',
                popperOptions: {
                    modifiers: [{ name: 'offset', options: { offset: [0, 12] } }]
                },
                buttons: [
                    {
                        label: 'Cancel',
                        disabled: false,
                        secondary: true,
                        text: 'Cancel',
                        action() {
                            return this.cancel();
                        }
                    },
                    {
                        label: 'Go to the next step',
                        text: 'Next',
                        secondary: false,
                        disabled: false,
                        action() {
                            return this.next();
                        }
                    }
                ],
            };
        },
    },

    mounted() {
        for(let step of this.tourSteps) {
            this.$tour.addStep(step);
        }
    }
};
