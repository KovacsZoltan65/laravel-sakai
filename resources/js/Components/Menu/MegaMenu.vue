<script setup>
import { MegaMenu } from 'primevue';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

const currentUrl = page.url; // vagy page.props.url, attól függően, hogyan adod át az aktuális URL-t
//console.log(currentUrl);
//const route = useRoute();

//const page = usePage();
//const userCan = computed(() => page.props.auth.can ?? {});

const items = ref([
    {
        label: "home",
        icon: 'pi pi-home',
        items: [
            [
                {
                    label: 'Dashboards',
                    items: [
                        {
                            label: "Dashboard", 
                            icon: "pi pi-fw pi-home", 
                            url: '/dashboard',
                        },
                    ],
                },
            ]
        ],
    },
    {
        label: "administration",
        items: [
            [
                {
                    label: 'Users, Roles & Permissions',
                    items: [
                        {
                            label: 'Users',
                            url: '/user',
                            icon: "pi pi-fw pi-user",
                            visible: has('read user'),
                        },
                        {
                            label: 'Roles',
                            url: '/role',
                            icon: "pi pi-fw pi-user-plus",
                            visible: has("read role"),
                        },
                        {
                            label: 'Permissions',
                            url: '/permission',
                            icon: "pi pi-fw pi-lock",
                            visible: has("read permission")
                        },
                    ]
                }
            ],[
                {
                    label: 'Services',
                    items: [
                        {
                            label: 'Companies', 
                            url: '/companies', 
                            icon: "pi pi-fw pi-briefcase",
                            class: 'p-menuitem'
                        }, {
                            label: 'Persons', 
                            url: '/persons', 
                            icon: "pi pi-fw pi-user",
                        }, {
                            label: 'Entities', 
                            url: '/entities', 
                            icon: "pi pi-fw pi-user",
                        }
                    ]
                }
            ],[
                {
                    label: 'Geo',
                    items: [
                        {
                            label: 'Countries', 
                            url: '/countries',
                            icon: 'pi pi-fw pi-map-marker',
                            visible: has('read country'),
                        }, {
                            label: 'Regions', 
                            url: '/regions',
                            icon: 'pi pi-fw pi-map-marker',
                            visible: has('read region'),
                        },
                        {
                            label: 'Cities', 
                            url: '/cities',
                            icon: 'pi pi-fw pi-map-marker',
                            visible: has('read city'),
                        }
                    ]
                }
            ]
        ],
    },
    {
        label: "System",
        items: [
            [{
                label: 'Logs',
                items: [
                    {label: 'System Logs'},
                    {label: 'User Logs'},
                ]
            }],
            [{
                label: 'Settings',
                items: [
                    {label: 'App Settings'},
                    {label: 'Company Settings'},
                    {label: 'User Settings'}
                ]
            }]
        ]
    },
    /* Furniture */
    {
        label: 'Furniture',
        icon: 'pi pi-box',
        items: [
            [
                {
                    label: 'Living Room',
                    items: [{ label: 'Accessories' }, { label: 'Armchair' }, { label: 'Coffee Table' }, { label: 'Couch' }, { label: 'TV Stand' }]
                }
            ],
            [
                {
                    label: 'Kitchen',
                    items: [{ label: 'Bar stool' }, { label: 'Chair' }, { label: 'Table' }]
                },
                {
                    label: 'Bathroom',
                    items: [{ label: 'Accessories' }]
                }
            ],
            [
                {
                    label: 'Bedroom',
                    items: [{ label: 'Bed' }, { label: 'Chaise lounge' }, { label: 'Cupboard' }, { label: 'Dresser' }, { label: 'Wardrobe' }]
                }
            ],
            [
                {
                    label: 'Office',
                    items: [{ label: 'Bookcase' }, { label: 'Cabinet' }, { label: 'Chair' }, { label: 'Desk' }, { label: 'Executive Chair' }]
                }
            ]
        ]
    },
    /* Electronics */
    {
        label: 'Electronics',
        icon: 'pi pi-mobile',
        items: [
            [
                {
                    label: 'Computer',
                    items: [{ label: 'Monitor' }, { label: 'Mouse' }, { label: 'Notebook' }, { label: 'Keyboard' }, { label: 'Printer' }, { label: 'Storage' }]
                }
            ],
            [
                {
                    label: 'Home Theater',
                    items: [{ label: 'Projector' }, { label: 'Speakers' }, { label: 'TVs' }]
                }
            ],
            [
                {
                    label: 'Gaming',
                    items: [{ label: 'Accessories' }, { label: 'Console' }, { label: 'PC' }, { label: 'Video Games' }]
                }
            ],
            [
                {
                    label: 'Appliances',
                    items: [{ label: 'Coffee Machine' }, { label: 'Fridge' }, { label: 'Oven' }, { label: 'Vaccum Cleaner' }, { label: 'Washing Machine' }]
                }
            ]
        ]
    },
    /* Sports */
    {
        label: 'Sports',
        icon: 'pi pi-clock',
        items: [
            [
                {
                    label: 'Football',
                    items: [{ label: 'Kits' }, { label: 'Shoes' }, { label: 'Shorts' }, { label: 'Training' }]
                }
            ],
            [
                {
                    label: 'Running',
                    items: [{ label: 'Accessories' }, { label: 'Shoes' }, { label: 'T-Shirts' }, { label: 'Shorts' }]
                }
            ],
            [
                {
                    label: 'Swimming',
                    items: [{ label: 'Kickboard' }, { label: 'Nose Clip' }, { label: 'Swimsuits' }, { label: 'Paddles' }]
                }
            ],
            [
                {
                    label: 'Tennis',
                    items: [{ label: 'Balls' }, { label: 'Rackets' }, { label: 'Shoes' }, { label: 'Training' }]
                }
            ]
        ]
    }
]);
</script>

<template>
    <MegaMenu :model="items" />
</template>