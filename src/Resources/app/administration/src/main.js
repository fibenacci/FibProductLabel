import './module/fib-product-label';
import './page/sw-product-detail';
import './view/sw-product-detail-fib-product-label';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Locale } = Shopware;

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);

Shopware.Module.register('sw-product-detail-fib-product-label-tab', {
    routeMiddleware(next, currentRoute) {
        const customRouteName = 'sw.product.detail.fib.product.labels';

        if (
            currentRoute.name === 'sw.product.detail'
            && currentRoute.children.every((childRoute) => childRoute.name !== customRouteName)
        ) {
            currentRoute.children.push({
                name: customRouteName,
                path: '/sw/product/detail/:id/labels',
                component: 'sw-product-detail-fib-product-label',
                meta: {
                    parentPath: 'sw.product.index',
                },
            });
        }

        next(currentRoute);
    },
});
