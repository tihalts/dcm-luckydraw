<template>
    <div class="page-content">
        <!-- <div class="container"  v-if="!form.customer_id">
            <div class="row">
                <div class="col-md-12">
                    <div id="form-container" class="main-container">
                        <div class="form-top-logo">
                            <img :src="'/images/spin/icon.png'" alt="">
                        </div>
                        <h3>Customer Registration</h3>
                        <hr>
                        <form role="form" ref="addVoucherFrom" name="addVoucherFrom">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" name="first_name"
                                            v-model="form.first_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" name="last_name"
                                            v-model="form.last_name" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" v-model="form.email"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="cpr">CPR</label>
                                        <input type="text" class="form-control" name="cpr" v-model="form.cpr" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" name="mobile" v-model="form.mobile"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="country">Nationality</label>
                                        <input type="text" class="form-control" name="country" v-model="form.country"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="form-bottom-logo">
                        <img :src="'/images/spin/logoblack.png'" alt="">
                    </div>
                </div>
            </div>
        </div> -->
        <div class="container h-100" v-if="!form.customer_id">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-12">
                    <div id="form-container" class="main-container">
                        <div class="form-top-logo">
                            <img :src="'/image/setting/spin_logo_img'" alt="">
                        </div>
                        <h3>Customer Registration</h3>
                        <hr>
                        <form role="form" ref="addVoucherFrom" name="addVoucherFrom">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" name="first_name"
                                            v-model="form.first_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" name="last_name"
                                            v-model="form.last_name" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" v-model="form.email"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="cpr">CPR</label>
                                        <input type="text" class="form-control" name="cpr" v-model="form.cpr" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" name="mobile" v-model="form.mobile"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <label for="country">Nationality</label>
                                        <input type="text" class="form-control" name="country" v-model="form.country"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="form-bottom-logo">
                        <img :src="'/images/spin/logoblack.png'" alt="">
                    </div> -->
                </div>
            </div>
        </div>
        <div class="container" v-if="form.customer_id">
            <!-- <button class="spinBtn" v-show="spinner.spins">CLICK TO SPIN <b v-if="spinner"> ({{spinner.spins}})</b> !</button> -->
            <div class="row">
                <div class="col-12 text-center">
                    <img src="/images/spin/icon.png" class="icon" width="350" alt="" srcset="">
                </div>
                <div class="col-lg-12 col-sm-12 col-h-500 mx-auto text-center"
                    style="padding-top:20px;padding-bottom:80px">
                    <div class="remaining-spins">Remaining Spins: <b v-if="spinner"> {{spinner.spins}}</b> </div>
                    <button class="spin-Btn btn btn-danger" v-show="spinner.spins">CLICK TO SPIN </button>
                </div>
                <div class="col-lg-12 col-sm-12">
                    <div class="wheelContainer">
                        <svg class="wheelSVG" version="1.1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" text-rendering="optimizeSpeed">
                            <defs>
                                <filter id="shadow" x="-100%" y="-100%" width="550%" height="550%">
                                    <feOffset in="SourceAlpha" dx="0" dy="0" result="offsetOut"></feOffset>
                                    <feGaussianBlur stdDeviation="9" in="offsetOut" result="drop" />
                                    <feColorMatrix in="drop" result="color-out" type="matrix" values="0 0 0 0   0
                            0 0 0 0   0 
                            0 0 0 0   0 
                            0 0 0 .3 0" />
                                    <feBlend in="SourceGraphic" in2="color-out" mode="normal" />
                                </filter>
                            </defs>
                            <g class="background-image">
                                <image xlink:href="/images/spin/hot_pocket_wheel.png" x="-8.5%" y="-5.8%" height="117%"
                                    width="117%"></image>
                                <!-- <image  xlink:href="Hot Pocket Wheel.png" height="100%" width="100%"></image> -->
                            </g>
                            <g class="mainContainer">
                                <g class="wheel">
                                    <!-- <image  xlink:href="http://example.com/images/wheel_graphic.png" x="0%" y="0%" height="100%" width="100%"></image> -->
                                </g>
                            </g>
                            <g class="centerCircle" />
                            <g class="wheelOutline" />
                            <g class="pegContainer" opacity="1">
                                <path class="peg" fill="#e5e295"
                                    d="M22.139,0C5.623,0-1.523,15.572,0.269,27.037c3.392,21.707,21.87,42.232,21.87,42.232 s18.478-20.525,21.87-42.232C45.801,15.572,38.623,0,22.139,0z" />
                            </g>
                            <g class="valueContainer" />
                            <image xlink:href="img/icon-black-logo.png" width="172" height="182" x="431" y="286">
                            </image>
                        </svg>
                    </div>
                    <!-- <div class="row" style="min-height:100vh;">
                <div class="col-sm-12 text-center align-self-center">
                    <div class="spinner-item">
                        <button v-show="!this.wheelSpinning" class="btn btn-lx btn-primary" @click="startSpin()">Start
                            <b v-if="spinner"> ({{spinner.spins}})</b></button>
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="438" height="582" class="the_wheel" align="center" valign="center">
                                    <canvas id="canvas" width="434" height="434">
                                        <p style="{color: white}" align="center">Sorry, your browser doesn't support canvas.
                                            Please try
                                            another.</p>
                                    </canvas>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12" style="max-height:100vh;overflow: scroll;overflow-x: hidden;">
                    <div class="main-container" style="background:#ffff;">
                        <h1>Customer Information</h1>
                        <hr/>
                        <h2>{{ customer.fullname }}</h2>
                        <h3>{{ customer.mobile }}</h3>
                        <h3>{{ customer.cpr }}</h3>
                        <table id="demo-foo-addrow"
                            class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Gift Name</th>
                                    <th>Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="gift in gifts" :key="gift.id">
                                    <td>{{gift.name}}</td>
                                    <td>{{gift.code}}</td>
                                </tr>
                                <tr v-show="!gifts.length">
                                    <td colspan="11" class="no-data-found-info">No gifts found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div> -->
                    <div class="toast">
                        <p />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    // Create new wheel object specifying the parameters at creation time.
    //let theWheel;
    let audio = new Audio('spinner/tick.mp3');

    export default {
        data() {
            return {
                form: {
                    customer_id: 700
                },
                errors: null,
                theWheel: null,
                wheelPower: 3,
                wheelSpinning: false,
                show_spinner: false,
                spinner: {},
                customer: {},
                gifts: [],
                spuns: [],
                timer: null,
                show_form: false,
                segments: [{
                        "fillStyle": "#2065B9",
                        "text": "Karthik",
                        "textFontSize": 16,
                        "textFillStyle": "#E2D7D7",
                        "gift_name": "karthik",
                        "gift_id": 1
                    },
                    {
                        "fillStyle": "#2065B9",
                        "text": "Karthik",
                        "textFontSize": 16,
                        "textFillStyle": "#E2D7D7",
                        "gift_name": "karthik",
                        "gift_id": 1
                    }
                ]
            }
        },
        watch: {
            form: {
                handler: function (newValue) {
                    if (newValue.customer_id) {
                        this.customerSpinners();
                        $("body").removeClass("body-image");
                        this.show_form = false;
                    } else {
                        $("body").addClass("body-image");
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
        beforeMount() {},
        mounted: function () {
            //this.initSpin(this.segments , this.segments.length);
            this.customerSpinners();
        },
        methods: {
            getMirror: function () {
                //this.form.customer_id = 5494;
                if (!this.form.customer_id) {
                    axios.get('/fetch/purchase/action').then(r => r.data).then((response) => {
                        this.form = response.data;
                        if (this.form.customer_id) {
                            this.customerSpinners();
                            this.show_form = true;
                        }
                    });
                }
            },
            initSpin(segments, counts = 0) {
                var jsonData = {
                    "colorArray": ["#364C62", "#F1C40F", "#E67E22", "#E74C3C", "#ECF0F1", "#95A5A6", "#16A085",
                        "#27AE60", "#2980B9", "#8E44AD", "#2C3E50", "#F39C12", "#D35400", "#C0392B", "#BDC3C7",
                        "#1ABC9C", "#2ECC71", "#E87AC2", "#3498DB", "#9B59B6", "#7F8C8D"
                    ],

                    "segmentValuesArray": segments,

                    "svgWidth": 1024,
                    "svgHeight": 768,
                    "wheelStrokeColor": "#862417",
                    "wheelStrokeWidth": 1,
                    "wheelSize": 620,
                    "wheelTextOffsetY": 80,
                    "wheelTextColor": "#e5e295",
                    "wheelTextSize": "1.5em",
                    "wheelImageOffsetY": 40,
                    "wheelImageSize": 20,
                    "centerCircleSize": 200,
                    "centerCircleStrokeColor": "#862417",
                    "centerCircleStrokeWidth": 6,
                    "centerCircleFillColor": "#f4f2c2",
                    "segmentStrokeColor": "#e5e295",
                    "segmentStrokeWidth": 1,
                    "centerX": 512,
                    "centerY": 384,
                    "hasShadows": false,
                    "numSpins": counts,
                    "spinDestinationArray": [],
                    "minSpinDuration": 10,
                    "gameOverText": null, //"THANK YOU FOR PLAYING SPIN2WIN WHEEL. COME AND PLAY AGAIN SOON!",
                    "invalidSpinText": "INVALID SPIN. PLEASE SPIN AGAIN.",
                    "introText": null, //"YOU HAVE TO<br>SPIN IT <span style='color:#F282A9;'>2</span> WIN IT!",
                    "hasSound": true,
                    "gameId": Math.random().toString(36).substring(
                    16), //"9a0232ec06bc431114e2a7f3aea03bbe2164f1aa",
                    "clickToSpin": true
                };
                var mySpinBtn = document.querySelector('.spin-Btn');
                //create a new instance of Spin2Win Wheel and pass in the vars object
                this.theWheel = new Spin2WinWheel();

                //WITH your own button
                this.theWheel.init({
                    data: jsonData,
                    onResult: this.alertPrize,
                    onGameEnd: this.spinGameEnd,
                    onError: this.spinError,
                    spinTrigger: mySpinBtn
                });
                // this.theWheel = new Winwheel({
                //     'outerRadius': 212, // Set outer radius so wheel fits inside the background.
                //     'innerRadius': 75, // Make wheel hollow so segments don't go all way to center.
                //     'textFontSize': 24, // Set default font size for the segments.
                //     'textOrientation': 'vertical', // Make text vertial so goes down from the outside of wheel.
                //     'textAlignment': 'outer', // Align text to outside of wheel.
                //     'numSegments': counts, // Specify number of segments.
                //     'segments': segments, // Define segments including colour and text.
                //     'animation': // Specify the animation to use.
                //     {
                //         'type': 'spinToStop',
                //         'duration': 10, // Duration in seconds.
                //         'spins': 3, // Default number of complete spins.
                //         'callbackFinished': this.alertPrize,
                //         'callbackSound': this.playSound,
                //         'soundTrigger': 'pin' // Specify pins are to trigger the sound, the other option is 'segment'.
                //     },
                //     'pins': // Turn pins on.
                //     {
                //         'number': counts,
                //         'fillStyle': 'silver',
                //         'outerRadius': 4,
                //     }
                // });
            },
            fetchCustomer: function () {
                axios.get('/customer/fetch/' + this.form.customer_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.customer = response.data;
                    });
            },
            spinGameEnd(e) {
                //e is gameResultsArray
                console.log(e);
                TweenMax.delayedCall(5, function () {
                    /*location.reload();*/
                });
            },
            spinError(e) {
                console.log('Spin Count: ' + e.spinCount + ' - ' + 'Message: ' + e.msg);
            },
            alertPrize(result) {
                this.$swal({
                    //text: "Please click confirm button to claim your gift.",
                    title: result.userData.gift_id ? "You won " + result.msg + " !." : 'Better Luck Next Time.',
                    type: "success",
                    background: '#842417',
                    color: '#fffff',
                    showCancelButton: false,
                    showConfirmButton: false,
                    timer: 6000
                }).then((result) => {
                    if (result) {
                        axios.get('/customer/' + this.form.customer_id + '/spin/completed', {
                                params: {
                                    spinner_id: this.spinner.id,
                                    gift_id: result.userData ? result.userData.gift_id : null,
                                    spin_id: this.spinner.spin_id
                                }
                            })
                            .then(r => r.data)
                            .then((response) => {
                                this.spinner = {};
                                this.customerSpinners();
                            });
                    } else {
                        this.$swal("Spin and Win", "Gift items out of stocks!.", "error");
                    }
                });
            },
            playSound() {
                audio.pause();
                audio.currentTime = 0;
                audio.play();
            },
            startSpin() {
                this.wheelSpinning = false;
                if (this.wheelSpinning == false) {
                    this.theWheel.animation.spins = 10; //Math.random(8, 15);
                    this.theWheel.animation.stopAngle = this.spinner.gift_segment;
                    this.theWheel.startAnimation();
                    this.wheelSpinning = true;
                }
            },
            resetWheel(spinner) {
                if (this.theWheel) {
                    this.theWheel.restart();
                }
                this.initSpin(spinner.segments, spinner.item_count);

                // this.theWheel.stopAnimation(false);
                // this.theWheel.rotationAngle = 0;
                // this.theWheel.draw();
                // this.wheelSpinning = false;
            },
            customerSpinners: function () {
                if (!this.form.customer_id) return;
                axios.get('/customer/' + this.form.customer_id + '/unspun/spinners')
                    .then(r => r.data)
                    .then((response) => {
                        this.spinner = response.data;
                        console.log(this.spinner);
                        this.fetchCustomer();
                        if (Object.keys(this.spinner).length !== 0) {
                            this.resetWheel(this.spinner);
                        } else {
                            this.form.customer_id = null;
                            this.resetMirror();
                        }
                        this.customerGifts();
                        //this.callScratchCard(this.card_index);
                        //clearInterval(this.timer);                                           
                    });
            },
            customerGifts: function () {
                if (!this.form.customer_id) return;
                axios.get('/customer/' + this.form.customer_id + '/spun/gifts')
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        // if (this.spuns.length == 0 && Object.keys(this.spinner).length !== 0) {
                        //     this.resetMirror();
                        // }
                    });
            },
            resetMirror: function () {
                this.customer = {};
                axios.delete('/remove/purchase/action').then(r => r.data).then((response) => {});
            }
        }
    }

</script>


<style scoped>
    .form-data-container {
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }

    .btn-lx {
        padding: 5px 26px;
        font-size: 24px;
    }

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

    #form-container.main-container h3 {
        color: white;
        text-align: center;
    }

    #form-container.main-container {
        width: 100%;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 3px;
        background-color: #c1a755;
        -webkit-box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        box-shadow: 0 2px 5px 0 rgba(147, 157, 170, 0.03);
        font-size: 15px;
        margin-top: 80px;
    }

    .form-group label {
        color: #ffff !important;
    }

    .main-container {
        width: 100%;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 3px;
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

    .remaining-spins {
        background-color: #872418;
        max-width: 300px;
        padding: 11px;
        font-weight: 700;
        font-size: 20px;
        color: #ededed;
        border-radius: 6px;
        border: none;
        box-shadow: 0 2px 0 #872418;
        cursor: pointer;
        font-family: 'Shojumaru', cursive;
        text-align: center;
        margin: auto;
    }

    .spin-Btn {
        margin-top: 20px;
    }

    @media only screen and (min-width: 1024px) {
        .remaining-spins {
            margin-top: 150px;
        }

        .spin-Btn {
            margin-top: 30px;
        }
    }

</style>
