<template>
    <div class="page-content">

        <div class="container-fluid" v-if="!scratchcards.length">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">No Scratch Cards Found</h2>
                </div>
                <div class="page-content__header-meta">
                    <a @click="finishAction()" class="btn btn-info icon-left" style="color:white;">
                        Goto Next  
                        <span class="btn-icon mdi mdi-wallet-giftcard"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid" v-if="scratchcards.length">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Scratch Cards ({{ scratchcards.length }}) </h2>
                </div>
                <div class="page-content__header-meta">
                    <a @click="finishAction()" class="btn btn-info icon-left" style="color:white;">
                        Goto Next  
                        <span class="btn-icon mdi mdi-wallet-giftcard"></span>
                    </a>
                </div>
            </div>
            <div class="row" >
                <div class="col-lg-3 col-md-4 col-sm-6" v-for="card in scratchcards" :key="card.id" @click="getScratchCard(card)">
                    <div class="card-type-2">
                        <div class="img-c">
                            <img :src="card.fg_url" width="800" height="800" />
                        </div>
                        <div class="clear-line"></div>
                        <h4>{{ card.campaign_name }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" v-if="scratchedcards.length">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Today's Gifts ({{ scratchedcards.length }}) </h2>
                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6" v-for="card in scratchedcards" :key="card.id">
                    <div class="card-type-2">
                        <div class="img-c">
                            <img :src="card.bg_url" width="800" height="800" />
                        </div>
                        <div class="clear-line"></div>
                        <h4>{{ card.campaign_name }}</h4>
                    </div>
                </div>
            </div>
        </div>
       
        <scratch-card v-if="show_card" :cardCount="scratchcards.length" :card="card" @clickSkip="skipScratchCards"
            @clickNext="nextScratchCard">
        </scratch-card>

        <!-- <div class="modal fade in" id="scratch-card-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="scratch-container">
                            <div id="promo" ref="promo" class="scratchpad"></div>
                        </div>
                    </div>
                    <div class="modal-footer" style="display:none;">
                        <button type="button" class="btn btn-danger" @click="skipScratchCards">Skip
                            {{scratchcards.length}} Cards</button>
                        <button type="button" class="btn btn-success" @click="nextScratchCard">Next</button>
                    </div>
                </div>
            </div>
        </div> -->

    </div>

</template>

<style scoped>
    .clear-line {

        width: 100%;
        height: 20px;
    }

    .img-c {
        position: relative;
        height: 0;
        width: 100%;
        overflow: hidden;
        padding-top: 75.09%
    }


    img {
        display: block;
        position: absolute;
        display: block;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        width: 100%;
        height: auto;
        margin: auto;
    }

    .card-type-1 {
        background-color: #fff;
        border-radius: 4px;
        border: 1px solid #e1e8ed;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        min-width: 300px;
        padding: 15px;
        width: 100%;
    }

    .card-type-2 {

        border-radius: 3px;
        background-color: #fff;
        background-color: #fff;
        border: 1px solid #d8d8d8;
        border-bottom-width: 2px;
        width: 100%;
        max-width: 300px;
        padding: 8px;
        margin-bottom: 3%;
    }

</style>

<script>
    import axios from 'axios';
    import ScratchCard from '../scratchcard';

    export default {
        components: {
            ScratchCard
        },
        data() {
            return {
                form: {},
                errors: null,
                campaigns: [],
                scratchcards: [],
                scratchedcards: [],
                card: {},
                show_card: false,
                timer: null
            }
        },
        created() {
            this.timer = setInterval(() => {
                this.customerScratchCards();
            }, 1500);
        },
        beforeDestroy() {
            clearInterval(this.timer);
        },
        mounted: function () {
            //this.customerScratchCards();
            this.updateMirror();
        },
        methods: {
            customerScratchCards: function () {
                axios.get('/customer/' + this.$route.params.customer_id + '/unredeemed/scratch-cards')
                    .then(r => r.data)
                    .then((response) => {
                        this.scratchcards = response.data;
                        //this.callScratchCard(this.card_index);
                        //console.log(this.$route.params.customer_id);
                        this.customerScratchedCards();                       
                    });
            },
            customerScratchedCards: function () {
                axios.get('/customer/' + this.$route.params.customer_id + '/scratched/cards')
                    .then(r => r.data)
                    .then((response) => {
                        this.scratchedcards = response.data;

                        if (this.scratchedcards.length == 0 &&  this.scratchcards.length == 0) {
                            this.getCampaigns();
                        }
                    });
            },
            updateMirror: function () {
                axios.get('/update/user-action/' +  this.$route.params.customer_id , { params : {}}).then(r => r.data).then((response) => {
                    //this.form = response.data;
                });
            },
            getScratchCard: function(card){
                this.card = card;
                this.show_card = true;
            },
            callScratchCard: function () {
                if (this.scratchcards.length != 0) {
                    this.card = this.scratchcards[this.card_index];
                    this.redeemedScratchCard(this.scratchcards[this.card_index], this.card_index);
                }
            },
            skipScratchCards: function () {
                this.$swal({
                    title: "Are you scratch all cards?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Scratch all!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.get('/customer/' + this.$route.params.customer_id + '/skip/scratch-cards')
                            .then(r => r.data)
                            .then((response) => {
                                this.scratchcards = [];
                                this.show_card = false;
                                this.customerScratchCards();
                            });
                    } else {
                        this.show_card = true;
                        //this.$swal("Scratch Card", "Your cards has not been skipped !", "error");   
                    }
                });
            },
            nextScratchCard: function () {
                axios.get('/scratch-card/' + this.card.id + '/scratched')
                    .then(r => r.data)
                    .then((response) => {
                        //this.$router.go();                         
                        this.show_card = false;
                        this.card = {}                      ; 
                        axios.get('/customer/' + this.$route.params.customer_id + '/unredeemed/scratch-cards')
                            .then(r => r.data)
                            .then((response) => {        
                                this.scratchcards = response.data; 
                                if(this.scratchcards.length != 0){
                                    this.getScratchCard(this.scratchcards[0]);
                                }                                                    
                            });
                        //this.updateScratchCards(); 
                                          
                    });
            },
            updateScratchCards: function(){
                if (this.scratchcards.length != 0) {
                    const cards = [];

                    for (let index = 0; index < this.scratchcards.length; index++) {
                        cards.push(this.scratchcards[index].id);                        
                    }

                    axios.post('/customer/'  + this.$route.params.customer_id +  '/scratch-card/infos' , {"cards" : cards})
                        .then(r => r.data)
                        .then((response) => {
                            ///this.$router.go();  
                            this.scratchcards = response.data;                     
                        });

                    
                }else{
                   this.getCampaigns();
                }
            },
            finishAction: function(){

                if(this.scratchcards.length == 0)
                {
                    this.getCampaigns();
                }

                for (let index = 0; index < this.scratchcards.length; index++) {
                    if(this.scratchcards[index].is_scratched){
                        this.$swal({
                            title: "Some scratch cads not yet scratched!",
                            text: "Are you sure? You won't be able to revert this!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Yes"
                        }).then((result) => { // <--
                            if (result.value) { // <-- if confirmed
                                this.getCampaigns();
                            } 
                        });
                          
                        break;
                    }                    
                } 

                               
            },
            getCampaigns: function () {
                this.customerSpinners();
                // axios.get('/fetch/reward-point/campaigns', {
                //     params: {
                //         'searchText': ''
                //     }
                // }).then(r => r.data).then((response) => {
                //     this.campaigns = response.data;
                //     if (this.campaigns.length == 0) {
                //         this.listPurchase();
                //     }else{
                //         this.rewardPoints();
                //     }
                // });
            },
            customerSpinners: function(){
                this.$router.push({
                    name: 'customerSpinners' , params : {
                        customer_id : this.$route.params.customer_id
                    }
                });
            },
            rewardPoints: function(){
                this.$router.push({
                    name: 'createCustomerVoucher' , params : {
                        customer_id : this.$route.params.customer_id
                    }
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
