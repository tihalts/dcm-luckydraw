let routes = [{
    path: '/dashboard',
    component: require('../pages/Dashboard.vue').default,
    name: 'dashboard',
},
{
    path: '/admin',
    redirect: '/dashboard',
},
{
    path: '/',
    redirect: '/dashboard',
},
{
    path: '/profile',
    component: require('../../components/Profile.vue').default,
    name: 'UserProfile',
},
{
    path: '/app-settings',
    component: require('../../components/Setting/index.vue').default,
    name: 'Settings',
},
{
    path: '/purchase-points',
    component: require('../../components/PurchasePoint/list.vue').default,
    name: 'PurchasePoints',
},
{
    path: '/purchases',
    component: require('../../components/Purchase/list.vue').default,
    name: 'Purchases',
},
{
    path: '/purchases/create',
    component: require('../../components/Purchase/create.vue').default,
    name: 'createPurchase',
},    
{
    path: '/change/password',
    component: require('../../components/Setting/change-password.vue').default,
    name: 'changePassword',
},
{
    path: '/customers',
    component: require('../../components/Customer/index.vue').default,
    children: [{
            path: '/customers',
            component: require('../../components/Customer/list.vue').default,
            name: 'Customers',
        },
        {
            path: '/customers/create',
            component: require('../../components/Customer/create.vue').default,
            name: 'createCustomer',
        },
        {
            path: '/customers/:customer_id/edit',
            component: require('../../components/Customer/edit.vue').default,
            name: 'editCustomer',
        },
        {
            path: '/customers/create/purchase',
            component: require('../../components/Customer/purchase.vue').default,
            name: 'createCustomerPurchase',
        },
        {
            path: '/customers/:customer_id/scratchcards',
            component: require('../../components/Customer/cards.vue').default,
            name: 'CustomerScratchCards',
        },
        {
            path: '/customers/:customer_id/voucher/create',
            component: require('../../components/Customer/voucher.vue').default,
            name: 'createCustomerVoucher',
        },
        {
            path: '/customers/:customer_id/spin-and-wins',
            component: require('../../components/Customer/spinners.vue').default,
            name: 'customerSpinners',
        },  
    ]
},
{
    path: '/customer',
    component: require('../../components/customerinfo/index.vue').default,
    children: [{
            path: ':customer_id/raffle-draws',
            component: require('../../components/customerinfo/raffledraw.vue').default,
            name: 'CustomerReportRaffleDraws',
        },
        {
            path: ':customer_id/vouchers',
            component: require('../../components/customerinfo/voucher.vue').default,
            name: 'CustomerReportVouchers',
        },
        {
            path: ':customer_id/scratch-cards',
            component: require('../../components/customerinfo/scratchcard.vue').default,
            name: 'CustomerReportCards',
        },
        {
            path: ':customer_id/purchases',
            component: require('../../components/customerinfo/purchase.vue').default,
            name: 'CustomerReportPurchases',
        },
        {
            path: ':customer_id/spin-and-wins',
            component: require('../../components/customerinfo/spin.vue').default,
            name: 'CustomerReportSpinners',
        },
    ]
},
{
    path: '/gifts/:gift_id/items',
    component: require('../../components/Gift/index.vue').default,
    children: [{
            path: '',
            component: require('../../components/Gift/items.vue').default,
            name: 'GiftItems',
        },
        {
            path: 'create',
            component: require('../../components/Gift/create-item.vue').default,
            name: 'createGiftItem',
        },
        {
            path: ':item_id/edit',
            component: require('../../components/Gift/edit-item.vue').default,
            name: 'editGiftItem',
        },
    ]
},
{
    path: '/scratch-cards',
    component: require('../../components/ScratchCard/index.vue').default,
    children: [{
            path: '/scratch-cards',
            component: require('../../components/ScratchCard/list.vue').default,
            name: 'ScratchCards',
        },
        {
            path: '/purchases/:purchase_id/scratch-cards',
            component: require('../../components/ScratchCard/scratchcard.vue').default,
            name: 'purchaseScratchCard',
        },
        {
            path: '/scratch-cards/:scratch_card_id/edit',
            component: require('../../components/ScratchCard/edit.vue').default,
            name: 'editScratchCard',
        },
    ]
},
{
    path: '/reward-points',
    component: require('../../components/RewardPoint/index.vue').default,
    children: [{
            path: '/reward-points',
            component: require('../../components/RewardPoint/list.vue').default,
            name: 'RewardPoints',
        },
        {
            path: '/reward-points/:customer_id/vouchers',
            component: require('../../components/RewardPoint/voucher.vue').default,
            name: 'createRewardPointVoucher',
        }
    ]
},
{
    path: '/purchase/mirror',
    component: require('../../components/Customer/mirror.vue').default,
    name: 'PurchaseMirror',
},
{
    path: '/vouchers',
    component: require('../../components/Voucher/index.vue').default,
    children: [{
            path: '/vouchers',
            component: require('../../components/Voucher/list.vue').default,
            name: 'Vouchers',
        }
    ]
},
{
    path: '/spin-and-win',
    component: require('../../components/Spinner/spin.vue').default,
    name: 'SpinnerMirror',
},  
{
    path: '/spinwinners',
    component: require('../../components/SpinWinner/list.vue').default,
    name: 'SpinWinners',
},
{
    path: '/free-gift/mirror',
    component: require('../../components/FreeGiftCampaign/mirror.vue').default,
    name: 'FreeGiftMirror',
},
{
    path: '/create-free-gift-campaigns',
    component: require('../../components/FreeGiftCampaign/free_gift.vue').default,
    name: 'createFreeGift',
},
{
    path: '/customer-free-gifts/reports',
    component: require('../../components/FreeGiftCampaign/reports.vue').default,
    name: 'customerFreeGiftReports',
},
{
    path: '*',
    component: require('../../components/NotFound.vue').default
},
];
export default routes;
