import template from './fib-product-label-list.html.twig';
import './fib-product-label-list.scss';

const {Criteria} = Shopware.Data;
const {Mixin} = Shopware;

export default Shopware.Component.register('fib-product-label-list', Shopware.Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            labels: null,
            isLoading: false,
            sortBy: 'priority',
            sortDirection: 'DESC',
            total: 0,
            deleteId: null,
        };
    },

    computed: {
        dateFilter() {
            return Shopware.Filter.getByName('date');
        },

        labelRepository() {
            return this.repositoryFactory.create('fib_product_label');
        },

        labelCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            criteria.setTerm(this.term);
            criteria.addAssociation('translations');
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            return criteria;
        },

        columns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('fib-product-label.list.columnName'),
                    primary: true,
                    routerLink: 'fib.product.label.detail',
                },
                {
                    property: 'color',
                    label: this.$tc('fib-product-label.list.columnColor'),
                },
                {
                    property: 'priority',
                    label: this.$tc('fib-product-label.list.columnPriority'),
                    align: 'center',
                },
                {
                    property: 'active',
                    label: this.$tc('fib-product-label.list.columnActive'),
                    align: 'center',
                },
                {
                    property: 'validFrom',
                    label: this.$tc('fib-product-label.list.columnValidFrom'),
                    type: 'date',
                },
                {
                    property: 'validTo',
                    label: this.$tc('fib-product-label.list.columnValidTo'),
                    type: 'date',
                },
            ];
        },
    },

    methods: {
        getList() {
            this.isLoading = true;

            return this.labelRepository.search(this.labelCriteria, Shopware.Context.api).then((labels) => {
                this.labels = labels;
                this.total = labels.total ?? 0;
                this.isLoading = false;
            });
        },

        onSearch(term) {
            this.term = term;
            this.$router.push({
                query: {
                    ...this.$route.query,
                    term,
                },
            });

            this.getList();
        },

        onCreateLabel() {
            this.$router.push({name: 'fib.product.label.create'});
        },

        onEditLabel(id) {
            this.$router.push({name: 'fib.product.label.detail', params: {id}});
        },

        onDeleteLabel(id) {
            this.deleteId = id;
        },

        onCloseDeleteModal() {
            this.deleteId = null;
        },

        onConfirmDelete(id) {
            this.isLoading = true;

            this.labelRepository.delete(id, Shopware.Context.api).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('global.default.success'),
                });

                this.deleteId = null;

                return this.getList();
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('global.default.error'),
                });

                this.isLoading = false;
            });
        },
    },
}));
