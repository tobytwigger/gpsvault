<template>

    <v-menu
        bottom
        left
        :offset-x="true"
    >
        <template v-slot:activator="{ on: menu, attrs }">
            <slot name="activator" v-bind:on="menu" v-bind:attrs="attrs">
                <v-list-item
                    class='d-flex justify-space-between'
                    v-on="menu"
                    v-bind="attrs"
                    :disabled="disabled"
                >

                    <v-icon>
                        mdi-chevron-left
                    </v-icon>
                    <span>
                        {{ title }}
                    </span>
                    <v-spacer></v-spacer>
                </v-list-item>

            </slot>
        </template>

        <v-list>
            <template
                v-for="(item, index) in menuItems"
            >
                <div
                    :key='index'
                    v-if="(item.show ?? true)">
                    <v-divider
                        v-if='item.isDivider'
                    />

                    <c-sub-menu
                        :disabled="item.disabled ?? false"
                        :menu-items='item.menu'
                        :title='item.title'
                        :icon="item.icon"
                        @sub-menu-click='emitClickEvent'
                        v-else-if='item.menu'
                    />

                    <Link v-else-if="item.href && item.useInertia === true" :href="item.href" style="text-decoration: none">
                        <v-list-item class='d-flex justify-space-between' :disabled="item.disabled">
                            <v-icon v-if="item.icon">{{ item.icon }}</v-icon>
                            <span>{{ item.title }}</span>
                            <v-spacer></v-spacer>
                        </v-list-item>
                    </Link>

                    <a v-else-if="item.href && item.useInertia === false" :target="(item.hrefTarget ?? '_self')" :href="(item.disabled ? '#' : item.href)" style="text-decoration: none">
                        <v-list-item class='d-flex justify-space-between' :disabled="item.disabled">
                            <v-icon v-if="item.icon">{{ item.icon }}</v-icon>
                            <span>{{ item.title }}</span>
                            <v-spacer></v-spacer>
                        </v-list-item>
                    </a>

                    <v-list-item
                        v-else
                        :disabled="item.disabled"
                        class='d-flex justify-space-between'
                        @click='emitClickEvent(item)'
                    >
                        <v-icon v-if="item.icon">{{ item.icon }}</v-icon>
                        <span>{{ item.title }}</span>
                        <v-spacer></v-spacer>
                    </v-list-item>


                </div>
            </template>
        </v-list>
    </v-menu>

</template>

<script>
export default {
    name: "CSubMenu",
    props: {
        title: {
            required: false,
            type: String,
        },
        menuItems: {
            required: true,
            type: Array,
            validator: (items) => items.filter((item) => {
                return true;
            }).length === items.length,
        },
        disabled: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    methods: {
        emitClickEvent(item) {
            this.$emit("sub-menu-click", item);
            this.openMenu = false;
            this.menuOpened = false;
        }
    },
    // watch: {
    //     menuOpened: function () {
    //         this.isOpenOnHover = !this.menuOpened;
    //     }
    // },
    // data: () => ({
    //     openMenu: false,
    //     isOpenOnHover: true,
    //     menuOpened: false
    // })
}
</script>

<style scoped>

</style>
