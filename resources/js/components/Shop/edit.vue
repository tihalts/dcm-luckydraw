<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Shop</h2>
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
                        <h3>Shop Edit Form</h3>
                        <hr />
                        <form role="form" ref="editShopFrom" name="editShopFrom" v-on:submit.prevent="editShop">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="shop_number">Shop Number</label>
                                    <input type="text" class="form-control" name="shop_number" v-model="form.shop_number"
                                        placeholder="Shop Number" required>
                                </div>
                                <div class="form-group">
                                    <label for="shop_name">Shop Name</label>
                                    <input type="text" class="form-control" name="shop_name" v-model="form.shop_name"
                                        placeholder="Shop Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" v-model="form.description"
                                        rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="email">Business Type</label>
                                    <select name="business_type_id" id="business_type_id" class="form-control" v-model="form.business_type_id">
                                        <option v-for="type in business_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                    </select>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listShop()"
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
    import vSelect from 'vue-select';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                business_types: [],
            }
        },
        mounted: function () {
            this.getShop();
             this.getBusinesTypes();
        },
        methods: {
            getBusinesTypes: function () {
                axios.get('/fetch/shop/business-types')
                    .then(r => r.data)
                    .then((response) => {
                        this.business_types = response.data;
                    });
            },
            getShop: function () {
                axios.get('/shop/fetch/' + this.$route.params.shop_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editShop: function () {
                axios.post('/shop/edit/' + this.$route.params.shop_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listShop();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
            },
            listShop: function () {
                this.$router.push({
                    path: '/shops'
                });
            }
        }
    }

</script>
