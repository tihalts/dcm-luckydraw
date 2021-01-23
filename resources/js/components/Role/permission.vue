<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Permissions</h2>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="main-container">
                        <h3>Role Permissions</h3>
                        <hr />
                        <div class="row">
                            <div class="col-xs-12" v-for="(permissions, key) in role.permissions" :key="key">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 style="text-transform:capitalize;">{{ role.name }}</h5>
                                    </div>
                                    <div class="col-md-3 col-sm-6" v-for="permission in permissions"
                                        :key="permission.id">
                                        <div class="checkbox">
                                            <input type="checkbox" v-model="permission.is_allow" :id="permission.slug">
                                            <label :for="permission.slug"
                                                style="text-transform:capitalize;">{{permission.name}}</label>
                                        </div>
                                    </div>
                                </div>
                                <!--.row-->
                            </div>                           
                        </div>
                        <hr />
                        <div class="from-block">
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="listRoles()"
                                    class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                <button type="button" @click="update()"
                                    class="btn float-right btn-success mb-2 mr-3">Update</button>
                            </div>
                        </div>
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
                role: {},
            }
        },
        mounted: function () {
            this.getRolePermissions();
        },
        methods: {
            getRolePermissions: function () {
                axios.get('/admin/role/' + this.$route.params.role_id + '/permissions')
                    .then(r => r.data)
                    .then((response) => {
                        this.role = response.data;
                    });
            },
            update: function (page) {
                axios.post('/admin/role/' + this.$route.params.role_id + '/update/permissions', this.role)
                    .then(r => r.data)
                    .then((response) => {
                        this.role = response.data;
                        notification(response.title, response.message, 'success');
                    }).catch((error) => {
                        //notification('Upload Invoice' , "Upload Invoice Faild!" , 'danger');
                    });
            },
            listRoles: function () {
                this.$router.push({
                    path: '/admin/roles'
                });
            }
        }
    }

</script>
