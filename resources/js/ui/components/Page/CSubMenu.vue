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
                <v-divider
                    :key='index'
                    v-if='item.isDivider'
                />
                <!--                    :is-offset-x=true-->
                <!--                    :is-offset-y=false-->
                <!--                    :is-open-on-hover=false-->

                <c-sub-menu
                    :key='index'
                    :menu-items='item.menu'
                    :title='item.title'
                    :icon="item.icon"
                    @sub-menu-click='emitClickEvent'
                    v-else-if='item.menu'
                />
                <v-list-item
                    class='d-flex justify-space-between'
                    :key='index'
                    @click='emitClickEvent(item)'
                    v-else
                >
                    <v-icon v-if="item.icon">{{ item.icon }}</v-icon>
                    <span>
                        {{ item.title }}
                    </span>
                    <v-spacer></v-spacer>
                </v-list-item>
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
            type: Array
        },
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
