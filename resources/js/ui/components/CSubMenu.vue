<template>
    <v-menu
        :close-on-content-click="false"
        :offset-x='isOffsetX'
        :offset-y='isOffsetY'
        :open-on-hover='isOpenOnHover'
        :transition='transition'
        :value="openMenu"
        v-model="menuOpened"
    >
        <template
            v-slot:activator="{ on }"
        >
            <v-list-item
                class='d-flex justify-space-between'
                v-if='isSubMenu'
                v-on="on"
            >
                {{ name }}
                <div class="flex-grow-1">
                </div>
                <v-icon>
                    mdi-chevron-right
                </v-icon>
            </v-list-item>

            <v-btn
                :color='color'
                @click="openMenu=true"
                text
                v-else
                v-on="on"
            >
                <v-icon
                    v-if="icon">
                    {{ icon }}
                </v-icon>
                {{ name }}
            </v-btn>

        </template>
        <v-list>
            <template
                v-for="(item, index) in menuItems"
            >
                <v-divider
                    :key='index'
                    v-if='item.isDivider'
                />
                <c-sub-menu
                    :is-offset-x=true
                    :is-offset-y=false
                    :is-open-on-hover=false
                    :is-sub-menu=true
                    :key='index'
                    :menu-items='item.menu'
                    :name='item.name'
                    @sub-menu-click='emitClickEvent'
                    v-else-if='item.menu'
                />
                <v-list-item
                    :key='index'
                    @click='emitClickEvent(item)'
                    v-else
                >
                    <v-list-item-action v-if="item.icon">
                        <v-icon>mdi-{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-title>
                        {{ item.name }}
                    </v-list-item-title>
                </v-list-item>
            </template>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    name: "CSubMenu",
    template: `

  `,

    props: {
        name: String,
        icon: String,
        menuItems: Array,
        color: { type: String, default: "secondary" },
        isOffsetX: { type: Boolean, default: false },
        isOffsetY: { type: Boolean, default: true },
        //isOpenOnHover: { type: Boolean, default: false },
        isSubMenu: { type: Boolean, default: false },
        transition: { type: String, default: "scale-transition" }
    },
    methods: {
        emitClickEvent(item) {
            this.$emit("sub-menu-click", item);
            this.openMenu = false;
            this.menuOpened = false;
        }
    },
    watch: {
        menuOpened: function () {
            this.isOpenOnHover = !this.menuOpened;
        }
    },
    data: () => ({
        openMenu: false,
        isOpenOnHover: true,
        menuOpened: false
    })
}
</script>

<style scoped>

</style>
