import './page/fib-product-label-list';
import './page/fib-product-label-detail';

const {Module} = Shopware;
const privileges = Shopware.Service('privileges');

privileges.addPrivilegeMappingEntry({
    category: 'catalogues',
    parent: 'sw-catalogue',
    key: 'fib_product_label',
    roles: {
        viewer: {
            privileges: ['fib_product_label:read'],
            dependencies: [],
        },
        editor: {
            privileges: ['fib_product_label:update'],
            dependencies: ['fib_product_label.viewer'],
        },
        creator: {
            privileges: ['fib_product_label:create'],
            dependencies: ['fib_product_label.viewer'],
        },
        deleter: {
            privileges: ['fib_product_label:delete'],
            dependencies: ['fib_product_label.viewer'],
        },
    },
});

Module.register('fib-product-label', {
    type: 'plugin',
    name: 'fib-product-label',
    title: 'fib-product-label.module.title',
    description: 'fib-product-label.module.description',
    color: '#0F172A',
    icon: 'regular-tag',
    entity: 'fib_product_label',
    routes: {
        list: {
            component: 'fib-product-label-list',
            path: 'list',
            meta: {
                privilege: 'fib_product_label.viewer',
            },
        },
        create: {
            component: 'fib-product-label-detail',
            path: 'create',
            meta: {
                parentPath: 'fib.product.label.list',
                privilege: 'fib_product_label.creator',
            },
        },
        detail: {
            component: 'fib-product-label-detail',
            path: 'detail/:id',
            props: (route) => ({id: route.params.id}),
            meta: {
                parentPath: 'fib.product.label.list',
                privilege: 'fib_product_label.viewer',
            },
        },
    },
    navigation: [
        {
            id: 'fib-product-label',
            label: 'fib-product-label.module.navigation',
            color: '#0F172A',
            icon: 'regular-tag',
            parent: 'sw-catalogue',
            path: 'fib.product.label.list',
            privilege: 'fib_product_label.viewer',
            position: 90,
        },
    ],
});
