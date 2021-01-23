<template>
    <div class="page-content">

        <div class="container" v-if="!form.customer_id && show_form">
            <div class="row">
                <div class="col-md-12">
                    <!-- <div class="page-content__header">
                        <div>
                            <h2 class="page-content__header-heading">Purchase Mirror</h2>
                        </div>
                    </div> -->
                    <div class="main-container">
                        <div class="form-top-logo">
                            <img :src="'/image/setting/gift_logo_img'" alt="">
                        </div>
                        <h3>Customer Registration</h3>
                        <hr>
                        <form role="form" ref="addVoucherFrom" name="addVoucherFrom">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" name="first_name" v-model="form.first_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" v-model="form.last_name" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" v-model="form.email" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cpr">CPR</label>
                                        <input type="text" class="form-control" name="cpr" v-model="form.cpr" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-group">
                                        <label for="dob">Date Of Birth</label>
                                        <flat-pickr class="form-control" :config="dateconfig" name="dob"
                                            v-model="form.dob" placeholder="Date of Birth"></flat-pickr>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" name="mobile" v-model="form.mobile" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="country">Nationality</label>
                                        <input type="text" class="form-control" name="country" v-model="form.country" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="form-bottom-logo">
                        <img :src="'/images/mirror/longlogo.png'" alt="">
                    </div>
                </div>
            </div>
        </div>

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


    .img-c img {
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

    .main-container h3 {
        color: white;
        text-align: center;
    }

    .main-container {
        width: 100%;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 3px;
        background-color: #ffffff69;
        -webkit-box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        font-size: 15px;
        margin-top: 80px;
    }

    .form-top-logo {
        margin-top: -80px !important;
    }

    .form-bottom-logo img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 350px;
    }

    .form-top-logo img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 350px;
    }

</style>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {
                    customer_id: null
                },
                errors: null,
                timer: null,
                show_form: false,
                 dateconfig: {
                    wrap: true,
                    altInput: true,
                    enableTime: false,
                    dateFormat: "Y-m-d",
                },
            }
        },
        watch: {
            form: {
                handler: function (newValue) {
                    if (newValue.customer_id) {
                        this.customerScratchCards();
                        this.show_form = false;
                    }else{
                        this.show_form = true;
                    }
                },
                deep: true
            },
        },
        created() {
            this.timer = setInterval(() => {
                this.getMirror();
            }, 1500);
        },
        beforeDestroy() {
            clearInterval(this.timer);
        },
        mounted: function () {
            //this.customerScratchCards();
        },
        methods: {
            getMirror: function () {
                axios.get('/fetch/purchase/action').then(r => r.data).then((response) => {
                    this.form = response.data;
                    if(this.form.customer_id){
                        this.show_form = true;
                    }
                });
            },
            resetMirror: function () {
                axios.delete('/remove/purchase/action').then(r => r.data).then((response) => {});
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            }
        }
    }

</script>
