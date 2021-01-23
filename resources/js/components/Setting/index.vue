<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Global Settings</h2>
                </div>
            </div>
            <div class="main-container container-heading-bordered">
                <h2 class="container-heading">Email Settings</h2>
                <div class="container-body global-settings">
                    <div class="row global-settings__block">
                        <div class="col-lg-4">
                            <h5 class="global-settings__block-heading">Email</h5>
                            <div class="global-settings__block-desc" >
                               fullname , email ,mobile , cpr ,nationality, created_date
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form class="global-settings__form">
                                <h6 class="global-settings__form-heading">Email Template</h6>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="email" v-model="form.email"
                                        rows="10"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row global-settings__form-actions">
                                    <div class="col-sm-2">
                                        <a href="user-register-email-preview" target="_blank" type="button"  class="btn btn-info">Preview</a>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <label for="description">Send Email</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox"  name="send_email" v-bind:true-value="1" v-bind:false-value="0" v-model="form.send_email">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>
                     <div class="row global-settings__block">
                        <div class="col-lg-4">
                            <h5 class="global-settings__block-heading">SMS</h5>
                            <div class="global-settings__block-desc" >
                               fullname , email ,mobile , cpr , nationality, created_date
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form class="global-settings__form">
                                <h6 class="global-settings__form-heading">SMS Template</h6>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="sms" v-model="form.sms"
                                        rows="10"></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <label for="description">Send SMS</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox"  name="send_sms" v-bind:true-value="1" v-bind:false-value="0" v-model="form.send_sms">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row global-settings__form-actions">
                                   <div class="col-sm-12">
                                       <button type="button" @click="saveSettings" class="btn btn-info pull-right">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="main-container container-heading-bordered">
                <h2 class="container-heading">Shop For Gift Settings</h2>
                <div class="container-body global-settings">
                    <form role="form" ref="giftSettingFrom" name="giftSettingFrom" v-on:submit.prevent="updateGiftSettings">
                        <div class="row global-settings__block">
                            <div class="col-lg-4">
                                <h5 class="global-settings__block-heading">Mirror Images</h5>
                                <div class="global-settings__block-desc" >
                                Shop For Gift Mirror Screen Images
                                </div>
                            </div>
                            <div class="col-lg-8">
                                    <h6 class="global-settings__form-heading">Background Image</h6>
                                    <div class="image" v-if="gift.gift_bg_img">
                                        <img :src="gift.gift_bg_img" class="img-thumbnail" alt="" srcset="">
                                    </div>                                
                                    <div class="form-group">
                                        <div class="file-upload" v-bind:class="{ active: gift_bg_img }">
                                            <div class="file-select">
                                                <div class="file-select-button" id="fileName">Choose File</div>
                                                <div class="file-select-name" v-if="!gift_bg_img">No file chosen...</div>
                                                <div class="file-select-name" v-if="gift_bg_img">{{gift_bg_img.name}}</div>
                                                <input type="file" ref="gift_bg_img" @change="onGiftBgImgFileChange"
                                                    name="gift_bg_img" id="gift_bg_img">
                                            </div>
                                        </div>
                                    </div>
                                    <h6 class="global-settings__form-heading">Logo Image</h6>
                                    <div class="image" v-if="gift.gift_logo_img">
                                        <img :src="gift.gift_logo_img" class="img-thumbnail" alt="" srcset="">
                                    </div>                                
                                    <div class="form-group">
                                        <div class="file-upload" v-bind:class="{ active: gift_logo_img }">
                                            <div class="file-select">
                                                <div class="file-select-button" id="fileName">Choose File</div>
                                                <div class="file-select-name" v-if="!gift_logo_img">No file chosen...</div>
                                                <div class="file-select-name" v-if="gift_logo_img">{{gift_logo_img.name}}</div>
                                                <input type="file" ref="gift_logo_img" @change="onGiftLogoImgFileChange"
                                                    name="gift_logo_img" id="gift_logo_img">
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="form-group row global-settings__form-actions">
                            <div class="col-sm-12">
                                <button type="submit"  class="btn btn-info pull-right">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="main-container container-heading-bordered">
                <h2 class="container-heading">Purchase Settings</h2>
                <div class="container-body global-settings">
                    <form role="form" ref="purchaseSettingFrom" name="purchaseSettingFrom" v-on:submit.prevent="updatePurchaseSettings">
                    <div class="row global-settings__block">
                        <div class="col-lg-4">
                            <h5 class="global-settings__block-heading">Mirror Images</h5>
                            <div class="global-settings__block-desc" >
                               Purchase Mirror Screen Images
                            </div>
                        </div>
                        <div class="col-lg-8">
                                <h6 class="global-settings__form-heading">Background Image</h6>
                                <div class="image" v-if="purchase.purchase_bg_img">
                                    <img :src="purchase.purchase_bg_img" class="img-thumbnail" alt="" srcset="">
                                </div>                                
                                <div class="form-group">
                                    <div class="file-upload" v-bind:class="{ active: purchase_bg_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!purchase_bg_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="purchase_bg_img">{{purchase_bg_img.name}}</div>
                                            <input type="file" ref="purchase_bg_img" @change="onPurchaseBgImgFileChange"
                                                name="purchase_bg_img" id="purchase_bg_img">
                                        </div>
                                    </div>
                                </div>
                                <h6 class="global-settings__form-heading">Logo Image</h6>
                                <div class="image" v-if="purchase.purchase_logo_img">
                                    <img :src="purchase.purchase_logo_img" class="img-thumbnail" alt="" srcset="">
                                </div>                                
                                <div class="form-group">
                                    <div class="file-upload" v-bind:class="{ active: purchase_logo_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!purchase_logo_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="purchase_logo_img">{{purchase_logo_img.name}}</div>
                                            <input type="file" ref="purchase_logo_img" @change="onPurchaseLogoImgFileChange"
                                                name="purchase_logo_img" id="purchase_logo_img">
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group row global-settings__form-actions">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-info pull-right">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="main-container container-heading-bordered">
                <h2 class="container-heading">Spinner Settings</h2>
                <div class="container-body global-settings">
                    <form role="form" ref="spinSettingFrom" name="spinSettingFrom" v-on:submit.prevent="updateSpinSettings">
                    <div class="row global-settings__block">
                        <div class="col-lg-4">
                            <h5 class="global-settings__block-heading">Mirror Images</h5>
                            <div class="global-settings__block-desc" >
                               Spinner Mirror Screen Images
                            </div>
                        </div>
                        <div class="col-lg-8">
                                <h6 class="global-settings__form-heading">Background Image</h6>
                                <div class="image" v-if="spin.spin_bg_img">
                                    <img :src="spin.spin_bg_img" class="img-thumbnail" alt="" srcset="">
                                </div>                                
                                <div class="form-group">
                                    <div class="file-upload" v-bind:class="{ active: spin_bg_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!spin_bg_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="spin_bg_img">{{spin_bg_img.name}}</div>
                                            <input type="file" ref="spin_bg_img" @change="onSpinBgImgFileChange"
                                                name="spin_bg_img" id="spin_bg_img">
                                        </div>
                                    </div>
                                </div>
                                <h6 class="global-settings__form-heading">Logo Image</h6>
                                <div class="image" v-if="spin.spin_logo_img">
                                    <img :src="spin.spin_logo_img" class="img-thumbnail" alt="" srcset="">
                                </div>                                
                                <div class="form-group">
                                    <div class="file-upload" v-bind:class="{ active: spin_logo_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!spin_logo_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="spin_logo_img">{{spin_logo_img.name}}</div>
                                            <input type="file" ref="spin_logo_img" @change="onSpinLogoImgFileChange"
                                                name="spin_logo_img" id="spin_logo_img">
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group row global-settings__form-actions">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-info pull-right">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form:{
                    email: null,
                    sms: null,
                },
                purchase: {},
                gift:{},
                spin:{},
                purchase_bg_img: null,
                purchase_logo_img: null,
                gift_bg_img: null,
                gift_logo_img: null,
                spin_bg_img: null,
                spin_logo_img: null,
                percentCompleted: 0,
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                },
               
            }
        },
        mounted: function () {
            this.getSettings();
            this.getGiftSettings();
            this.getPurchaseSettings();
            this.getSpinSettings();
        },
        methods: {
            getPurchaseSettings: function(){
                axios.get('/get/gift/settings')
                    .then(r => r.data)
                    .then((response) => {
                        this.gift = response.data;
                    });
            },
            getGiftSettings: function(){
                axios.get('/get/purchase/settings')
                    .then(r => r.data)
                    .then((response) => {
                        this.purchase = response.data;
                    });
            },
            getSpinSettings: function(){
                axios.get('/get/spin/settings')
                    .then(r => r.data)
                    .then((response) => {
                        this.spin = response.data;
                    });
            },
            getSettings: function () {
                axios.get('/get/settings')
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            saveSettings: function () {                
                axios.post('/update/settings', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                        notification('Setting' , "Update setting successfully.!" , 'success');
                    });
                //hidebtnLoader();
            },
            onPurchaseBgImgFileChange(e) {
                this.purchase_bg_img = this.$refs.purchase_bg_img.files[0];
            },
            onPurchaseLogoImgFileChange(e) {
                this.purchase_logo_img = this.$refs.purchase_logo_img.files[0];
            },
            onGiftBgImgFileChange(e) {
                this.gift_bg_img = this.$refs.gift_bg_img.files[0];
            },
            onGiftLogoImgFileChange(e) {
                this.gift_logo_img = this.$refs.gift_logo_img.files[0];
            },
            onSpinBgImgFileChange(e) {
                this.spin_bg_img = this.$refs.spin_bg_img.files[0];
            },
            onSpinLogoImgFileChange(e) {
                this.spin_logo_img = this.$refs.spin_logo_img.files[0];
            },
            updatePurchaseSettings(){
                const formData = new FormData(this.$refs.purchaseSettingFrom);
                axios.post('/update/purchase-settings', formData)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchase = response.data;
                        notification('Setting' , "Update setting successfully.!" , 'success');
                    });
            },
            updateSpinSettings(){
                const formData = new FormData(this.$refs.spinSettingFrom);
                axios.post('/update/spin-settings', formData)
                    .then(r => r.data)
                    .then((response) => {
                        this.spin = response.data;
                        notification('Setting' , "Update setting successfully.!" , 'success');
                    });
            },
            updateGiftSettings(){
                const formData = new FormData(this.$refs.giftSettingFrom);
                axios.post('/update/gift-settings', formData)
                    .then(r => r.data)
                    .then((response) => {
                        this.gift = response.data;
                        notification('Setting' , "Update setting successfully.!" , 'success');
                    });

            },
            AlertFilesize: function (sizeinbytes) {
                var fSExt = new Array('Bytes', 'KB', 'MB', 'GB');
                var fSize = sizeinbytes;
                var i = 0;
                while (fSize > 900) {
                    fSize /= 1024;
                    i++;
                }
                return ((Math.round(fSize * 100) / 100) + ' ' + fSExt[i]);
            },
            preview: function(){

            }
        }
    }

</script>

<style scoped>
    img.img-thumbnail {
        max-width: 300px;
    }

    .file-upload__files {
        margin-top: 20px;
    }

    .file-upload__file.file-upload__border {
        padding: 10px;
        border: 1px solid rgba(147, 157, 170, .2) !important;
    }

    .file-upload {
        display: block;
        text-align: center;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
    }

    .file-upload .file-select {
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select .file-select-button {
        background: #dce4ec;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }

    .file-upload .file-select:hover {
        border-color: #34495e;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select:hover .file-select-button {
        background: #34495e;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select {
        border-color: #3fa46a;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select .file-select-button {
        background: #3fa46a;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select input[type=file] {
        z-index: 100;
        cursor: pointer;
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .file-upload .file-select.file-select-disabled {
        opacity: 0.65;
    }

    .file-upload .file-select.file-select-disabled:hover {
        cursor: default;
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        margin-top: 5px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-button {
        background: #dce4ec;
        color: #666666;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }

</style>