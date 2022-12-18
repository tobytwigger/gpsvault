export default {
    data() {
        return {
            tourSteps: [],
            tourObject: null
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
        this.tourObject = this.$shepherd({
            useModalOverlay: true,
        });
        this.$showTour.onShow = () => {
            if(this.tourObject !== null) {
                this.tourObject.start();
            }
        }

        for(let step of this.tourSteps) {
            this.tourObject.addStep(step);
        }
    },

    destroyed() {
        this.tourObject = null;
    }
};
