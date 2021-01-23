<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Reports</h2>
                    <div class="page-content__header-description"></div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseCharts' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Purchase</div>
                                <div class="widget-alpha__description">wise reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'CustomerCharts' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Customer</div>
                                <div class="widget-alpha__description">wise purchase reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'GiftCharts' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Gift</div>
                                <div class="widget-alpha__description">wise purchase reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'VoucherCharts' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Voucher</div>
                                <div class="widget-alpha__description">wise purchase reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div>
                    <!-- <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseShopReports' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Shop</div>
                                <div class="widget-alpha__description">wise purchase reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseCategoryReports' }" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Shop Category</div>
                                <div class="widget-alpha__description">wise purchase reports</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-history"></span>
                        </router-link>
                    </div> -->
                </div>                     
            </div>         
        </div>
    </div>
</template>

<style scoped>
.widget{
    cursor: pointer;
}
</style>