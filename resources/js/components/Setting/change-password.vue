<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Update Password</h2>
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
                        <h3>Change Password Form</h3>
                        <form role="form" ref="changeUserPasswordFrom" name="changeUserPasswordFrom" v-on:submit.prevent="changeUserPassword">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="current">Current Password</label>
                                    <input type="password" class="form-control" name="current" v-model="form.current"
                                        placeholder="current password" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" name="password" v-model="form.password"
                                        placeholder="new password" required>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmation Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" v-model="form.password_confirmation"
                                        placeholder="confirmation password" required>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="cancel()"
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
                form: {
                    'current': null,
                    'password': null,
                    'password_confirmation': null
                },
                errors: null,
            }
        },
        mounted: function () {
        },
        methods: {
            changeUserPassword: function () {
                axios.post('/update/password', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.form = {};
                        notification('Change Password' , response.message , 'success');
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data.errors : null;
                        notification('Change Password' , error.response.data.message , 'danger');
                    });
                hidebtnLoader();
            },
            cancel: function () {
                this.$router.push({
                    path: '/dashboard'
                });
            }
        }
    }

</script>
