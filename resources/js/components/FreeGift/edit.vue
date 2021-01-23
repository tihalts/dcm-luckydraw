<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Gift</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div v-if="errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                        <div class="content-alert__info">
                            <span class="content-alert__info-icon ua-icon-warning"></span>
                        </div>
                        <div class="content-alert__content">
                            <div class="content-alert__heading"></div>
                            <div class="content-alert__message">
                                <ul class="custom-alert__list" v-for="(values , key) in errors" :key="key">
                                    <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="main-container">
                        <h3>Gift Edit Form</h3>
                        <hr />
                        <form role="form" ref="editGiftFrom" name="editGiftFrom"
                            v-on:submit.prevent="editGift">
                            <div class="from-block">
                               <div class="form-group">
                                    <label for="gift_name">Gift Name</label>
                                    <input type="text" class="form-control" name="gift_name"
                                        v-model="form.gift_name" placeholder="Gift Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="code">Gift Code</label>
                                    <input type="text" min="0" class="form-control" name="code"
                                        v-model="form.code" placeholder="Gift code" required>
                                </div>
                                <div class="form-group">
                                    <label for="code">o Of Gifts</label>
                                    <input type="number" min="1" class="form-control" name="no_of_gifts"
                                        v-model="form.no_of_gifts" placeholder="No of Gifts" required>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="no_of_gifts">No of gifts</label>
                                    <input type="number" min="0" class="form-control" name="no_of_gifts"
                                        v-model="form.no_of_gifts" placeholder="No of gifts">
                                </div>
                                <div class="form-group">
                                    <label for="start_at">Start At</label>
                                    <flat-pickr class="form-control" :config="config" name="start_at"
                                        v-model="form.start_at" placeholder="Start At"></flat-pickr>
                                </div>
                                <div class="form-group">
                                    <label for="end_at">End At</label>
                                    <flat-pickr class="form-control" :config="config" name="end_at"
                                        v-model="form.end_at" placeholder="End At"></flat-pickr>
                                </div> -->
                                <!-- <div class="image" v-if="form.gift_img">
                                    <img :src="form.gift_img" class="img-thumbnail" alt="" srcset="">
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Gift Image</label>
                                    <div class="file-upload" v-bind:class="{ active: gift_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!gift_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="gift_img">{{gift_img.name}}</div>
                                            <input type="file" ref="gift_img" @change="onGiftImgFileChange"
                                                name="gift_img" id="gift_img">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="gift_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress"
                                                :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="gift_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ gift_img.name }}</span>
                                                <span
                                                    class="file-upload__file-size">{{ AlertFilesize(gift_img.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listGift()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Update</button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
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
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                gift_img: null,
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
            this.getGift();
        },
        methods: {
            getGift: function () {
                axios.get('/free-gift/fetch/' + this.$route.params.gift_id , {params: {'campaign_id' : this.$route.params.campaign_id}})
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            onGiftImgFileChange(e) {
                this.gift_img = this.$refs.gift_img.files[0];
            },
            editGift: function () {
                const formData = new FormData(this.$refs.editGiftFrom);
                var v = this;
                const config = {
                    onUploadProgress: function (progressEvent) {
                        v.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        console.log(v.percentCompleted);
                    }
                };
                axios.post('/free-gift/edit/' + this.$route.params.gift_id, formData, config)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listGift();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
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
            listGift: function () {
                this.$router.push({
                    name: 'FreeGifts' , params:{ campaign_id : this.$route.params.campaign_id }
                });
            }
        }
    }

</script>
