<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Supervisor</h2>
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
                        <h3>Supervisor Edit Form</h3>
                        <hr />
                        <form role="form" ref="editUserFrom" name="editUserFrom" v-on:submit.prevent="editUser">
                            <div class="from-block">
                                <!-- <label for="inputEmail3">Supervisor Image</label>
                                <div class="profile-img-container">
                                    <img class="img-responsive img-bordered img-thumbnail" :src="form.profile_image" />
                                    <div class="upload-btn-wrapper">
                                        <button class="upload-btn btn btn-success">Select Image</button>
                                        <input type="file" ref="profileImage" name="profileImage"
                                            @change="onFileChange" />
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" name="first_name" v-model="form.first_name"
                                        placeholder="First Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" v-model="form.last_name"
                                        placeholder="Last Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" v-model="form.email"
                                        placeholder="Email address" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Mobile</label>
                                    <vue-tel-input 
                                        :class="!mobile_isValid ? 'is-invalid' : ''" 
                                        :required="true" 
                                        :placeholder="'Enter mobile number'"
                                        :defaultCountry="'bh'"
                                        :enabledCountryCode="true"
                                        :disabledFormatting="true"
                                        :disabledFetchingCountry="true"
                                        @onInput="onInput" 
                                        @onValidate="CheckValidation"
                                        v-model="form.mobile"
                                        :preferredCountries="['bh','sa','kw','ae','om','in','pk','ph','bd']">
                                        </vue-tel-input>
                                </div>
                                <div class="form-group">
                                    <label for="email">Nationality</label>
                                    <v-select label="name" v-model="form.nationality" :options="countries" style="width:100%;"></v-select>
                                </div>
                                <div class="form-group">
                                    <label for="password">User Role</label>
                                    <select name="role" id="user_role" class="form-control" v-model="form.role">
                                        <option value="promoter">Promoter</option>
                                        <option value="supervisor">Supervisor</option>
                                    </select>
                                </div>

                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listUser()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Update</button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div v-if="change_password_errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                        <div class="content-alert__info">
                            <span class="content-alert__info-icon ua-icon-warning"></span>
                        </div>
                        <div class="content-alert__content">
                            <div class="content-alert__heading"></div>
                            <div class="content-alert__message">
                                <ul class="custom-alert__list" v-for="(values , key) in change_password_errors" :key="key">
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
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" name="password" v-model="change_password.password"
                                        placeholder="new password" required>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmation Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" v-model="change_password.password_confirmation"
                                        placeholder="confirmation password" required>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Change Password</button>
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
    .profile-img-container {
        width: 200px;
        margin: auto;
    }

    .profile-img-container img {
        margin-bottom: 20px;
        width: 200px;
        height: 200px;
    }

    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .from-block {
        padding: 20px;
    }

    .upload-btn {
        /* border: 2px solid gray;
        color: #fff;
        background-color: #f16a39;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 20px;
        font-weight: bold; */
        margin-left: 25px;
    }

    .upload-btn-wrapper input[type=file] {
        font-size: 30px;
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 150px;
    }

</style>

<script>
    import axios from 'axios';
    import vSelect from 'vue-select';

    export default {
        data() {
            return {
                form: {
                    profile_image: null
                },
                errors: null,
                profile_image_prev: null,
                countries: [],
                mobile_isValid: null,
                change_password_errors: null,
                change_password: {}
            }
        },
        mounted: function () {
            this.getUser();
            this.getCountries();
        },
        methods: {
            onFileChange(e) {
                this.form.profile_image  = URL.createObjectURL(this.$refs.profileImage.files[0]);
            },
            isObject: function (a) {
                return (!!a) && (a.constructor === Object);
            },
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
            },
            getUser: function () {
                axios.get('/supervisor/fetch/' + this.$route.params.supervisor_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editUser: function () {
                 if(!this.mobile_isValid) return;
                axios.post('/supervisor/edit/' + this.$route.params.supervisor_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listUser();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
            },
            onInput({ number, isValid, country }) {
                this.mobile_isValid = isValid;
            },
            CheckValidation: function({ number, isValid, country }){
                this.mobile_isValid = isValid;
                this.form.mobile = number;
                this.form.dialCode = '+' + country.dialCode;
                this.form.country_iso = country.iso2;
            },
            changeUserPassword: function () {
                axios.post('/supervisor/change-password/' + this.$route.params.supervisor_id, this.change_password)
                    .then(r => r.data)
                    .then((response) => {
                        this.change_password_errors = null;
                        this.change_password = {};
                        notification('Change Password' , response.message , 'success');
                    }).catch((error) => {
                        this.change_password_errors = typeof error.response.data.errors !== 'undefined' ? error.response.data.errors : null;
                        notification('Change Password' , error.response.data.message , 'danger');
                    });
                hidebtnLoader();
            },
            listUser: function () {
                this.$router.push({
                    path: '/supervisors'
                });
            }
        }
    }

</script>
