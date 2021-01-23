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
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Purchase</div>
                                <div class="widget-alpha__description">wise reports</div>
                            </div>
                            <span class="widget-alpha__icon fa fa-money"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseCustomerReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Customer</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div>
                            <span class="widget-alpha__icon icon-fa fa fa-users"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchasePromoterReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Promoter</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div>
                            <span class="widget-alpha__icon icon-fa fa fa-user"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseCountryReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Country</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div>
                            <span class="widget-alpha__icon icon-fa fa fa-globe"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseShopReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Shop</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div>
                            <span class="widget-alpha__icon icon-fa fa fa-building"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link tag="div" :to="{ 'name' : 'PurchaseCategoryReports' }"
                            class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Shop Category</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div>
                            <span class="widget-alpha__icon icon-fa fa fa-building"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link :to="{ name:'SpinnerSaleReports' }" tag="a" class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">Spinner & Wins</div>
                                <div class="widget-alpha__description">purchase wise reports</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </router-link>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <router-link :to="{ name:'SpinnerCustomerRegisterReports' }" tag="a" class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">Spinner & Wins</div>
                                <div class="widget-alpha__description">Customer wise reports</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .widget {
        cursor: pointer;
    }

</style>
