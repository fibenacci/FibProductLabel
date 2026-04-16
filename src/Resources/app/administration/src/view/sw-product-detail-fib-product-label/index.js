import template from './sw-product-detail-fib-product-label.html.twig';
import './sw-product-detail-fib-product-label.scss';

const {Criteria, EntityCollection} = Shopware.Data;

Shopware.Component.register('sw-product-detail-fib-product-label', {
    template,

    metaInfo() {
        return {
            title: this.$tc('fib-product-label.productAssignment.tabTitle'),
        };
    },

    data() {
        return {
            emptyLabelCollection: this.createEmptyLabelCollection(),
        };
    },

    computed: {
        product() {
            return Shopware.Store.get('swProductDetail').product;
        },

        labelCollection() {
            return this.product?.extensions?.fibProductLabels ?? this.emptyLabelCollection;
        },
    },

    watch: {
        product: {
            immediate: true,
            handler() {
                this.ensureLabelExtension();
            },
        },
    },

    methods: {
        createEmptyLabelCollection() {
            return new EntityCollection(
                '/fib-product-label',
                'fib_product_label',
                Shopware.Context.api,
                new Criteria(1, 25)
            );
        },

        ensureLabelExtension() {
            if (!this.product) {
                return;
            }

            if (!this.product.extensions) {
                this.product.extensions = {};
            }

            if (!this.product.extensions.fibProductLabels) {
                this.product.extensions.fibProductLabels = this.createEmptyLabelCollection();
            }
        },

        onUpdateLabelCollection(collection) {
            this.product.extensions.fibProductLabels = collection;
        },
    },
});
