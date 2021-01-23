<template>
    <div class="page-content">
   
        <div class="modal fade" id="scratch-card-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="scratch-container">
                            <div id="promo"  class="scratchpad"></div>
                        </div>
                    </div>
                    <div class="modal-footer" style="display:none;">
                        <button type="button" class="btn btn-danger" @click="callScratchCard()">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
<style scoped>
.scratchpad {
        width: 450px;
        height: 445px;
        border: solid 10px #FFFFFF;
        margin: 0 auto;
    }

    .scratch-container {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width: 100%;
    }

    @media only screen and (max-width : 480px) {
        .scratchpad {
            width: 400px;
            height: 396px;
        }

        .scratch-container {
            width: 400px !important;
        }
    }

    @media only screen and (max-width : 370px) {
        .scratchpad {
            width: 350px;
            height: 320px;
        }

        .scratch-container {
            width: 320px !important;
        }
    }

    /* Custom, iPhone Retina */
    @media only screen and (max-width : 320px) {
        .scratchpad {
            width: 290px;
            height: 287px;
        }

        .scratch-container {
            width: 290px !important;
        }
    }

    #scratch-card-modal .modal-body {
        padding: 0 !important;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width: 100%;
    }

    #scratch-card-modal .modal-content {
        height: auto;
        min-height: 100%;
        border-radius: 0;
        width: 100%;
    }

    #scratch-card-modal {
        width: 100%;
    }

    #scratch-card-modal .modal-footer {
        padding: 0;
    }

    #scratch-card-modal .modal-footer .btn {
        padding: 12px 8px;
        font-size: 17px;
        width: 100%;
        border-radius: 0;
    }

</style>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                campaigns: [],
                campaign: {},
                vouchers: [],
                voucher_count: 0,
                scratchcards: [],
                card_index: 0
            }
        },
        mounted: function () {
            this.customerScratchCards(this.$route.params.customer_id);
            this.getCampaigns();
        },
        methods: {
            customerScratchCards: function (id) {
                axios.get('/customer/' + id + '/unredeemed/scratch-cards')
                    .then(r => r.data)
                    .then((response) => {
                        this.scratchcards = response.data;
                        this.callScratchCard(this.card_index);
                    });
            },
            callScratchCard: function(){                
                if(this.scratchcards[this.card_index] !== null){
                    this.redeemedScratchCard(this.scratchcards[this.card_index] , this.card_index);                                       
                }else{
                    $('#scratch-card-modal').modal('hide');    
                }                           
            },
            redeemedScratchCard: function (item , index) {
                var scratched = false;
                var card = item;            
                this.card_index++;   
               
                $('#scratch-card-modal').modal({
                    show: true,backdrop: 'static', keyboard: false
                });
                //$('#scratch-card-modal .modal-footer').hide();
                
                //$('#promo').wScratchPad('reset');
                //$("#promo").empty();
                
                $("#promo").wScratchPad({
                    size: 70,
                    bg: card.bg_url ? card.bg_url : "#cacaca",
                    realtime: true,
                    fg: card.fg_url ? card.fg_url : "#6699ff" ,
                    cursor: null, //'url("images/coin1.png") 5 5, default',
                    scratchDown: null, // Set scratchDown callback.
                    scratchMove: function (e, percent) {
                        if (percent > 90 && !scratched) {   
                            scratched = true;                         
                             $('#scratch-card-modal .modal-footer').show();                      
                        }
                    },
                    scratchUp: function (e, percent) {
                        if (percent > 90 && scratched) {            
                            axios.get('/scratch-card/' + card.id + '/scratched')
                                .then(r => r.data)
                                .then((response) => {
                                    $('#promo').wScratchPad('destroy');
                                });                           
                        }
                    }
                });
            },
            getCampaigns: function(){
                axios.get('/fetch/reward-point/campaigns', {params: {'searchText': ''}
                }).then(r => r.data).then((response) => {
                    this.campaigns = response.data;
                });
            },
            getVouchers: function () {
                axios.get('/voucher/list', {
                        params: {
                            'customer_id': this.$route.params.customer_id,
                            'campaign_id': this.campaign.id
                        }
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                    });
            },
            onCampaignSearch(search, loading) {
                loading(true);
                this.searchCampaigns(loading, search, this);
            },
            searchCampaigns: _.debounce((loading, search, vm) => {
                axios.get('/fetch/reward-point/campaigns', {
                    params: {
                        'searchText': search
                    }
                }).then(r => r.data).then((response) => {
                    vm.campaigns = response.data;
                    loading(false);
                });
            }, 350),
            getVoucherCounts: function () {
                if(!this.campaign.id) return; 
                axios.get('/campaign/'+ this.campaign.id +'/customer/'+ this.$route.params.customer_id+'/voucher/limit' )
                    .then(r => r.data)
                    .then((response) => {
                        this.voucher_count = response;
                        this.getVouchers();
                    });
            },
            createVoucher: function () {
                if(!this.campaign.id) return;
                this.form.campaign_id = this.campaign.id;
                this.form.customer_id = this.$route.params.customer_id;
                axios.post('/reward-point/voucher/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.getVoucherCounts();
                        this.form = {};
                        this.errors = null;
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                hidebtnLoader();
            },
            searchScratchCards: function(){
               axios.get('/customer/'+ this.$route.params.customer_id + '/scratch-card/count')
                    .then(r => r.data)
                    .then((response) => {
                        if(response.data != 0){
                            this.$router.push({
                                path: '/customers/'+ this.$route.params.customer_id + '/scratch-cards'
                            });
                        }else{
                            this.$router.push({
                                path: '/purchases'
                            });
                        }                        
                    });
            },
            remove: function (id) {
                axios.delete('/voucher/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.getVoucherCounts();
                    });
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            }
        }
    }

</script>
