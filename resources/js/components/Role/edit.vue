<template>
    <div class="container-fluid">
        <div class="page-content__header">
            <div>
                <h2 class="page-content__header-heading">Edit Role</h2>
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
                    <h3>Role Edit Form</h3>
                    <hr />
                    <form role="form" ref="editRoleFrom" name="editRoleFrom" v-on:submit.prevent="editRole">
                        <div class="from-block">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label" for="exampleName">Role Name</label>
                                        <input type="text" class="form-control" v-model="form.role_name"
                                            placeholder="Role name">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label" for="exampleDescription">Description</label>
                                        <input type="text" class="form-control" v-model="form.description"
                                            placeholder="Description">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label" for="exampleGroupName">Group Name</label>
                                        <input type="text" class="form-control" v-model="form.group_name"
                                            placeholder="Group Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label" for="exampleTypeName">Type Name</label>
                                        <input type="text" class="form-control" v-model="form.type_name"
                                            placeholder="Type name">
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <hr />
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="listRole()"
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
</template>
<style scoped>
    .text-red {
        color: red !important;
        padding-left: 10px;
    }

</style>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
            }
        },
        mounted: function () {
            this.getRole();
        },
        methods: {
            getRole: function () {
                axios.get('/role/fetch/' + this.$route.params.role_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editRole: function () {
                axios.post('/role/edit/' + this.$route.params.role_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listRole();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                //hidebtnLoader();
            },
            listRole: function () {
                this.$router.push({
                    path: '/roles'
                });
            }
        }
    }

</script>
