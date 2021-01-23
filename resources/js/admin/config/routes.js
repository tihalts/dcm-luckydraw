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
        path: '/lucky-draws',
        component: require('../../components/RaffleDraw/index.vue').default,
        children: [{
                path: '/lucky-draws',
                component: require('../../components/RaffleDraw/list.vue').default,
                name: 'RaffleDraws',
            },
            {
                path: '/lucky-draws/create',
                component: require('../../components/RaffleDraw/create.vue').default,
                name: 'createRaffleDraw',
            },
            {
                path: '/lucky-draws/:lucky_draw_id/edit',
                component: require('../../components/RaffleDraw/edit.vue').default,
                name: 'editRaffleDraw',
            },
            {
                path: '/lucky-draws/:lucky_draw_id/winners',
                component: require('../../components/RaffleDraw/winners.vue').default,
                name: 'showWinners',
            },
            {
                path: '/lucky-draws/:lucky_draw_id/select/winners',
                component: require('../../components/RaffleDraw/select-winners.vue').default,
                name: 'selectWinners',
            },
            {
                path: '/raffledraw/:lucky_draw_id/select/winners',
                component: require('../../components/RaffleDraw/selectwinner.vue').default,
                name: 'selectWinner',
            },
            {
                path: '/lucky-draws/:lucky_draw_id/winner/{customer_id}',
                component: require('../../components/RaffleDraw/winner.vue').default,
                name: 'showWinner',
            },
            {
                path: '/lucky-draws/:lucky_draw_id/settings',
                component: require('../../components/RaffleDraw/setting.vue').default,
                name: 'RaffleDrawSettings',
            },
        ]
    },
    {
        path: '/promoters',
        component: require('../../components/Promoter/index.vue').default,
        children: [{
                path: '/promoters',
                component: require('../../components/Promoter/list.vue').default,
                name: 'Promoters',
            },
            {
                path: '/promoters/create',
                component: require('../../components/Promoter/create.vue').default,
                name: 'createPromoter',
            },
            {
                path: '/promoters/:promoter_id/edit',
                component: require('../../components/Promoter/edit.vue').default,
                name: 'editPromoter',
            },
        ]
    },
    {
        path: '/supervisors',
        component: require('../../components/Supervisor/index.vue').default,
        children: [{
                path: '/supervisors',
                component: require('../../components/Supervisor/list.vue').default,
                name: 'Supervisors',
            },
            {
                path: '/supervisors/create',
                component: require('../../components/Supervisor/create.vue').default,
                name: 'createSupervisor',
            },
            {
                path: '/supervisors/:supervisor_id/edit',
                component: require('../../components/Supervisor/edit.vue').default,
                name: 'editSupervisor',
            },
        ]
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
                path: ':customer_id/shop-for-free-report',
                component: require('../../components/customerinfo/gift.vue').default,
                name: 'ShopForFreeReport',
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
        path: '/users',
        component: require('../../components/User/index.vue').default,
        children: [{
                path: '/users',
                component: require('../../components/User/list.vue').default,
                name: 'AdminUsers',
            },
            {
                path: '/users/create',
                component: require('../../components/User/create.vue').default,
                name: 'createUser',
            },
            {
                path: '/users/:user_id/edit',
                component: require('../../components/User/edit.vue').default,
                name: 'editUser',
            },
            {
                path: '/users/:user_id/profile',
                component: require('../../components/User/edit.vue').default,
                name: 'userProfile',
            },
            {
                path: '/users/:user_id/settings',
                component: require('../../components/User/edit.vue').default,
                name: 'userSettings',
            },
        ]
    },
    {
        path: '/promoter',
        component: require('../../components/promoterinfo/index.vue').default, 
        children: [{
                path: ':promoter_id/vouchers',
                component: require('../../components/promoterinfo/voucher.vue').default,
                name: 'PromoterReportVouchers',
            },
            {
                path: ':promoter_id/scratch-cards',
                component: require('../../components/promoterinfo/scratchcard.vue').default,
                name: 'PromoterReportCards',
            },
            {
                path: ':promoter_id/purchases',
                component: require('../../components/promoterinfo/purchase.vue').default,
                name: 'PromoterReportPurchases',
            },
            {
                path: ':promoter_id/spin-and-wins',
                component: require('../../components/promoterinfo/spin.vue').default,
                name: 'PromoterReportSpinners',
            },
        ]
    },
    {
        path: '/roles',
        component: require('../../components/Role/index.vue').default,
        children: [{
                path: '/roles',
                component: require('../../components/Role/list.vue').default,
                name: 'Roles',
            },
            {
                path: '/roles/create',
                component: require('../../components/Role/create.vue').default,
                name: 'createRole',
            },
            {
                path: '/roles/:role_id/edit',
                component: require('../../components/Role/edit.vue').default,
                name: 'editRole',
            },
            {
                path: '/roles/:role_id/permissions',
                component: require('../../components/Role/permission.vue').default,
                name: 'rolePermissions',
            },
        ]
    },
    {
        path: '/permissions',
        component: require('../../components/Permission/index.vue').default,
        children: [{
                path: '/permissions',
                component: require('../../components/Permission/list.vue').default,
                name: 'Permissions',
            },
            {
                path: '/permissions/create',
                component: require('../../components/Permission/create.vue').default,
                name: 'createPermission',
            },
            {
                path: '/permissions/:permission_id/edit',
                component: require('../../components/Permission/edit.vue').default,
                name: 'editPermission',
            },
        ]
    },
    {
        path: '/business-types',
        component: require('../../components/BusinessType/index.vue').default,
        children: [{
                path: '/business-types',
                component: require('../../components/BusinessType/list.vue').default,
                name: 'BusinessTypes',
            },
            {
                path: '/business-types/create',
                component: require('../../components/BusinessType/create.vue').default,
                name: 'createBusinessType',
            },
            {
                path: '/business-types/:business_type_id/edit',
                component: require('../../components/BusinessType/edit.vue').default,
                name: 'editBusinessType',
            },
        ]
    },
    {
        path: '/shops',
        component: require('../../components/Shop/index.vue').default,
        children: [{
                path: '/shops',
                component: require('../../components/Shop/list.vue').default,
                name: 'Shops',
            },
            {
                path: '/shops/create',
                component: require('../../components/Shop/create.vue').default,
                name: 'createShop',
            },
            {
                path: '/shops/:shop_id/edit',
                component: require('../../components/Shop/edit.vue').default,
                name: 'editShop',
            },
            {
                path: '/shops/:shop_id/purchases',
                component: require('../../components/Shop/purchases.vue').default,
                name: 'shopPurchases',
            },
        ]
    },
    {
        path: '/campaigns',
        component: require('../../components/Campaign/index.vue').default,
        children: [{
                path: '/campaigns',
                component: require('../../components/Campaign/list.vue').default,
                name: 'Campaigns',
            },
            {
                path: '/campaigns/create',
                component: require('../../components/Campaign/create.vue').default,
                name: 'createCampaign',
            },
            {
                path: '/campaigns/:campaign_id/edit',
                component: require('../../components/Campaign/edit.vue').default,
                name: 'editCampaign',
            },
            {
                path: '/campaigns/:campaign_id/gifts/imports',
                component: require('../../components/Gift/import.vue').default,
                name: 'ImportCampaignGift',
            },           
        ]
    },
    {
        path: '/campaign-loss/imports',
        component: require('../../components/import.vue').default,
        name: 'ImportCampaignLoss',
    }, 
    {
        path: '/reward-point-campaigns',
        component: require('../../components/RewardPointCampaign/index.vue').default,
        children: [{
                path: '/reward-point-campaigns',
                component: require('../../components/RewardPointCampaign/list.vue').default,
                name: 'RewardPointCampaigns',
            },
            {
                path: '/reward-point-campaigns/create',
                component: require('../../components/RewardPointCampaign/create.vue').default,
                name: 'createRewardPointCampaign',
            },
            {
                path: '/reward-point-campaigns/:campaign_id/edit',
                component: require('../../components/RewardPointCampaign/edit.vue').default,
                name: 'editRewardPointCampaign',
            },
            {
                path: '/reward-point-campaigns/:campaign_id/templates',
                component: require('../../components/RewardPointCampaign/template.vue').default,
                name: 'RewardPointTemplates',
            },
        ]
    },
    {
        path: '/campaign-groups',
        component: require('../../components/CampaignGroup/index.vue').default,
        children: [{
                path: '/campaign-groups',
                component: require('../../components/CampaignGroup/list.vue').default,
                name: 'CampaignGroups',
            },
            {
                path: '/campaign-groups/create',
                component: require('../../components/CampaignGroup/create.vue').default,
                name: 'createCampaignGroup',
            },
            {
                path: '/campaign-groups/:campaign_id/edit',
                component: require('../../components/CampaignGroup/edit.vue').default,
                name: 'editCampaignGroup',
            }
        ]
    },
    {
        path: '/campaign-groups/:group_id/free-gift-campaigns',
        component: require('../../components/FreeGiftCampaign/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/FreeGiftCampaign/list.vue').default,
                name: 'FreeGiftCampaigns',
            },
            {
                path: 'create',
                component: require('../../components/FreeGiftCampaign/create.vue').default,
                name: 'createFreeGiftCampaign',
            },
            {
                path: ':campaign_id/edit',
                component: require('../../components/FreeGiftCampaign/edit.vue').default,
                name: 'editFreeGiftCampaign',
            },
            {
                path: '/free-gift-campaigns/:campaign_id/templates',
                component: require('../../components/FreeGiftCampaign/template.vue').default,
                name: 'FreeGiftCampaignTemplates',
            },
        ]
    },
    {
        path: '/customer-free-gifts',
        component: require('../../components/FreeGiftCampaign/users.vue').default,
        name: 'customerFreeGifts',
    },
    {
        path: '/customer-free-gifts/reports',
        component: require('../../components/FreeGiftCampaign/reports.vue').default,
        name: 'customerFreeGiftReports',
    },
    {
        path: '/create-free-gift-campaigns',
        component: require('../../components/FreeGiftCampaign/free_gift.vue').default,
        name: 'createFreeGift',
    },
    {
        path: '/free-gift-campaigns/:campaign_id/gifts',
        component: require('../../components/FreeGift/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/FreeGift/list.vue').default,
                name: 'FreeGifts',
            },
            {
                path: 'create',
                component: require('../../components/FreeGift/create.vue').default,
                name: 'createGiftFree',
            },
            {
                path: ':gift_id/edit',
                component: require('../../components/FreeGift/edit.vue').default,
                name: 'editFreeGift',
            },
            {
                path: 'imports',
                component: require('../../components/FreeGift/import.vue').default,
                name: 'importFreeGifts',
            },     
        ]
    },
    {
        path: '/free-gifts/:gift_id/items',
        component: require('../../components/FreeGift/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/FreeGift/items.vue').default,
                name: 'FreeGiftItems',
            },
            {
                path: 'create',
                component: require('../../components/FreeGift/create-item.vue').default,
                name: 'createFreeGiftItem',
            },
            {
                path: ':item_id/edit',
                component: require('../../components/FreeGift/edit-item.vue').default,
                name: 'editFreeGiftItem',
            },
            {
                path: 'edit',
                component: require('../../components/FreeGift/edit-items.vue').default,
                name: 'editFreeGiftItems',
            },
        ]
    },
    {
        path: '/campaigns/:campaign_id/gifts',
        component: require('../../components/Gift/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/Gift/list.vue').default,
                name: 'Gifts',
            },
            {
                path: 'create',
                component: require('../../components/Gift/create.vue').default,
                name: 'createGift',
            },
            {
                path: ':gift_id/edit',
                component: require('../../components/Gift/edit.vue').default,
                name: 'editGift',
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
            {
                path: 'edit',
                component: require('../../components/Gift/edit-items.vue').default,
                name: 'editGiftItems',
            },
        ]
    },
    {
        path: '/scratch-card-campaigns',
        component: require('../../components/ScratchCardCampaign/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/ScratchCardCampaign/list.vue').default,
                name: 'ScratchCardCampaigns',
            },
            {
                path: 'create',
                component: require('../../components/ScratchCardCampaign/create.vue').default,
                name: 'createScratchCardCampaign',
            },
            {
                path: ':campaign_id/edit',
                component: require('../../components/ScratchCardCampaign/edit.vue').default,
                name: 'editScratchCardCampaign',
            },
            {
                path: ':campaign_id/winners',
                component: require('../../components/ScratchCardCampaign/winners.vue').default,
                name: 'ScratchCardWinners',
            },
            {
                path: ':campaign_id/templates',
                component: require('../../components/ScratchCardCampaign/template.vue').default,
                name: 'ScratchCardTemplates',
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
        path: '/spin-and-wins',
        component: require('../../components/Spinner/index.vue').default,
        children: [{
                path: '/spin-and-wins',
                component: require('../../components/Spinner/list.vue').default,
                name: 'SpinAndWins',
            },
            {
                path: '/spin-and-wins/create',
                component: require('../../components/Spinner/create.vue').default,
                name: 'createSpinAndWin',
            },
            {
                path: '/spin-and-wins/:spinner_id/edit',
                component: require('../../components/Spinner/edit.vue').default,
                name: 'editSpinAndWin',
            },
            {
                path: '/spin-and-wins/:spinner_id/gifts/imports',
                component: require('../../components/SpinwinGift/import.vue').default,
                name: 'ImportSpinAndWinGift',
            },           
        ]
    },
    {
        path: '/spin-and-wins/:spinner_id/gifts',
        component: require('../../components/SpinwinGift/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/SpinwinGift/list.vue').default,
                name: 'SpinAndWinGifts',
            },
            {
                path: 'create',
                component: require('../../components/SpinwinGift/create.vue').default,
                name: 'createSpinAndWinGift',
            },
            {
                path: ':gift_id/edit',
                component: require('../../components/SpinwinGift/edit.vue').default,
                name: 'editSpinAndWinGift',
            },       
        ]
    },
    {
        path: '/spin-and-wins/gifts/:gift_id/items',
        component: require('../../components/SpinwinGift/index.vue').default,
        children: [{
                path: '',
                component: require('../../components/SpinwinGift/items.vue').default,
                name: 'SpinAndWinGiftItems',
            },
            {
                path: 'create',
                component: require('../../components/SpinwinGift/create-item.vue').default,
                name: 'createSpinAndWinGiftItem',
            },
            {
                path: ':item_id/edit',
                component: require('../../components/SpinwinGift/edit-item.vue').default,
                name: 'editSpinAndWinGiftItem',
            },
            {
                path: 'edit',
                component: require('../../components/SpinwinGift/edit-items.vue').default,
                name: 'editSpinAndWinGiftItems',
            },
        ]
    },
    {
        path: '/spinwinners',
        component: require('../../components/SpinWinner/list.vue').default,
        name: 'SpinWinners',
    },
    {
        path: '/spinandwin/sale-reports',
        component: require('../../components/spinner-report/sales.vue').default,
        name: 'SpinnerSaleReports',
    },
    {
        path: '/spinandwin/customer-reports',
        component: require('../../components/spinner-report/customers.vue').default,
        name: 'SpinnerCustomerRegisterReports',
    },
    {
        path: '/purchase/mirror',
        component: require('../../components/Customer/mirror.vue').default,
        name: 'PurchaseMirror',
    },
    {
        path: '/free-gift/mirror',
        component: require('../../components/FreeGiftCampaign/mirror.vue').default,
        name: 'FreeGiftMirror',
    },
    {
        path: '/spin-and-win',
        component: require('../../components/Spinner/spin.vue').default,
        name: 'SpinnerMirror',
    },    
    {
        path: '/report/purchases',
        component: require('../../components/Charts/purchase.vue').default,
        name: 'PurchaseCharts',
    },
    {
        path: '/report/gifts',
        component: require('../../components/Charts/gift.vue').default,
        name: 'GiftCharts',
    },
    {
        path: '/report/customers',
        component: require('../../components/Charts/customer.vue').default,
        name: 'CustomerCharts',
    },
    {
        path: '/report/vouchers',
        component: require('../../components/Charts/voucher.vue').default,
        name: 'VoucherCharts',
    },
    {
        path: '/reports/customer/by-countries',
        component: require('../../components/Charts/Customer/country.vue').default,
        name: 'ReportCountryCustomers',
    },
    {
        path: '/reports/customer/by-vouchers',
        component: require('../../components/Charts/Customer/voucher.vue').default,
        name: 'ReportVoucherCustomers',
    },
    {
        path: '/reports/customer/by-gifts',
        component: require('../../components/Charts/Customer/gift.vue').default,
        name: 'ReportGiftCustomers',
    },
    {
        path: '/reports/voucher/by-campaigns',
        component: require('../../components/Charts/Voucher/campaign.vue').default,
        name: 'ReportVoucherCampaigns',
    },
    {
        path: '/reports/voucher/by-days',
        component: require('../../components/Charts/Voucher/list.vue').default,
        name: 'ReportVoucherDays',
    },
    {
        path: '/reports/voucher/by-promoters',
        component: require('../../components/Charts/Voucher/promoter.vue').default,
        name: 'ReportVoucherPromoters',
    },
    {
        path: '/reports/sale/by-campaigns',
        component: require('../../components/Charts/Purchase/campaign.vue').default,
        name: 'ReportCampaignSales',
    },
    {
        path: '/reports/sale/by-categories',
        component: require('../../components/Charts/Purchase/category.vue').default,
        name: 'ReportCategorySales',
    },
    {
        path: '/reports/sale/by-shops',
        component: require('../../components/Charts/Purchase/shop.vue').default,
        name: 'ReportShopSales',
    },
    {
        path: '/reports/sale/by-Countries',
        component: require('../../components/Charts/Purchase/country.vue').default,
        name: 'ReportCountrySales',
    },
    {
        path: '/reports/sale/by-customers',
        component: require('../../components/Charts/Purchase/customer.vue').default,
        name: 'ReportCustomerSales',
    },
    {
        path: '/reports/sale/by-promoters',
        component: require('../../components/Charts/Purchase/promoter.vue').default,
        name: 'ReportPromoterSales',
    },
    {
        path: '/reports/sales',
        component: require('../../components/Charts/Purchase/list.vue').default,
        name: 'ReportDaySales',
    },
    {
        path: '/reports/gift/by-promoters',
        component: require('../../components/Charts/Gift/promoter.vue').default,
        name: 'ReportPromoterGifts',
    },
    {
        path: '/reports/gift/by-days',
        component: require('../../components/Charts/Gift/list.vue').default,
        name: 'ReportDayGifts',
    },
    {
        path: '/reports/gift/by-campaigns',
        component: require('../../components/Charts/Gift/campaign.vue').default,
        name: 'ReportCampaignGifts',
    },
    {
        path: '/reports/purchase-by-amounts',
        component: require('../../components/Purchase/report-by-amount.vue').default,
        name: 'ReportPurchaseByAmounts',
    },
    {
        path: '/reports/purchase-by-customers',
        component: require('../../components/Purchase/report-by-customer.vue').default,
        name: 'ReportPurchaseByCustomers',
    },
    {
        path: '/vouchers/report',
        component: require('../../components/Report/voucher.vue').default,
        name: 'VoucherReport',
    },
    {
        path: '/purchases/report',
        component: require('../../components/Report/purchase.vue').default,
        name: 'PurchaseReport',
    },
    {
        path: '/activities',
        component: require('../../components/Report/activity.vue').default,
        name: 'ActivityLogs',
    },
    {
        path: '/scratchcards/report',
        component: require('../../components/Report/scratchcard.vue').default,
        name: 'ScratchCardReport',
    },
    {
        path: '/gifts/report',
        component: require('../../components/Report/gift.vue').default,
        name: 'GiftReport'
    },
    {
        path: '/spin-and-wins/gifts/report',
        component: require('../../components/Report/gift.vue').default,
        name: 'SpinAndWinGiftReport'
    },
    {
        path: '/reports',
        component: require('../../components/report/index.vue').default,
        name: 'PurchaseIndexReports'
    },
    {
        path: '/chart/reports',
        component: require('../../components/Charts/index.vue').default,
        name: 'chartReports'
    },
    {
        path: '/purchase-reports',
        component: require('../../components/report/purchases.vue').default,
        name: 'PurchaseReports'
    },
    {
        path: '/purchase-customer-reports',
        component: require('../../components/report/customers.vue').default,
        name: 'PurchaseCustomerReports'
    },
    {
        path: '/purchase-promoter-reports',
        component: require('../../components/report/promoters.vue').default,
        name: 'PurchasePromoterReports'
    },
    {
        path: '/purchase-shop-category-reports',
        component: require('../../components/report/categories.vue').default,
        name: 'PurchaseCategoryReports'
    },
    {
        path: '/purchase-country-reports',
        component: require('../../components/report/countries.vue').default,
        name: 'PurchaseCountryReports'
    },
    {
        path: '/purchase-shop-reports',
        component: require('../../components/report/shops.vue').default,
        name: 'PurchaseShopReports'
    },
    {
        path: '*',
        component: require('../../components/NotFound.vue').default
    },
];
export default routes;
