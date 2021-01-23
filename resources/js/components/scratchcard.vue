<template>
    <transition name="modal">
        <div class="modal fade in" id="scratch-card-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-mask">

                <div class="modal-wrapper">

                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="scratch-container">
                                    <div id="promo" ref="promo" class="scratchpad"></div>
                                </div>
                            </div>
                            <div class="modal-footer" style="display:none;">
                                <button type="button" class="btn btn-danger" v-if="cardCount >= 10" @click="skip">Skip {{ cardCount }}
                                    Cards</button>
                                <button type="button" class="btn btn-success" @click="next">Next</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </transition>


</template>


<script>
    export default {
        name: 'ScratchCard',
        props: {
            card: {
                type: Object,
                required: true
            },
            cardCount: {
                type: Number,
                default: 0
            },
            clickSkip: {
                type: Function,
                default: () => {}
            },
            clickNext: {
                type: Function,
                default: () => {}
            },
        },
        data() {
            return {}
        },
        mounted: function () {
            this.redeemedScratchCard();
        },
        methods: {
            redeemedScratchCard: function () {
                var vm = this;
                var scratched = false;

                $('#scratch-card-modal').modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });

                $("#promo").wScratchPad({
                    size: 70,
                    bg: vm.card["bg_url"] ? vm.card["bg_url"] : "#cacaca",
                    realtime: true,
                    fg: vm.card["fg_url"] ? vm.card["fg_url"] : "#6699ff",
                    cursor: null, //'url("images/coin1.png") 5 5, default',
                    scratchDown: null, // Set scratchDown callback.

                    scratchMove: function (e, percent) {
                        if (percent > 30 && !scratched) {
                            scratched = true;
                            $('#scratch-card-modal .modal-footer').show();                            
                            vm.close();
                        }
                    },

                    scratchUp: function (e, percent) {
                        if (percent > 50 && scratched) {
                            if(vm.cardCount == 1){
                                vm.next();                                
                            }
                        }
                    }
                });

            },
            skip() {
                $('#scratch-card-modal').modal('hide');
                this.reset();
                this.$emit('clickSkip');
            },
            next() {
                $('#scratch-card-modal').modal('hide');
                this.reset();
                this.$emit('clickNext');
            },
            close() {
                this.$emit('close');
            },
            reset(){
                $('#promo').wScratchPad('reset');
            },
            clear() {
                $('#promo').wScratchPad('clear');
            }
        }
    }

</script>

<style scoped>
    .scratchpad {
        width: 450px;
        height: 430px;
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
