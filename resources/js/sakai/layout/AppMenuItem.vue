<script setup>
import { useLayout } from "@/sakai/layout/composables/layout";
import { onBeforeMount, ref, watch } from "vue";
import NavLink from "@/Components/NavLink.vue";

const { layoutState, setActiveMenuItem, onMenuToggle } = useLayout();

const props = defineProps({
    item: {
        type: Object,
        default: () => ({}),
    },
    index: {
        type: Number,
        default: 0,
    },
    root: {
        type: Boolean,
        default: true,
    },
    parentItemKey: {
        type: String,
        default: null,
    },
});

const isActiveMenu = ref(false);
const itemKey = ref(null);

onBeforeMount(() => {
    itemKey.value = props.parentItemKey
        ? props.parentItemKey + "-" + props.index
        : String(props.index);

    const activeItem = layoutState.activeMenuItem;

    isActiveMenu.value =
        activeItem === itemKey.value || activeItem
            ? activeItem.startsWith(itemKey.value + "-")
            : false;
});

watch(
    () => layoutState.activeMenuItem,
    (newVal) => {
        isActiveMenu.value =
            newVal === itemKey.value || newVal.startsWith(itemKey.value + "-");
    },
);

const itemClick = (event, item) => {
    if (item.disabled) {
        event.preventDefault();
        return;
    }

    if (
        (item.to || item.url) &&
        (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)
    ) {
        onMenuToggle();
    }

    if (item.command) {
        item.command({ originalEvent: event, item: item });
    }

    const foundItemKey = item.items
        ? isActiveMenu.value
            ? props.parentItemKey
            : itemKey
        : itemKey.value;

    setActiveMenuItem(foundItemKey);
};
</script>

<template>
    <li
        :class="{
            'layout-root-menuitem': root,
            'active-menuitem': isActiveMenu,
        }"
    >
        <!-- Csoportcímke (csak root szinten) -->
        <div
            v-if="root && item.label && item.visible !== false"
            class="menu-group-label"
        >
            {{ item.label }}
        </div>

        <!-- Menüpont -->
        <a
            v-if="(!item.to || item.items) && item.visible !== false"
            :href="item.url || '#'"
            @click="itemClick($event, item, index)"
            :class="item.class"
            :target="item.target"
            tabindex="0"
        >
            <i :class="item.icon" class="layout-menuitem-icon" v-if="item.icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i
                class="pi pi-fw pi-angle-down layout-submenu-toggler"
                v-if="item.items"
            ></i>
        </a>

        <nav-link
            v-if="item.to && !item.items && item.visible !== false"
            @click="itemClick($event, item, index)"
            :href="item.to"
            :class="[item.class, { 'active-route': $page.url === item.to }]"
        >
            <i :class="item.icon" class="layout-menuitem-icon" v-if="item.icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
        </nav-link>

        <!-- Almenük -->
        <Transition
            v-if="item.items && item.visible !== false"
            name="layout-submenu"
        >
            <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
                <app-menu-item
                    v-show="!child?.can || can([child.can])"
                    v-for="(child, i) in item.items"
                    :key="child.label + i"
                    :index="i"
                    :item="child"
                    :parentItemKey="itemKey"
                    :root="false"
                ></app-menu-item>
            </ul>
        </Transition>
    </li>
</template>

<style lang="scss" scoped>
.layout-submenu {
    list-style: none;
    padding-left: 1rem;

    li {
        margin-left: 0.5rem;
    }
}
.menu-group-label {
    padding: 0.75rem 1rem;
    font-weight: bold;
    font-size: 0.9rem;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.05em;
}
</style>
