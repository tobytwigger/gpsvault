<template>
    <div class="antialiased p-8">
        <div class="flex flex-row">
            <nav id="nav" class="relative flex-shrink">
                <span
                    :style="{ transform: `translateY(calc(100% * ${selectedIndex}))` }"
                    class="absolute h-10 w-full bg-white rounded-lg shadow ease-out transition-transform transition-medium"
                ></span>
                <ul class="relative">
                    <li v-for="(item) in menuItems">
                        <button
                            :aria-selected="menuItem === item"
                            class="py-2 px-3 w-full flex items-center focus:outline-none focus-visible:underline"
                            type="button"
                            @click="select(item.id)"
                        >
                            <svg
                                :class="menuItem === item ? 'text-indigo-400' : 'text-gray-500'"
                                class="h-6 w-6 transition-all ease-out transition-medium"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    clip-rule="evenodd"
                                    d="M12.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 101.414 1.414L4 12.414V21a1 1 0 001 1h5a1 1 0 001-1v-6h2v6a1 1 0 001 1h5a1 1 0 001-1v-8.586l.293.293a1 1 0 001.414-1.414l-9-9zM18 10.414l-6-6-6 6V20h3v-6a1 1 0 011-1h4a1 1 0 011 1v6h3v-9.586z"
                                    fill-rule="evenodd"
                                />
                            </svg>
                            <span
                                :class="menuItem === item ? 'text-indigo-600' : 'text-gray-700'"
                                class="ml-2 text-sm font-medium transition-all ease-out transition-medium"
                            >
                              {{ item.title }}
                            </span>
                        </button>
                    </li>
                </ul>
            </nav>


            <p class="flex-grow">
                <slot v-if="hasSelectedItem" :name="menuItem.id">
                    {{menuItem}}
                </slot>
            </p>
        </div>

    </div>

</template>

<script>
export default {
    name: "PageTabs",
    props: {
        menuItems: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            selected: null,
        }
    },
    created() {
        if(this.menuItems.length > 0) {
            this.selected = this.menuItems[0].id
        }
    },
    computed: {
        menuItem() {
            return this.menuItems.find(m => m.id === this.selected);
        },
        hasSelectedItem() {
            return this.menuItem !== null;
        },
        selectedIndex() {
            if(!this.hasSelectedItem) {
                return 0;
            }
            return this.menuItems.indexOf(this.menuItem);
        }
    },
    methods: {
        select(i) {
            this.selected = i;
        }
    }
}
</script>

<style scoped>
.ease-in {
    transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
}

.ease-out {
    transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
}

.ease-in-out {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.transition-fastest {
    transition-duration: 75ms;
}

.transition-faster {
    transition-duration: 100ms;
}

.transition-fast {
    transition-duration: 150ms;
}

.transition-medium {
    transition-duration: 200ms;
}

.transition-slow {
    transition-duration: 300ms;
}

.transition-slower {
    transition-duration: 500ms;
}

.transition-slowest {
    transition-duration: 700ms;
}

.transition-all {
    transition-property: all;
}

.transition-opacity {
    transition-property: opacity;
}

.transition-transform {
    transition-property: transform;
}

.focus-visible.focus-visible\:underline {
    text-decoration: underline;
}
</style>
