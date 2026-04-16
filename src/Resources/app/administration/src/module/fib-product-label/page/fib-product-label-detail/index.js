import template from './fib-product-label-detail.html.twig';

const { Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

export default Shopware.Component.register('fib-product-label-detail', Shopware.Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        id: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            productLabel: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    computed: {
        labelRepository() {
            return this.repositoryFactory.create('fib_product_label');
        },

        productRepository() {
            return this.repositoryFactory.create('product');
        },

        isNewLabel() {
            return !this.id || this.id === 'create';
        },

        title() {
            return this.productLabel?.translated?.name || this.$tc(
                this.isNewLabel
                    ? 'fib-product-label.detail.titleNew'
                    : 'fib-product-label.detail.titleEdit'
            );
        },

        allowSave() {
            return this.isNewLabel
                ? this.acl.can('fib_product_label.creator')
                : this.acl.can('fib_product_label.editor');
        },
    },

    watch: {
        id() {
            this.loadEntity();
        },
    },

    created() {
        this.loadEntity();
    },

    methods: {
        loadEntity() {
            this.isLoading = true;

            if (this.isNewLabel) {
                this.productLabel = this.labelRepository.create(Shopware.Context.api);
                this.productLabel.priority = 0;
                this.productLabel.active = true;
                this.productLabel.color = '#000000';
                
                // Initialize products collection for ManyToMany
                this.productLabel.products = new EntityCollection(
                    this.productRepository.route,
                    this.productRepository.entityName,
                    Shopware.Context.api
                );

                this.isLoading = false;
                return;
            }

            const criteria = new Criteria(1, 25);
            criteria.addAssociation('translations');
            criteria.addAssociation('products');

            this.labelRepository.get(this.id, Shopware.Context.api, criteria).then((label) => {
                this.productLabel = label;
                this.isLoading = false;
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('fib-product-label.detail.messageLoadError'),
                });
                this.isLoading = false;
            });
        },

        onSave() {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            return this.labelRepository.save(this.productLabel, Shopware.Context.api).then(() => {
                this.isSaveSuccessful = true;

                if (this.isNewLabel) {
                    this.$router.push({
                        name: 'fib.product.label.detail',
                        params: { id: this.productLabel.id },
                    });
                    return;
                }

                return this.loadEntity();
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('fib-product-label.detail.messageSaveError'),
                });
                this.isLoading = false;
            });
        },

        onCancel() {
            this.$router.push({ name: 'fib.product.label.list' });
        },

        onProductLabelChange(products) {
            this.productLabel.products = products;
        },

        abortOnLanguageChange() {
            return this.labelRepository.hasChanges(this.productLabel);
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        onChangeLanguage(languageId) {
            Shopware.Store.get('context').api.languageId = languageId;
            this.loadEntity();
        },
    },
}));
