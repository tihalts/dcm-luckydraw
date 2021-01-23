<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Gift Item</h2>
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
                        <h3>Gift Item Edit Form</h3>
                        <hr />
                        <form role="form" ref="editGiftFrom" name="editGiftFrom"
                            v-on:submit.prevent="editGift">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="code">Gift Item Code</label>
                                    <input type="text" min="0" class="form-control" name="code"
                                        v-model="form.code" placeholder="Gift Item code" required>
                                </div>
                                <div class="form-group">
                                    <label for="gift_at">Gift At</label>
                                    <flat-pickr class="form-control" :config="config" name="gift_at"
                                        v-model="form.gift_at" placeholder="Gift At"></flat-pickr>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="$router.go(-1)"
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
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
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
                axios.get('/spinner/gift/item/fetch/' + this.$route.params.item_id )
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editGift: function () {
                this.form.gift_id = this.$route.params.gift_id;
                axios.post('/spinner/gift/item/edit/' + this.$route.params.item_id, this.form)
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
            listGift: function () {
                this.$router.push({
                    name: 'SpinAndWinGiftItems' , params:{ gift_id : this.$route.params.gift_id }
                });
            }
        }
    }

</script>
